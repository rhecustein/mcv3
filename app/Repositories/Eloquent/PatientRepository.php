<?php

namespace App\Repositories\Eloquent;

use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Patient Repository
 * Handles data access for patient records
 */
class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function model(): string
    {
        return Patient::class;
    }

    /**
     * Get patients by outlet ID with filters and pagination
     *
     * @param int $outletId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOutlet(int $outletId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['user', 'company', 'outlet'])
            ->where('outlet_id', $outletId);

        // Apply gender filter
        if (isset($filters['gender']) && !empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Apply company filter
        if (isset($filters['company_id']) && !empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get patients by company ID
     *
     * @param int $companyId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCompany(int $companyId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet', 'results'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find patient by NIK
     *
     * @param string $nik
     * @return Patient|null
     */
    public function findByNik(string $nik): ?Patient
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet', 'company'])
            ->where('nik', $nik)
            ->first();
    }

    /**
     * Find patient by phone number
     *
     * @param string $phone
     * @return Patient|null
     */
    public function findByPhone(string $phone): ?Patient
    {
        return $this->model->newQuery()
            ->with(['user', 'outlet', 'company'])
            ->where('phone', $phone)
            ->first();
    }

    /**
     * Search patients by keyword (name, NIK, phone)
     *
     * @param int $outletId
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(int $outletId, string $keyword, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user', 'company'])
            ->where('outlet_id', $outletId)
            ->where(function ($query) use ($keyword) {
                $query->where('full_name', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('identity', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get or create patient
     * Used during result creation when patient might not exist
     *
     * @param array $data
     * @param int $outletId
     * @return Patient
     */
    public function getOrCreate(array $data, int $outletId): Patient
    {
        // Try to find existing patient by NIK first
        if (!empty($data['nik'])) {
            $patient = $this->findByNik($data['nik']);
            if ($patient) {
                return $patient;
            }
        }

        // Try to find by phone
        if (!empty($data['phone'])) {
            $patient = $this->findByPhone($data['phone']);
            if ($patient) {
                return $patient;
            }
        }

        // Try to find by name in the same outlet (less reliable)
        if (!empty($data['full_name'])) {
            $patient = $this->model->newQuery()
                ->where('outlet_id', $outletId)
                ->where('full_name', $data['full_name'])
                ->first();

            if ($patient) {
                return $patient;
            }
        }

        // Create new patient if not found
        $patientData = [
            'outlet_id' => $outletId,
            'full_name' => $data['full_name'] ?? $data['patient_name'] ?? null,
            'nik' => $data['nik'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'address' => $data['address'] ?? null,
            'company_id' => $data['company_id'] ?? null,
        ];

        return $this->create(array_filter($patientData));
    }

    /**
     * Get patient statistics for outlet
     *
     * @param int $outletId
     * @return array
     */
    public function getStatistics(int $outletId): array
    {
        $query = $this->model->newQuery()->where('outlet_id', $outletId);

        $totalPatients = $query->count();
        $totalMale = (clone $query)->where('gender', 'L')->count();
        $totalFemale = (clone $query)->where('gender', 'P')->count();
        $totalCompanies = (clone $query)->whereNotNull('company_id')->distinct('company_id')->count();

        $todayRegistrations = (clone $query)->whereDate('created_at', today())->count();
        $thisMonthRegistrations = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total_patients' => $totalPatients,
            'total_male' => $totalMale,
            'total_female' => $totalFemale,
            'total_companies' => $totalCompanies,
            'today_registrations' => $todayRegistrations,
            'this_month_registrations' => $thisMonthRegistrations,
            'percentage_male' => $totalPatients > 0 ? round(($totalMale / $totalPatients) * 100, 2) : 0,
            'percentage_female' => $totalPatients > 0 ? round(($totalFemale / $totalPatients) * 100, 2) : 0,
        ];
    }

    /**
     * Get patients by gender
     *
     * @param int $outletId
     * @param string $gender
     * @return Collection
     */
    public function getByGender(int $outletId, string $gender): Collection
    {
        return $this->model->newQuery()
            ->where('outlet_id', $outletId)
            ->where('gender', $gender)
            ->latest()
            ->get();
    }

    /**
     * Get patients registered today
     *
     * @param int $outletId
     * @return Collection
     */
    public function getRegisteredToday(int $outletId): Collection
    {
        return $this->model->newQuery()
            ->with(['company'])
            ->where('outlet_id', $outletId)
            ->whereDate('created_at', today())
            ->latest()
            ->get();
    }

    /**
     * Count patients by outlet
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
     * Get recent patients
     *
     * @param int $outletId
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $outletId, int $limit = 10): Collection
    {
        return $this->model->newQuery()
            ->with(['company'])
            ->where('outlet_id', $outletId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
