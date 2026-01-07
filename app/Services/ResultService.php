<?php

namespace App\Services;

use App\Models\Result;
use App\Models\MedicalDiagnosis;
use App\Repositories\Contracts\ResultRepositoryInterface;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Result Service
 * Handles business logic for creating and managing medical certificates (SKB and MC)
 */
class ResultService
{
    public function __construct(
        protected ResultRepositoryInterface $resultRepository,
        protected PatientRepositoryInterface $patientRepository,
        protected DocumentService $documentService
    ) {}

    /**
     * Create Surat Keterangan Berobat (SKB)
     *
     * @param array $data
     * @param int $outletId
     * @return Result
     * @throws \Throwable
     */
    public function createSKB(array $data, int $outletId): Result
    {
        DB::beginTransaction();
        try {
            // 1. Get or create patient
            $patient = $this->getOrCreatePatient($data, $outletId);

            // 2. Create diagnosis
            $diagnosis = $this->createDiagnosis($data, $patient->id);

            // 3. Create result
            $result = $this->createResult('skb', $data, $patient, $diagnosis, $outletId);

            // 4. Queue PDF generation
            $this->documentService->queuePDFGeneration($result, 'skb');

            DB::commit();

            \Log::info('SKB created successfully', [
                'result_id' => $result->id,
                'patient_id' => $patient->id,
                'outlet_id' => $outletId,
            ]);

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to create SKB', [
                'error' => $e->getMessage(),
                'data' => $data,
                'outlet_id' => $outletId,
            ]);
            throw $e;
        }
    }

    /**
     * Create Medical Certificate (MC)
     *
     * @param array $data
     * @param int $outletId
     * @return Result
     * @throws \Throwable
     */
    public function createMC(array $data, int $outletId): Result
    {
        DB::beginTransaction();
        try {
            // 1. Get or create patient
            $patient = $this->getOrCreatePatient($data, $outletId);

            // 2. Create diagnosis
            $diagnosis = $this->createDiagnosis($data, $patient->id);

            // 3. Create result
            $result = $this->createResult('mc', $data, $patient, $diagnosis, $outletId);

            // 4. Queue PDF generation
            $this->documentService->queuePDFGeneration($result, 'mc');

            DB::commit();

            \Log::info('MC created successfully', [
                'result_id' => $result->id,
                'patient_id' => $patient->id,
                'outlet_id' => $outletId,
            ]);

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to create MC', [
                'error' => $e->getMessage(),
                'data' => $data,
                'outlet_id' => $outletId,
            ]);
            throw $e;
        }
    }

    /**
     * Update existing result
     *
     * @param int $resultId
     * @param array $data
     * @return Result
     * @throws \Throwable
     */
    public function update(int $resultId, array $data): Result
    {
        DB::beginTransaction();
        try {
            $result = $this->resultRepository->findOrFail($resultId);

            // Update diagnosis if provided
            if (isset($data['icd_code']) || isset($data['diagnosis_name'])) {
                $this->updateDiagnosis($result, $data);
            }

            // Update result
            $resultData = $this->prepareResultData($result->type, $data);
            $result = $this->resultRepository->update($resultId, $resultData);

            // Regenerate PDF if needed
            if (isset($data['regenerate_pdf']) && $data['regenerate_pdf']) {
                $this->documentService->queuePDFGeneration($result, $result->type);
            }

            DB::commit();

            \Log::info('Result updated successfully', [
                'result_id' => $resultId,
            ]);

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to update result', [
                'result_id' => $resultId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete result
     *
     * @param int $resultId
     * @return bool
     * @throws \Throwable
     */
    public function delete(int $resultId): bool
    {
        try {
            $result = $this->resultRepository->findOrFail($resultId);

            \Log::info('Deleting result', [
                'result_id' => $resultId,
                'unique_code' => $result->unique_code,
            ]);

            return $this->resultRepository->delete($resultId);
        } catch (\Throwable $e) {
            \Log::error('Failed to delete result', [
                'result_id' => $resultId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get or create patient from data
     *
     * @param array $data
     * @param int $outletId
     * @return \App\Models\Patient
     */
    protected function getOrCreatePatient(array $data, int $outletId): \App\Models\Patient
    {
        // Use patient_id if provided and exists
        if (!empty($data['patient_id'])) {
            try {
                return $this->patientRepository->findOrFail($data['patient_id']);
            } catch (\Exception $e) {
                // Patient ID invalid, create new patient
            }
        }

        // Get or create patient using repository method
        return $this->patientRepository->getOrCreate($data, $outletId);
    }

    /**
     * Create diagnosis
     *
     * @param array $data
     * @param int $patientId
     * @return MedicalDiagnosis|null
     */
    protected function createDiagnosis(array $data, int $patientId): ?MedicalDiagnosis
    {
        if (empty($data['icd_code']) && empty($data['diagnosis_name'])) {
            return null;
        }

        $diagnosis = MedicalDiagnosis::create([
            'patient_id' => $patientId,
            'icd_code' => $data['icd_code'] ?? null,
            'diagnosis_name' => $data['diagnosis_name'] ?? null,
            'diagnosis_date' => $data['diagnosis_date'] ?? now(),
            'notes' => $data['diagnosis_notes'] ?? null,
        ]);

        return $diagnosis;
    }

    /**
     * Update diagnosis
     *
     * @param Result $result
     * @param array $data
     * @return void
     */
    protected function updateDiagnosis(Result $result, array $data): void
    {
        if ($result->medical_diagnosis_id) {
            $result->diagnosis->update([
                'icd_code' => $data['icd_code'] ?? $result->diagnosis->icd_code,
                'diagnosis_name' => $data['diagnosis_name'] ?? $result->diagnosis->diagnosis_name,
                'notes' => $data['diagnosis_notes'] ?? $result->diagnosis->notes,
            ]);
        } else {
            // Create new diagnosis if not exists
            $diagnosis = $this->createDiagnosis($data, $result->patient_id);
            if ($diagnosis) {
                $result->update(['medical_diagnosis_id' => $diagnosis->id]);
            }
        }
    }

    /**
     * Create result record
     *
     * @param string $type
     * @param array $data
     * @param \App\Models\Patient $patient
     * @param MedicalDiagnosis|null $diagnosis
     * @param int $outletId
     * @return Result
     */
    protected function createResult(
        string $type,
        array $data,
        \App\Models\Patient $patient,
        ?MedicalDiagnosis $diagnosis,
        int $outletId
    ): Result {
        $resultData = $this->prepareResultData($type, $data);

        $resultData['outlet_id'] = $outletId;
        $resultData['patient_id'] = $patient->id;
        $resultData['medical_diagnosis_id'] = $diagnosis?->id;
        $resultData['type'] = $type;
        $resultData['unique_code'] = $this->generateUniqueCode($type);
        $resultData['qrcode'] = $this->generateQRCode();

        return $this->resultRepository->create($resultData);
    }

    /**
     * Prepare result data based on type
     *
     * @param string $type
     * @param array $data
     * @return array
     */
    protected function prepareResultData(string $type, array $data): array
    {
        $resultData = [
            'doctor_id' => $data['doctor_id'],
            'company_id' => $data['company_id'] ?? null,
            'sign_type' => $data['sign_type'] ?? 'qrcode',
            'sign_value' => $data['sign_value'] ?? null,
            'verification_date' => $data['verification_date'] ?? now(),
        ];

        if ($type === 'skb') {
            $resultData['date'] = $data['date'] ?? now()->toDateString();
            $resultData['time'] = $data['time'] ?? now()->format('H:i');
        } else { // mc
            $resultData['start_date'] = $data['start_date'];
            $resultData['duration'] = $data['duration'];
            $resultData['end_date'] = $data['end_date'] ?? $this->calculateEndDate($data['start_date'], $data['duration']);
        }

        return $resultData;
    }

    /**
     * Calculate end date for MC
     *
     * @param string $startDate
     * @param int $duration
     * @return string
     */
    protected function calculateEndDate(string $startDate, int $duration): string
    {
        return \Carbon\Carbon::parse($startDate)->addDays($duration - 1)->toDateString();
    }

    /**
     * Generate unique code for result
     *
     * @param string $type
     * @return string
     */
    protected function generateUniqueCode(string $type): string
    {
        $prefix = strtoupper($type);
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(4));

        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * Generate QR code token
     *
     * @return string
     */
    protected function generateQRCode(): string
    {
        return Str::uuid()->toString();
    }
}
