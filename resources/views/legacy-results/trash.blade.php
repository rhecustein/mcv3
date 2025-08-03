@extends('layouts.admin')

@section('title', 'Legacy Results Trash')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-trash me-2"></i>Legacy Results Trash
            </h1>
            <p class="text-muted mb-0">Kelola data legacy results yang telah dihapus</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('legacy-results.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Legacy Results
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Items di Trash
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalTrashed) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trash fa-2x text-gray-300"></i>
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
                                Dapat Direstore
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($trashedResults->where('deleted_at', '>=', now()->startOfDay())->count()) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Oldest in Trash
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if($trashedResults->count() > 0)
                                    {{ $trashedResults->last()->deleted_at->format('M Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('legacy-results.trash') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="ID, nama user...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Dihapus Dari</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Dihapus Sampai</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="d-flex flex-column gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('legacy-results.trash') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Trash Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Items di Trash</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks me-1"></i> Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Bulk Actions (Hidden by default) -->
            <div id="bulk-actions" class="alert alert-warning" style="display: none;">
                <form method="POST" action="{{ route('legacy-results.bulk-action') }}" id="bulk-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <select name="action" class="form-control" required>
                                <option value="">Pilih Aksi</option>
                                <option value="restore">Restore</option>
                                <option value="force_delete">Hapus Permanen</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-warning btn-sm">
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

            @if($trashedResults->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>ID</th>
                                <th>Dihapus Oleh</th>
                                <th>Outlet</th>
                                <th>Tanggal Dihapus</th>
                                <th>IP Address</th>
                                <th>Lokasi</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedResults as $trash)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" 
                                               value="{{ $trash->id }}" 
                                               class="form-check-input bulk-checkbox">
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $trash->id }}</span>
                                    </td>
                                    <td>
                                        @if($trash->user)
                                            <div>
                                                <strong>{{ $trash->user->name }}</strong>
                                                <br><small class="text-muted">{{ $trash->user->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trash->outlet)
                                            <div>
                                                {{ $trash->outlet->name }}
                                                @if($trash->outlet->city)
                                                    <br><small class="text-muted">{{ $trash->outlet->city }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            {{ $trash->deleted_at->format('d/m/Y') }}
                                            <br><small class="text-muted">{{ $trash->deleted_at->format('H:i') }}</small>
                                        </div>
                                        <small class="text-muted">({{ $trash->deleted_at->diffForHumans() }})</small>
                                    </td>
                                    <td>
                                        <code class="small">{{ $trash->deleted_ip ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $trash->deleted_location ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical d-grid gap-1" role="group">
                                            <form method="POST" action="{{ route('legacy-results.restore', $trash->id) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin restore item ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success w-100" title="Restore">
                                                    <i class="fas fa-undo me-1"></i> Restore
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('legacy-results.force-delete', $trash->id) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('PERINGATAN: Item akan dihapus PERMANEN dan tidak dapat dikembalikan! Lanjutkan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100" title="Hapus Permanen">
                                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                                </button>
                                            </form>
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
                        Menampilkan {{ $trashedResults->firstItem() }}-{{ $trashedResults->lastItem() }} 
                        dari {{ $trashedResults->total() }} hasil
                    </div>
                    {{ $trashedResults->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Trash kosong</h5>
                    <p class="text-muted">Tidak ada legacy results di trash.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Warning Card -->
    <div class="card border-left-warning shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="text-warning mb-1">
                        <i class="fas fa-exclamation-triangle me-2"></i>Perhatian
                    </h6>
                    <div class="small text-gray-600">
                        <ul class="mb-0">
                            <li>Items di trash dapat direstore dalam waktu 30 hari setelah dihapus</li>
                            <li>Setelah 30 hari, beberapa data mungkin sudah tidak dapat direstore dengan sempurna</li>
                            <li>Aksi "Hapus Permanen" akan menghapus data secara PERMANEN dan tidak dapat dikembalikan</li>
                            <li>File PDF yang terkait juga akan ikut terhapus permanen</li>
                        </ul>
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
        'restore': 'restore',
        'force_delete': 'menghapus PERMANEN'
    };
    
    let confirmMessage = `Apakah Anda yakin ingin ${actionText[action]} ${selectedCount} item yang dipilih?`;
    
    if (action === 'force_delete') {
        confirmMessage += '\n\nPERINGATAN: Data akan dihapus PERMANEN dan tidak dapat dikembalikan!';
    }
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
    }
});

// Auto-refresh every 30 seconds
let autoRefreshInterval;

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        window.location.reload();
    }, 30000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Start auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Stop auto-refresh when user is interacting
document.addEventListener('click', stopAutoRefresh);
document.addEventListener('keypress', stopAutoRefresh);

// Resume auto-refresh after 5 seconds of inactivity
let inactivityTimer;
document.addEventListener('mousemove', function() {
    stopAutoRefresh();
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(startAutoRefresh, 5000);
});
</script>
@endpush