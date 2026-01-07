<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PatientSearchRequest;
use App\Http\Resources\Api\V1\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PatientController extends Controller
{
    /**
     * Search patients
     *
     * @param PatientSearchRequest $request
     * @return AnonymousResourceCollection
     */
    public function search(PatientSearchRequest $request): AnonymousResourceCollection
    {
        $query = $request->validated('q');
        $perPage = $request->validated('per_page', 10);

        $patients = Patient::query()
            ->when(app()->bound('tenant'), function ($q) {
                // Tenant scoping - only show patients for current tenant
                $q->where('tenant_id', app('tenant')->id);
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->paginate($perPage);

        return PatientResource::collection($patients);
    }

    /**
     * Display the specified patient
     *
     * @param Patient $patient
     * @return PatientResource
     */
    public function show(Patient $patient): PatientResource
    {
        // Authorization check
        $this->authorize('view', $patient);

        return new PatientResource($patient);
    }
}
