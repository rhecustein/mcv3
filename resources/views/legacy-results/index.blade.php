@extends('layouts.admin')

@section('title', 'Legacy Results')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-archive me-2"></i>Legacy Results
            </h1>
            <p class="text-muted mb-0">Kelola data hasil pemeriksaan lama/legacy</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statisticsModal">
                <i class="fas fa-chart-bar me-1"></i> Statistik
            </button>
            <a href="{{ route('legacy-results.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export
            </a>
            <a href="{{ route('legacy-results.trash') }}" class="btn btn-outline-danger">
                <i class="fas fa-trash me-1"></i> Trash
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Legacy Results
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalLegacyResults) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Medical Certificate (MC)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalMC) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Surat Keterangan Sehat (SKB)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalSKB) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Oldest Result
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if($oldestResult)
                                    {{ $oldestResult->created_at->format('M Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('legacy-results.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nama pasien, NIK, kode unik...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="type" class="form-label">Tipe</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">Semua</option>
                            <option value="mc" {{ request('type') == 'mc' ? 'selected' : '' }}>MC</option>
                            <option value="skb" {{ request('type') == 'skb' ? 'selected' : '' }}>SKB</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('legacy-results.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Legacy Results</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks me-1"></i> Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Bulk Actions (Hidden by default) -->
            <div id="bulk-actions" class="alert alert-info" style="display: none;">
                <form method="POST" action="{{ route('legacy-results.bulk-action') }}" id="bulk-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <select name="action" class="form-control" required>
                                <option value="">Pilih Aksi</option>
                                <option value="delete">Hapus</option>
                                <option value="archive">Arsipkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-play me-1"></i> Jalankan
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleBulkActions()">
                                Batal
                            </button>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <span id="selected-count">0</span> item dipilih
                            </small>
                        </div>
                    </div>
                </form>
            </div>

            @if($results->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Kode Unik</th>
                                <th>Tipe</th>
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Outlet</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" 
                                               value="{{ $result->id }}" 
                                               class="form-check-input bulk-checkbox">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $result->unique_code }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $result->type == 'mc' ? 'bg-danger' : 'bg-success' }}">
                                            {{ strtoupper($result->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $result->patient->full_name ?? 'N/A' }}</strong>
                                            @if($result->patient && $result->patient->nik)
                                                <br><small class="text-muted">NIK: {{ $result->patient->nik }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{ $result->doctor->user->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <div>
                                            {{ $result->outlet->name ?? 'N/A' }}
                                            @if($result->outlet && $result->outlet->city)
                                                <br><small class="text-muted">{{ $result->outlet->city }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $result->created_at->format('d/m/Y') }}
                                            <br><small class="text-muted">{{ $result->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($result->deleted_at)
                                            <span class="badge bg-danger">Deleted</span>
                                        @elseif($result->archived_at)
                                            <span class="badge bg-warning">Archived</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('legacy-results.show', $result->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$result->deleted_at)
                                                <a href="{{ route('results.download', Crypt::encrypt($result->id)) }}" 
                                                   class="btn btn-sm btn-outline-success" title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $results->firstItem() }}-{{ $results->lastItem() }} 
                        dari {{ $results->total() }} hasil
                    </div>
                    {{ $results->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-archive fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Tidak ada legacy results ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian atau parameter lainnya.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar me-2"></i>Statistik Legacy Results
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="statistics-content">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Loading statistik...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Bulk Actions
function toggleBulkActions() {
    const bulkDiv = document.getElementById('bulk-actions');
    bulkDiv.style.display = bulkDiv.style.display === 'none' ? 'block' : 'none';
    
    if (bulkDiv.style.display === 'none') {
        // Reset checkboxes
        document.getElementById('select-all').checked = false;
        document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = false);
        updateSelectedCount();
    }
}

// Select All Checkbox
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

// Individual Checkboxes
document.querySelectorAll('.bulk-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.bulk-checkbox:checked').length;
    document.getElementById('selected-count').textContent = selectedCount;
    
    // Update form data
    const form = document.getElementById('bulk-form');
    const existingInputs = form.querySelectorAll('input[name="selected_ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    document.querySelectorAll('.bulk-checkbox:checked').forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
}

// Bulk Form Submission
document.getElementById('bulk-form').addEventListener('submit', function(e) {
    const selectedCount = document.querySelectorAll('.bulk-checkbox:checked').length;
    const action = this.querySelector('select[name="action"]').value;
    
    if (selectedCount === 0) {
        e.preventDefault();
        alert('Pilih minimal satu item terlebih dahulu.');
        return;
    }
    
    const actionText = {
        'delete': 'menghapus',
        'archive': 'mengarsipkan'
    };
    
    if (!confirm(`Apakah Anda yakin ingin ${actionText[action]} ${selectedCount} item yang dipilih?`)) {
        e.preventDefault();
    }
});

// Load Statistics
document.getElementById('statisticsModal').addEventListener('show.bs.modal', function() {
    fetch('{{ route("legacy-results.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('statistics-content').innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Total Legacy</h6>
                                <h4 class="text-primary">${data.total_legacy.toLocaleString()}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Total Trash</h6>
                                <h4 class="text-danger">${data.total_trashed.toLocaleString()}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Berdasarkan Tipe</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Medical Certificate (MC)</span>
                                        <strong>${data.total_mc.toLocaleString()}</strong>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger" style="width: ${(data.total_mc / data.total_legacy * 100)}%"></div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Surat Keterangan Sehat (SKB)</span>
                                        <strong>${data.total_skb.toLocaleString()}</strong>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: ${(data.total_skb / data.total_legacy * 100)}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Top 5 Outlet</h6>
                            </div>
                            <div class="card-body">
                                ${data.by_outlet.slice(0, 5).map(outlet => `
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-truncate" style="max-width: 150px;" title="${outlet.outlet_name}">${outlet.outlet_name}</span>
                                            <strong>${outlet.total.toLocaleString()}</strong>
                                        </div>
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar" style="width: ${(outlet.total / data.by_outlet[0].total * 100)}%"></div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Tren Bulanan (12 Bulan Terakhir)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    ${data.by_month.map(month => `
                                        <div class="col-md-2 mb-2 text-center">
                                            <div class="bg-light p-2 rounded">
                                                <small class="text-muted">${month.month}/${month.year}</small>
                                                <div class="h6 mb-0">${month.total.toLocaleString()}</div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${data.oldest_result ? `
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Data Tertua:</strong> ${new Date(data.oldest_result.created_at).toLocaleDateString('id-ID')}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Data Terbaru:</strong> ${new Date(data.newest_result.created_at).toLocaleDateString('id-ID')}
                            </div>
                        </div>
                    </div>
                ` : ''}
            `;
        })
        .catch(error => {
            document.getElementById('statistics-content').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat statistik. Silakan coba lagi.
                </div>
            `;
        });
});
</script>
@endpush