<?php

namespace App\Repositories\Eloquent;

use App\Models\Doctor;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Doctor Repository
 * Handles data access for doctor records
 */
class DoctorRepository extends BaseRepository implements DoctorRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return Doctor::class;
    }

    /**
     * Get doctors by outlet ID
     *
     * @param int $outletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOutlet(int $outletId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet'])
            ->where('outlet_id', $outletId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get doctors by admin ID
     *
     * @param int $adminId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByAdmin(int $adminId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet'])
            ->where('admin_id', $adminId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find doctor by license number
     *
     * @param string $licenseNumber
     * @return Doctor|null
     */
    public function findByLicenseNumber(string $licenseNumber): ?Doctor
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet'])
            ->where('license_number', $licenseNumber)
            ->first();
    }

    /**
     * Get doctors by specialization
     *
     * @param int $outletId
     * @param string $specialization
     * @return Collection
     */
    public function getBySpecialization(int $outletId, string $specialization): Collection
    {
        return $this->model->newQuery()
            ->with(['user'])
            ->where('outlet_id', $outletId)
            ->where('specialization', $specialization)
            ->get();
    }

    /**
     * Get verified doctors only
     *
     * @param int $outletId
     * @return Collection
     */
    public function getVerified(int $outletId): Collection
    {
        return $this->model->newQuery()
            ->with(['user'])
            ->where('outlet_id', $outletId)
            ->where('is_signature_verified', true)
            ->get();
    }

    /**
     * Get active doctors for outlet
     *
     * @param int $outletId
     * @return Collection
     */
    public function getActive(int $outletId): Collection
    {
        return $this->model->newQuery()
            ->with(['user'])
            ->where('outlet_id', $outletId)
            ->whereHas('user', function ($query) {
                // Assuming active users have specific criteria
                // Adjust based on your User model implementation
            })
            ->get();
    }

    /**
     * Search doctors by keyword
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet'])
            ->where('outlet_id', $outletId)
            ->where(function ($query) use ($keyword) {
                $query->where('license_number', 'like', "%{$keyword}%")
                    ->orWhere('specialization', 'like', "%{$keyword}%")
                    ->orWhere('specialist', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Count doctors by outlet
     *
     * @param int $outletId
     * @return int
     */
    public function countByOutlet(int $outletId): int
    {
        return $this->model->newQuery()
            ->where('outlet_id', $outletId)
            ->count();
    }

    /**
     * Get doctor statistics
     *
     * @param int $outletId
     * @return array
     */
    public function getStatistics(int $outletId): array
    {
        $query = $this->model->newQuery()->where('outlet_id', $outletId);

        $totalDoctors = $query->count();
        $verifiedDoctors = (clone $query)->where('is_signature_verified', true)->count();
        $maleDoctors = (clone $query)->where('gender', 'male')->count();
        $femaleDoctors = (clone $query)->where('gender', 'female')->count();

        // Get specialization distribution
        $specializations = (clone $query)
            ->select('specialization')
            ->selectRaw('count(*) as count')
            ->whereNotNull('specialization')
            ->groupBy('specialization')
            ->get()
            ->pluck('count', 'specialization')
            ->toArray();

        return [
            'total_doctors' => $totalDoctors,
            'verified_doctors' => $verifiedDoctors,
            'unverified_doctors' => $totalDoctors - $verifiedDoctors,
            'male_doctors' => $maleDoctors,
            'female_doctors' => $femaleDoctors,
            'specializations' => $specializations,
            'percentage_verified' => $totalDoctors > 0 ? round(($verifiedDoctors / $totalDoctors) * 100, 2) : 0,
        ];
    }
}
