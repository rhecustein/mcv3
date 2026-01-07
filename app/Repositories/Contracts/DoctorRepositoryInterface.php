<?php

namespace App\Repositories\Contracts;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Doctor Repository Interface
 * Handles data access for doctor records
 */
interface DoctorRepositoryInterface extends RepositoryInterface
{
    /**
     * Get doctors by outlet ID
     *
     * @param int $outletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOutlet(int $outletId, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get doctors by admin ID
     *
     * @param int $adminId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByAdmin(int $adminId, int $perPage = 20): LengthAwarePaginator;

    /**
     * Find doctor by license number
     *
     * @param string $licenseNumber
     * @return Doctor|null
     */
    public function findByLicenseNumber(string $licenseNumber): ?Doctor;

    /**
     * Get doctors by specialization
     *
     * @param int $outletId
     * @param string $specialization
     * @return Collection
     */
    public function getBySpecialization(int $outletId, string $specialization): Collection;

    /**
     * Get verified doctors only
     *
     * @param int $outletId
     * @return Collection
     */
    public function getVerified(int $outletId): Collection;

    /**
     * Get active doctors for outlet
     *
     * @param int $outletId
     * @return Collection
     */
    public function getActive(int $outletId): Collection;

    /**
     * Search doctors by keyword
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20): LengthAwarePaginator;

    /**
     * Count doctors by outlet
     *
     * @param int $outletId
     * @return int
     */
    public function countByOutlet(int $outletId): int;

    /**
     * Get doctor statistics
     *
     * @param int $outletId
     * @return array
     */
    public function getStatistics(int $outletId): array;
}
