@extends('layouts.admin')

@section('title', 'PDF Archives Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">🗄️ PDF Archives Management</h1>
                <a href="{{ route('superadmin.pdf-storage.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Archive Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Archived</h6>
                    <h2 class="mb-0">{{ number_format($archiveStats['total_count']) }}</h2>
                    <small class="text-muted">PDFs in archive</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Archive Size</h6>
                    <h2 class="mb-0">{{ formatBytes($archiveStats['total_size']) }}</h2>
                    <small class="text-muted">Total archived data</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Monthly Cost</h6>
                    <h2 class="mb-0">${{ number_format($archiveStats['monthly_cost'], 2) }}</h2>
                    <small class="text-muted">Glacier storage cost</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Restoreable</h6>
                    <h2 class="mb-0">{{ number_format($archiveStats['restoreable']) }}</h2>
                    <small class="text-success">Ready to restore</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🔍 Search & Filter</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('superadmin.pdf-storage.archives') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Unique Code</label>
                                <input type="text" name="unique_code" class="form-control"
                                       value="{{ request('unique_code') }}"
                                       placeholder="Search by code...">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Tenant ID</label>
                                <input type="text" name="tenant_id" class="form-control"
                                       value="{{ request('tenant_id') }}"
                                       placeholder="Filter by tenant...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Archived From</label>
                                <input type="date" name="archived_from" class="form-control"
                                       value="{{ request('archived_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Archived To</label>
                                <input type="date" name="archived_to" class="form-control"
                                       value="{{ request('archived_to') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Archived PDFs List --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">📋 Archived PDFs</h5>
                        <div>
                            @if($archivedPdfs->total() > 0)
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkRestoreModal">
                                <i class="bi bi-arrow-counterclockwise"></i> Bulk Restore
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Unique Code</th>
                                    <th>Tenant ID</th>
                                    <th>Patient Name</th>
                                    <th class="text-end">Size</th>
                                    <th>Archived At</th>
                                    <th>Archive Path</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archivedPdfs as $pdf)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="pdf_ids[]" value="{{ $pdf->id }}"
                                               class="form-check-input pdf-checkbox">
                                    </td>
                                    <td>
                                        <code>{{ $pdf->unique_code }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $pdf->tenant_id }}</span>
                                    </td>
                                    <td>{{ $pdf->name ?? '-' }}</td>
                                    <td class="text-end">
                                        @if($pdf->pdf_size_bytes)
                                            {{ formatBytes($pdf->pdf_size_bytes) }}
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $pdf->pdf_deleted_at ? $pdf->pdf_deleted_at->format('Y-m-d H:i') : '-' }}
                                            <br>
                                            <span class="text-muted">{{ $pdf->pdf_deleted_at ? $pdf->pdf_deleted_at->diffForHumans() : '' }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if($pdf->pdf_archive_path)
                                            <small class="text-muted">{{ Str::limit($pdf->pdf_archive_path, 30) }}</small>
                                        @else
                                            <span class="text-danger">No path</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pdf->pdf_archive_path)
                                            <form action="{{ route('superadmin.pdf-storage.restore') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="result_id" value="{{ $pdf->id }}">
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Restore this PDF?')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                No Archive
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No archived PDFs found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $archivedPdfs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Restore Modal --}}
<div class="modal fade" id="bulkRestoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Restore PDFs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This will restore selected PDFs from archive back to local storage.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Note:</strong> Restoration from Glacier may take 3-5 hours for standard retrieval.
                </div>
                <p id="selectedCount">Selected: <strong>0</strong> PDFs</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="bulkRestore()">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore Selected
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.pdf-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
    updateSelectedCount();
});

// Update selected count
document.querySelectorAll('.pdf-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const selected = document.querySelectorAll('.pdf-checkbox:checked').length;
    const countEl = document.getElementById('selectedCount');
    if (countEl) {
        countEl.innerHTML = `Selected: <strong>${selected}</strong> PDFs`;
    }
}

function bulkRestore() {
    const selected = Array.from(document.querySelectorAll('.pdf-checkbox:checked'))
        .map(cb => cb.value);

    if (selected.length === 0) {
        alert('Please select at least one PDF to restore');
        return;
    }

    if (!confirm(`Restore ${selected.length} PDFs from archive?`)) {
        return;
    }

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("superadmin.pdf-storage.bulk-restore") }}';

    // CSRF token
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    // Add selected IDs
    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'result_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

// Initialize selected count
updateSelectedCount();
</script>
@endpush
@endsection
