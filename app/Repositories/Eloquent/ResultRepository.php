<?php

namespace App\Repositories\Eloquent;

use App\Models\Result;
use App\Repositories\Contracts\ResultRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Result Repository
 * Handles data access for medical certificates (SKB and MC)
 */
class ResultRepository extends BaseRepository implements ResultRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return Result::class;
    }

    /**
     * Get results by outlet ID with filters and pagination
     *
     * @param int $outletId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOutlet(int $outletId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['patient', 'doctor', 'diagnosis', 'outlet'])
            ->where('outlet_id', $outletId);

        // Apply type filter
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Apply date range filter
        if (isset($filters['start_date']) && !empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date']) && !empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('unique_code', 'like', "%{$search}%")
                    ->orWhere('no_letters', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        // Apply doctor filter
        if (isset($filters['doctor_id']) && !empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        // Apply company filter
        if (isset($filters['company_id']) && !empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get statistics for outlet dashboard
     *
     * @param int $outletId
     * @param array $dateRange
     * @return array
     */
    public function getStatistics(int $outletId, array $dateRange = []): array
    {
        $query = $this->model->newQuery()->where('outlet_id', $outletId);

        // Apply date range if provided
        if (!empty($dateRange['start'])) {
            $query->whereDate('created_at', '>=', $dateRange['start']);
        }
        if (!empty($dateRange['end'])) {
            $query->whereDate('created_at', '<=', $dateRange['end']);
        }

        $totalResults = $query->count();
        $totalSKB = (clone $query)->where('type', 'skb')->count();
        $totalMC = (clone $query)->where('type', 'mc')->count();

        $todayResults = (clone $query)->whereDate('created_at', today())->count();
        $thisMonthResults = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total_results' => $totalResults,
            'total_skb' => $totalSKB,
            'total_mc' => $totalMC,
            'today_results' => $todayResults,
            'this_month_results' => $thisMonthResults,
            'percentage_skb' => $totalResults > 0 ? round(($totalSKB / $totalResults) * 100, 2) : 0,
            'percentage_mc' => $totalResults > 0 ? round(($totalMC / $totalResults) * 100, 2) : 0,
        ];
    }

    /**
     * Get results by patient
     *
     * @param int $patientId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByPatient(int $patientId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['doctor', 'outlet', 'diagnosis'])
            ->where('patient_id', $patientId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get results by doctor
     *
     * @param int $doctorId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByDoctor(int $doctorId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['patient', 'outlet', 'diagnosis'])
            ->where('doctor_id', $doctorId);

        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get results by company
     *
     * @param int $companyId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCompany(int $companyId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['patient', 'doctor', 'outlet'])
            ->where('company_id', $companyId);

        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find result by unique code
     *
     * @param string $uniqueCode
     * @return Result|null
     */
    public function findByUniqueCode(string $uniqueCode): ?Result
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor', 'outlet', 'diagnosis'])
            ->where('unique_code', $uniqueCode)
            ->first();
    }

    /**
     * Find result by QR code
     *
     * @param string $qrcode
     * @return Result|null
     */
    public function findByQrCode(string $qrcode): ?Result
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor', 'outlet', 'diagnosis'])
            ->where('qrcode', $qrcode)
            ->first();
    }

    /**
     * Get results by type (SKB or MC)
     *
     * @param string $type
     * @param int $outletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, int $outletId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor', 'diagnosis'])
            ->where('outlet_id', $outletId)
            ->where('type', $type)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get results within date range
     *
     * @param int $outletId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(int $outletId, string $startDate, string $endDate): Collection
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor', 'diagnosis'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
    }

    /**
     * Get today's results
     *
     * @param int $outletId
     * @return Collection
     */
    public function getTodayResults(int $outletId): Collection
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor'])
            ->where('outlet_id', $outletId)
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    /**
     * Get this month's results
     *
     * @param int $outletId
     * @return Collection
     */
    public function getThisMonthResults(int $outletId): Collection
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor'])
            ->where('outlet_id', $outletId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()
            ->get();
    }

    /**
     * Count results by type
     *
     * @param int $outletId
     * @param string $type
     * @return int
     */
    public function countByType(int $outletId, string $type): int
    {
        return $this->model->newQuery()
            ->where('outlet_id', $outletId)
            ->where('type', $type)
            ->count();
    }

    /**
     * Search results
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['patient', 'doctor', 'diagnosis'])
            ->where('outlet_id', $outletId)
            ->where(function ($query) use ($keyword) {
                $query->where('unique_code', 'like', "%{$keyword}%")
                    ->orWhere('no_letters', 'like', "%{$keyword}%")
                    ->orWhereHas('patient', function ($q) use ($keyword) {
                        $q->where('full_name', 'like', "%{$keyword}%")
                            ->orWhere('nik', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('doctor', function ($q) use ($keyword) {
                        $q->whereHas('user', function ($uq) use ($keyword) {
                            $uq->where('name', 'like', "%{$keyword}%");
                        });
                    });
            })
            ->latest()
            ->paginate($perPage);
    }
}
