<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\User;
use App\Helpers\PasswordHelper;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Doctor Service
 * Handles business logic for doctor management
 */
class DoctorService
{
    public function __construct(
        protected DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Create new doctor with user account
     *
     * @param array $data
     * @param int $outletId
     * @param int $adminId
     * @return Doctor
     * @throws \Throwable
     */
    public function create(array $data, int $outletId, int $adminId): Doctor
    {
        DB::beginTransaction();
        try {
            // Generate secure password for user account
            $passwordData = PasswordHelper::generateForNewUser();

            // Create user account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $passwordData['password'],
                'role_type' => 'doctor',
                'must_change_password' => $passwordData['must_change_password'],
            ]);

            // Create doctor record
            $doctorData = [
                'user_id' => $user->id,
                'outlet_id' => $outletId,
                'admin_id' => $adminId,
                'license_number' => $data['license_number'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'specialist' => $data['specialist'] ?? null,
                'practice_days' => $data['practice_days'] ?? null,
                'education' => $data['education'] ?? null,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
            ];

            $doctor = $this->doctorRepository->create(array_filter($doctorData));

            DB::commit();

            \Log::info('Doctor created successfully', [
                'doctor_id' => $doctor->id,
                'user_id' => $user->id,
                'outlet_id' => $outletId,
            ]);

            return $doctor;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to create doctor', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update doctor
     *
     * @param int $doctorId
     * @param array $data
     * @return Doctor
     * @throws \Throwable
     */
    public function update(int $doctorId, array $data): Doctor
    {
        DB::beginTransaction();
        try {
            // Update doctor
            $doctor = $this->doctorRepository->update($doctorId, [
                'license_number' => $data['license_number'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'specialist' => $data['specialist'] ?? null,
                'practice_days' => $data['practice_days'] ?? null,
                'education' => $data['education'] ?? null,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'outlet_id' => $data['outlet_id'] ?? $doctor->outlet_id,
            ]);

            // Update user if name or email changed
            if ($doctor->user) {
                $userUpdates = [];
                if (isset($data['name']) && $data['name'] !== $doctor->user->name) {
                    $userUpdates['name'] = $data['name'];
                }
                if (isset($data['email']) && $data['email'] !== $doctor->user->email) {
                    $userUpdates['email'] = $data['email'];
                }

                if (!empty($userUpdates)) {
                    $doctor->user->update($userUpdates);
                }
            }

            DB::commit();

            \Log::info('Doctor updated successfully', [
                'doctor_id' => $doctorId,
            ]);

            return $doctor->fresh(['user', 'outlet']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to update doctor', [
                'doctor_id' => $doctorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete doctor
     *
     * @param int $doctorId
     * @return bool
     * @throws \Throwable
     */
    public function delete(int $doctorId): bool
    {
        DB::beginTransaction();
        try {
            $doctor = $this->doctorRepository->findOrFail($doctorId);

            // Delete associated user account if exists
            if ($doctor->user) {
                $doctor->user->delete();
            }

            // Delete doctor
            $result = $this->doctorRepository->delete($doctorId);

            DB::commit();

            \Log::info('Doctor deleted successfully', [
                'doctor_id' => $doctorId,
            ]);

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to delete doctor', [
                'doctor_id' => $doctorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify doctor's signature
     *
     * @param int $doctorId
     * @param string $signatureImage
     * @return Doctor
     * @throws \Throwable
     */
    public function verifySignature(int $doctorId, string $signatureImage): Doctor
    {
        DB::beginTransaction();
        try {
            $doctor = $this->doctorRepository->findOrFail($doctorId);

            $doctor->update([
                'signature_image' => $signatureImage,
                'signature_token' => \Str::uuid()->toString(),
                'signature_qrcode' => \Str::random(32),
                'signed_at' => now(),
                'is_signature_verified' => true,
            ]);

            DB::commit();

            \Log::info('Doctor signature verified', [
                'doctor_id' => $doctorId,
            ]);

            return $doctor->fresh();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to verify signature', [
                'doctor_id' => $doctorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate signature token for QR code
     *
     * @param int $doctorId
     * @return string
     */
    public function generateSignatureToken(int $doctorId): string
    {
        $doctor = $this->doctorRepository->findOrFail($doctorId);

        $token = \Str::uuid()->toString();

        $doctor->update([
            'signature_token' => $token,
        ]);

        \Log::info('Signature token generated', [
            'doctor_id' => $doctorId,
        ]);

        return $token;
    }

    /**
     * Get doctor statistics
     *
     * @param int $outletId
     * @return array
     */
    public function getStatistics(int $outletId): array
    {
        return $this->doctorRepository->getStatistics($outletId);
    }
}
