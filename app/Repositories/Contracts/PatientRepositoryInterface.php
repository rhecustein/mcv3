<?php

namespace App\Repositories\Contracts;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Patient Repository Interface
 * Handles data access for patient records
 */
interface PatientRepositoryInterface extends RepositoryInterface
{
    /**
     * Get patients by outlet ID
     *
     * @param int $outletId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOutlet(int $outletId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Get patients by company ID
     *
     * @param int $companyId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCompany(int $companyId, int $perPage = 20): LengthAwarePaginator;

    /**
     * Find patient by NIK
     *
     * @param string $nik
     * @return Patient|null
     */
    public function findByNik(string $nik): ?Patient;

    /**
     * Find patient by phone number
     *
     * @param string $phone
     * @return Patient|null
     */
    public function findByPhone(string $phone): ?Patient;

    /**
     * Search patients by keyword (name, NIK, phone)
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get or create patient
     * Used during result creation when patient might not exist
     *
     * @param array $data
     * @param int $outletId
     * @return Patient
     */
    public function getOrCreate(array $data, int $outletId): Patient;

    /**
     * Get patient statistics for outlet
     *
     * @param int $outletId
     * @return array
     */
    public function getStatistics(int $outletId): array;

    /**
     * Get patients by gender
     *
     * @param int $outletId
     * @param string $gender
     * @return Collection
     */
    public function getByGender(int $outletId, string $gender): Collection;

    /**
     * Get patients registered today
     *
     * @param int $outletId
     * @return Collection
     */
    public function getRegisteredToday(int $outletId): Collection;

    /**
     * Count patients by outlet
     *
     * @param int $outletId
     * @return int
     */
    public function countByOutlet(int $outletId): int;

    /**
     * Get recent patients
     *
     * @param int $outletId
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $outletId, int $limit = 10): Collection;
}
