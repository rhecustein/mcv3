@extends('layouts.admin')

@section('title', 'PDF Storage Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">📁 PDF Storage Management</h1>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total PDFs</h5>
                    <h2 class="mb-0">{{ number_format($totalPdfs) }}</h2>
                    <small class="text-muted">Stored certificates</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Size</h5>
                    <h2 class="mb-0">{{ formatBytes($totalSize) }}</h2>
                    <small class="text-muted">Disk usage</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title text-muted">Archived</h5>
                    <h2 class="mb-0">{{ number_format($archivedCount) }}</h2>
                    <small class="text-muted">In cold storage</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-muted">Auto-Delete</h5>
                    <h2 class="mb-0">{{ $globalSettings->auto_delete_days }} days</h2>
                    <small class="text-muted">
                        <span class="badge {{ $globalSettings->auto_delete_enabled ? 'bg-success' : 'bg-secondary' }}">
                            {{ $globalSettings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage by Tenant --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Storage by Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tenant ID</th>
                                    <th class="text-end">PDF Count</th>
                                    <th class="text-end">Total Size</th>
                                    <th class="text-end">Oldest PDF</th>
                                    <th class="text-end">Latest PDF</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storageByTenant as $storage)
                                <tr>
                                    <td>
                                        <code>{{ $storage->tenant_id }}</code>
                                    </td>
                                    <td class="text-end">{{ number_format($storage->pdf_count) }}</td>
                                    <td class="text-end">
                                        <strong>{{ $storage->total_size }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @if($storage->oldest_pdf_days)
                                            <span class="badge bg-secondary">{{ $storage->oldest_pdf_days }} days ago</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($storage->latest_pdf_days !== null)
                                            <span class="badge bg-info">{{ $storage->latest_pdf_days }} days ago</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="cleanupTenant('{{ $storage->tenant_id }}')">
                                            <i class="bi bi-trash"></i> Cleanup
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Manual Cleanup --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">🗑️ Manual Cleanup</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.pdf-storage.cleanup') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Delete PDFs older than</label>
                            <select name="days_old" class="form-select" required>
                                <option value="30">30 days (1 month)</option>
                                <option value="60">60 days (2 months)</option>
                                <option value="90" selected>90 days (3 months)</option>
                                <option value="180">180 days (6 months)</option>
                                <option value="365">365 days (1 year)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tenant (Optional)</label>
                            <input type="text" name="tenant_id" class="form-control" placeholder="Leave empty for all tenants">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="archive" value="1" class="form-check-input" id="archiveCheck" checked>
                            <label class="form-check-label" for="archiveCheck">
                                Archive before delete (recommended)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to cleanup old PDFs?')">
                            <i class="bi bi-trash"></i> Run Cleanup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent Cleanups --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📜 Recent Cleanup Logs</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentCleanups as $log)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $log->executed_at->format('Y-m-d H:i') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $log->deleted_count }} deleted • {{ $log->archived_count }} archived
                                        @if($log->tenant_id)
                                            • Tenant: <code>{{ $log->tenant_id }}</code>
                                        @else
                                            • All tenants
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-success">{{ $log->freed_size }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No cleanup logs yet</p>
                        @endforelse
                    </div>

                    <a href="{{ route('superadmin.pdf-storage.logs') }}" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        View All Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">⚡ Quick Actions</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('superadmin.pdf-storage.settings') }}" class="btn btn-outline-primary">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                        <a href="{{ route('superadmin.pdf-storage.logs') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-file-earmark-text"></i> View Logs
                        </a>
                        <a href="{{ route('superadmin.pdf-storage.statistics') }}" class="btn btn-outline-info">
                            <i class="bi bi-graph-up"></i> Statistics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cleanupTenant(tenantId) {
    if (confirm(`Cleanup old PDFs for tenant ${tenantId}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("superadmin.pdf-storage.cleanup") }}';

        form.innerHTML = `
            @csrf
            <input type="hidden" name="tenant_id" value="${tenantId}">
            <input type="hidden" name="days_old" value="90">
            <input type="hidden" name="archive" value="1">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
