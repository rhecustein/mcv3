<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ResultService;
use App\Services\PatientService;
use App\Services\DocumentService;
use App\Repositories\Contracts\ResultRepositoryInterface;
use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Http\Requests\Result\StoreResultRequest;
use App\Helpers\OutletHelper;
use App\Models\Company;
use App\Models\Result;

/**
 * Refactored Healthletter Controller
 * Uses Repository Pattern and Service Layer
 */
class HealthletterControllerRefactored extends Controller
{
    public function __construct(
        protected ResultService $resultService,
        protected PatientService $patientService,
        protected DocumentService $documentService,
        protected ResultRepositoryInterface $resultRepository,
        protected PatientRepositoryInterface $patientRepository,
        protected DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Display main healthletter management page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $outlet = OutletHelper::getCurrentOutletOrFail('Akun Anda tidak terasosiasi dengan outlet manapun.');

        // Get statistics using repository
        $stats = $this->resultRepository->getStatistics($outlet->id, [
            'start' => $request->from,
            'end' => $request->to,
        ]);

        // Get results with filters using repository
        $results = $this->resultRepository->getByOutlet(
            outletId: $outlet->id,
            filters: [
                'search' => $request->keyword,
                'start_date' => $request->from,
                'end_date' => $request->to,
                'type' => $request->type,
                'doctor_id' => $request->doctor_id,
            ],
            perPage: 25
        );

        // Get doctors for filter dropdown
        $doctors = $this->doctorRepository->getByOutlet($outlet->id, perPage: 999)->items();

        return view('outlets.healthletters.index', [
            'totalLettersAllTime' => $stats['total_results'],
            'totalLettersThisMonth' => $stats['this_month_results'],
            'totalSKBThisMonth' => $stats['total_skb'],
            'totalMCThisMonth' => $stats['total_mc'],
            'results' => $results,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Show form to create Surat Keterangan Berobat (SKB)
     *
     * @return \Illuminate\View\View
     */
    public function createSuratSehat()
    {
        $outlet = OutletHelper::getCurrentOutletOrFail();

        return view('outlets.results.skbcreate', [
            'type' => 'skb',
            'title' => 'Buat Surat Keterangan Berobat (SKB)',
            'outlet' => $outlet,
            'companies' => Company::orderBy('name')->get(),
            'doctors' => $this->doctorRepository->getActive($outlet->id),
            'todayDate' => now()->format('Y-m-d'),
            'nowTime' => now()->format('H:i'),
        ]);
    }

    /**
     * Show form to create Surat Keterangan Sakit (MC)
     *
     * @return \Illuminate\View\View
     */
    public function createSuratSakit()
    {
        $outlet = OutletHelper::getCurrentOutletOrFail();

        return view('outlets.results.mccreate', [
            'type' => 'mc',
            'title' => 'Input Surat Sakit (MC)',
            'outlet' => $outlet,
            'companies' => Company::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'doctors' => $this->doctorRepository->getActive($outlet->id),
            'todayDate' => now()->format('Y-m-d'),
            'nowTime' => now()->format('H:i'),
        ]);
    }

    /**
     * Store Surat Keterangan Berobat (SKB)
     *
     * @param StoreResultRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSuratSehat(StoreResultRequest $request)
    {
        try {
            $outlet = OutletHelper::getCurrentOutletOrFail();

            // Use ResultService to create SKB
            $result = $this->resultService->createSKB(
                data: $request->validated(),
                outletId: $outlet->id
            );

            return redirect()->route('outlet.healthletter.index')
                ->with('success', 'Surat Keterangan Berobat berhasil dibuat.');
        } catch (\Throwable $e) {
            \Log::error('Failed to create SKB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Gagal membuat surat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Store Surat Keterangan Sakit (MC)
     *
     * @param StoreResultRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSuratSakit(StoreResultRequest $request)
    {
        try {
            $outlet = OutletHelper::getCurrentOutletOrFail();

            // Use ResultService to create MC
            $result = $this->resultService->createMC(
                data: $request->validated(),
                outletId: $outlet->id
            );

            return redirect()->route('outlet.healthletter.index')
                ->with('success', 'Surat Keterangan Sakit berhasil dibuat.');
        } catch (\Throwable $e) {
            \Log::error('Failed to create MC', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Gagal membuat surat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show edit form for SKB
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editSuratSehat(int $id)
    {
        $result = $this->resultRepository->findOrFail($id);
        $this->authorize('update', $result);

        $outlet = OutletHelper::getCurrentOutletOrFail();

        return view('outlets.results.skbedit', [
            'result' => $result,
            'outlet' => $outlet,
            'companies' => Company::orderBy('name')->get(),
            'doctors' => $this->doctorRepository->getActive($outlet->id),
        ]);
    }

    /**
     * Show edit form for MC
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editSuratSakit(int $id)
    {
        $result = $this->resultRepository->findOrFail($id);
        $this->authorize('update', $result);

        $outlet = OutletHelper::getCurrentOutletOrFail();

        return view('outlets.results.mcedit', [
            'result' => $result,
            'outlet' => $outlet,
            'companies' => Company::where('is_active', true)->orderBy('name')->get(),
            'doctors' => $this->doctorRepository->getActive($outlet->id),
        ]);
    }

    /**
     * Update SKB
     *
     * @param StoreResultRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSuratSehat(StoreResultRequest $request, int $id)
    {
        try {
            $result = $this->resultRepository->findOrFail($id);
            $this->authorize('update', $result);

            // Use ResultService to update
            $result = $this->resultService->update($id, $request->validated());

            return redirect()->route('outlet.healthletter.index')
                ->with('success', 'Surat Keterangan Berobat berhasil diperbarui.');
        } catch (\Throwable $e) {
            \Log::error('Failed to update SKB', [
                'result_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'Gagal memperbarui surat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update MC
     *
     * @param StoreResultRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSuratSakit(StoreResultRequest $request, int $id)
    {
        try {
            $result = $this->resultRepository->findOrFail($id);
            $this->authorize('update', $result);

            // Use ResultService to update
            $result = $this->resultService->update($id, $request->validated());

            return redirect()->route('outlet.healthletter.index')
                ->with('success', 'Surat Keterangan Sakit berhasil diperbarui.');
        } catch (\Throwable $e) {
            \Log::error('Failed to update MC', [
                'result_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'Gagal memperbarui surat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete result (soft delete)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int $id)
    {
        try {
            $result = $this->resultRepository->findOrFail($id);
            $this->authorize('delete', $result);

            // Use ResultService to delete
            $this->resultService->delete($id);

            return back()->with('success', 'Surat berhasil dihapus.');
        } catch (\Throwable $e) {
            \Log::error('Failed to delete result', [
                'result_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }

    /**
     * Show result detail
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $result = $this->resultRepository->findOrFail($id);
        $this->authorize('view', $result);

        return view('outlets.results.show', [
            'result' => $result,
        ]);
    }

    /**
     * Download PDF for result
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadPDF(int $id)
    {
        $result = $this->resultRepository->findOrFail($id);
        $this->authorize('download', $result);

        if (empty($result->document_name)) {
            return back()->with('error', 'PDF belum tersedia. Silakan generate ulang.');
        }

        $filePath = storage_path('app/public/pdfs/' . $result->document_name);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->download($filePath);
    }

    /**
     * Regenerate PDF for result
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regeneratePDF(int $id)
    {
        try {
            $result = $this->resultRepository->findOrFail($id);
            $this->authorize('update', $result);

            // Use DocumentService to regenerate PDF
            $this->documentService->regeneratePDF($result);

            return back()->with('success', 'PDF sedang di-generate ulang. Refresh halaman dalam beberapa saat.');
        } catch (\Throwable $e) {
            \Log::error('Failed to regenerate PDF', [
                'result_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal generate ulang PDF: ' . $e->getMessage());
        }
    }

    /**
     * Search patients for autocomplete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPatients(Request $request)
    {
        $outlet = OutletHelper::getCurrentOutletOrFail();

        $patients = $this->patientRepository->search(
            outletId: $outlet->id,
            keyword: $request->keyword ?? '',
            perPage: 10
        );

        return response()->json($patients->items());
    }
}
