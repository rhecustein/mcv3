<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\CertificateCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Get current tenant ID from authenticated user
     */
    private function getTenantId(): string
    {
        return Auth::user()->tenant_id;
    }

    /**
     * Get list of certificates for current tenant
     *
     * @OA\Get(
     *     path="/api/v1/certificates",
     *     summary="Get certificates list",
     *     tags={"Certificates"},
     *     @OA\Parameter(name="search", in="query", description="Search by code, name, or NIK"),
     *     @OA\Parameter(name="page", in="query", description="Page number"),
     *     @OA\Parameter(name="per_page", in="query", description="Results per page (default: 20, max: 100)"),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $tenantId = $this->getTenantId();
        $perPage = min($request->get('per_page', 20), 100);

        // Base query
        $query = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unique_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Order by latest
        $query->orderByDesc('pdf_generated_at');

        // Paginate
        $certificates = $query->paginate($perPage);

        return response()->json(new CertificateCollection($certificates));
    }

    /**
     * Get certificate detail
     *
     * @OA\Get(
     *     path="/api/v1/certificates/{id}",
     *     summary="Get certificate detail",
     *     tags={"Certificates"},
     *     @OA\Parameter(name="id", in="path", required=true, description="Certificate ID"),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Certificate not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $tenantId = $this->getTenantId();

        $certificate = Result::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->whereNotNull('pdf_path')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new CertificateResource($certificate),
        ]);
    }

    /**
     * Download certificate PDF
     *
     * @OA\Get(
     *     path="/api/v1/certificates/{id}/download",
     *     summary="Download certificate PDF",
     *     tags={"Certificates"},
     *     @OA\Parameter(name="id", in="path", required=true, description="Certificate ID"),
     *     @OA\Response(response=200, description="PDF file"),
     *     @OA\Response(response=404, description="Certificate not found")
     * )
     */
    public function download(string $id)
    {
        $tenantId = $this->getTenantId();

        $certificate = Result::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->whereNotNull('pdf_path')
            ->firstOrFail();

        if (!Storage::disk('public')->exists($certificate->pdf_path)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF file not found on server',
            ], 404);
        }

        $filename = "{$certificate->unique_code}.pdf";

        return Storage::disk('public')->download($certificate->pdf_path, $filename);
    }

    /**
     * Get PDF URL (for preview/streaming)
     *
     * @OA\Get(
     *     path="/api/v1/certificates/{id}/pdf-url",
     *     summary="Get PDF URL for preview",
     *     tags={"Certificates"},
     *     @OA\Parameter(name="id", in="path", required=true, description="Certificate ID"),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function pdfUrl(string $id): JsonResponse
    {
        $tenantId = $this->getTenantId();

        $certificate = Result::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->whereNotNull('pdf_path')
            ->firstOrFail();

        if (!Storage::disk('public')->exists($certificate->pdf_path)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF file not found',
            ], 404);
        }

        $url = Storage::disk('public')->url($certificate->pdf_path);

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $url,
                'filename' => "{$certificate->unique_code}.pdf",
                'size' => $certificate->pdf_size_bytes,
                'size_formatted' => formatBytes($certificate->pdf_size_bytes),
            ],
        ]);
    }

    /**
     * Get storage info for current tenant
     *
     * @OA\Get(
     *     path="/api/v1/certificates/storage/info",
     *     summary="Get storage information",
     *     tags={"Certificates"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function storageInfo(): JsonResponse
    {
        $tenantId = $this->getTenantId();

        $totalCertificates = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->count();

        $totalSize = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_size_bytes')
            ->sum('pdf_size_bytes');

        $compressedCount = Result::where('tenant_id', $tenantId)
            ->where('pdf_compressed', true)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_certificates' => $totalCertificates,
                'total_size' => $totalSize,
                'total_size_formatted' => formatBytes($totalSize),
                'compressed_count' => $compressedCount,
                'compression_ratio' => $totalCertificates > 0
                    ? round(($compressedCount / $totalCertificates) * 100, 1)
                    : 0,
            ],
        ]);
    }

    /**
     * Search certificates
     *
     * @OA\Get(
     *     path="/api/v1/certificates/search",
     *     summary="Search certificates",
     *     tags={"Certificates"},
     *     @OA\Parameter(name="q", in="query", required=true, description="Search query"),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $tenantId = $this->getTenantId();
        $query = $request->q;

        $certificates = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('unique_code', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('nik', 'like', "%{$query}%")
                  ->orWhere('company', 'like', "%{$query}%");
            })
            ->orderByDesc('pdf_generated_at')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => CertificateResource::collection($certificates),
            'meta' => [
                'total' => $certificates->count(),
                'query' => $query,
            ],
        ]);
    }
}
