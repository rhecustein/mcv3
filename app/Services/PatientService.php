<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use App\Helpers\PasswordHelper;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Patient Service
 * Handles business logic for patient management
 */
class PatientService
{
    public function __construct(
        protected PatientRepositoryInterface $patientRepository
    ) {}

    /**
     * Create new patient with user account
     *
     * @param array $data
     * @param int $outletId
     * @return Patient
     * @throws \Throwable
     */
    public function create(array $data, int $outletId): Patient
    {
        DB::beginTransaction();
        try {
            // Generate secure password for user account
            $passwordData = PasswordHelper::generateForNewUser();

            // Create user account
            $user = User::create([
                'name' => $data['full_name'],
                'email' => 'auto_' . uniqid() . '@mail.local',
                'password' => $passwordData['password'],
                'role_type' => 'patient',
                'must_change_password' => $passwordData['must_change_password'],
            ]);

            // Create patient record
            $patientData = [
                'user_id' => $user->id,
                'outlet_id' => $outletId,
                'full_name' => $data['full_name'],
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'phone' => $data['phone'] ?? null,
                'nik' => $data['nik'] ?? null,
                'identity' => $data['identity'] ?? null,
                'address' => $data['address'] ?? null,
                'company_id' => $data['company_id'] ?? null,
            ];

            $patient = $this->patientRepository->create(array_filter($patientData));

            DB::commit();

            \Log::info('Patient created successfully', [
                'patient_id' => $patient->id,
                'user_id' => $user->id,
                'outlet_id' => $outletId,
            ]);

            return $patient;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to create patient', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update patient
     *
     * @param int $patientId
     * @param array $data
     * @return Patient
     * @throws \Throwable
     */
    public function update(int $patientId, array $data): Patient
    {
        DB::beginTransaction();
        try {
            // Update patient
            $patient = $this->patientRepository->update($patientId, [
                'full_name' => $data['full_name'],
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'phone' => $data['phone'] ?? null,
                'nik' => $data['nik'] ?? null,
                'identity' => $data['identity'] ?? null,
                'address' => $data['address'] ?? null,
                'outlet_id' => $data['outlet_id'],
                'company_id' => $data['company_id'] ?? null,
            ]);

            // Update user name if user exists
            if ($patient->user) {
                $patient->user->update([
                    'name' => $data['full_name'],
                ]);
            }

            DB::commit();

            \Log::info('Patient updated successfully', [
                'patient_id' => $patientId,
            ]);

            return $patient->fresh(['user', 'company', 'outlet']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to update patient', [
                'patient_id' => $patientId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete patient
     *
     * @param int $patientId
     * @return bool
     * @throws \Throwable
     */
    public function delete(int $patientId): bool
    {
        DB::beginTransaction();
        try {
            $patient = $this->patientRepository->findOrFail($patientId);

            // Delete associated user account if exists
            if ($patient->user) {
                $patient->user->delete();
            }

            // Delete patient
            $result = $this->patientRepository->delete($patientId);

            DB::commit();

            \Log::info('Patient deleted successfully', [
                'patient_id' => $patientId,
            ]);

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to delete patient', [
                'patient_id' => $patientId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get or create patient (used during result creation)
     *
     * @param array $data
     * @param int $outletId
     * @return Patient
     */
    public function getOrCreate(array $data, int $outletId): Patient
    {
        return $this->patientRepository->getOrCreate($data, $outletId);
    }

    /**
     * Search patients
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20)
    {
        return $this->patientRepository->search($outletId, $keyword, $perPage);
    }

    /**
     * Get patient statistics
     *
     * @param int $outletId
     * @return array
     */
    public function getStatistics(int $outletId): array
    {
        return $this->patientRepository->getStatistics($outletId);
    }
}
