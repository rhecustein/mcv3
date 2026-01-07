<?php

namespace App\Repositories\Contracts;

use App\Models\Result;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Result Repository Interface
 * Handles data access for medical certificates (SKB and MC)
 */
interface ResultRepositoryInterface extends RepositoryInterface
{
    /**
     * Get results by outlet ID
     *
     * @param int $outletId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOutlet(int $outletId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Get statistics for outlet dashboard
     *
     * @param int $outletId
     * @param array $dateRange
     * @return array
     */
    public function getStatistics(int $outletId, array $dateRange = []): array;

    /**
     * Get results by patient
     *
     * @param int $patientId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByPatient(int $patientId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get results by doctor
     *
     * @param int $doctorId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByDoctor(int $doctorId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Get results by company
     *
     * @param int $companyId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCompany(int $companyId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Find result by unique code
     *
     * @param string $uniqueCode
     * @return Result|null
     */
    public function findByUniqueCode(string $uniqueCode): ?Result;

    /**
     * Find result by QR code
     *
     * @param string $qrcode
     * @return Result|null
     */
    public function findByQrCode(string $qrcode): ?Result;

    /**
     * Get results by type (SKB or MC)
     *
     * @param string $type
     * @param int $outletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, int $outletId, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get results within date range
     *
     * @param int $outletId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(int $outletId, string $startDate, string $endDate): Collection;

    /**
     * Get today's results
     *
     * @param int $outletId
     * @return Collection
     */
    public function getTodayResults(int $outletId): Collection;

    /**
     * Get this month's results
     *
     * @param int $outletId
     * @return Collection
     */
    public function getThisMonthResults(int $outletId): Collection;

    /**
     * Count results by type
     *
     * @param int $outletId
     * @param string $type
     * @return int
     */
    public function countByType(int $outletId, string $type): int;

    /**
     * Search results
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20): LengthAwarePaginator;
}
