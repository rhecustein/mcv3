@extends('layouts.admin')

@section('title', 'PDF Storage Overview')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">📁 My PDF Storage</h1>
                <a href="{{ route('admin.pdf-storage.browse') }}" class="btn btn-primary">
                    <i class="bi bi-search"></i> Browse All PDFs
                </a>
            </div>
        </div>
    </div>

    {{-- Storage Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total PDFs</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_pdfs']) }}</h2>
                            <small class="text-muted">Active certificates</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-file-earmark-pdf" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Storage Used</h6>
                            <h2 class="mb-0">{{ formatBytes($stats['total_size']) }}</h2>
                            @if($stats['quota_limit'] > 0)
                                <small class="text-muted">of {{ $stats['quota_limit'] }} GB</small>
                            @else
                                <small class="text-muted">No quota limit</small>
                            @endif
                        </div>
                        <div class="text-success">
                            <i class="bi bi-hdd" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Compressed</h6>
                            <h2 class="mb-0">{{ number_format($stats['compressed_count']) }}</h2>
                            <small class="text-muted">Space optimized</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-file-zip" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Archived</h6>
                            <h2 class="mb-0">{{ number_format($stats['archived_count']) }}</h2>
                            <small class="text-muted">In cold storage</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-archive" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage Quota Progress --}}
    @if($stats['quota_limit'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 Storage Quota</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Used: {{ formatBytes($stats['total_size']) }}</span>
                            <span>Limit: {{ $stats['quota_limit'] }} GB</span>
                        </div>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar {{ $stats['quota_used'] > 80 ? 'bg-danger' : ($stats['quota_used'] > 60 ? 'bg-warning' : 'bg-success') }}"
                             role="progressbar"
                             style="width: {{ min($stats['quota_used'], 100) }}%"
                             aria-valuenow="{{ $stats['quota_used'] }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ $stats['quota_used'] }}%
                        </div>
                    </div>
                    @if($stats['quota_used'] > 80)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Warning:</strong> You're using {{ $stats['quota_used'] }}% of your storage quota. Consider deleting old PDFs or contact admin for quota increase.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent PDFs --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">📄 Recent PDFs</h5>
                        <a href="{{ route('admin.pdf-storage.browse') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Unique Code</th>
                                    <th>Patient Name</th>
                                    <th>NIK</th>
                                    <th class="text-end">Size</th>
                                    <th>Generated At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPdfs as $pdf)
                                <tr>
                                    <td><code>{{ $pdf->unique_code }}</code></td>
                                    <td>{{ $pdf->name ?? '-' }}</td>
                                    <td>{{ $pdf->nik ?? '-' }}</td>
                                    <td class="text-end">
                                        @if($pdf->pdf_size_bytes)
                                            {{ formatBytes($pdf->pdf_size_bytes) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $pdf->pdf_generated_at ? $pdf->pdf_generated_at->format('Y-m-d H:i') : '-' }}
                                            <br>
                                            <span class="text-muted">
                                                {{ $pdf->pdf_generated_at ? $pdf->pdf_generated_at->diffForHumans() : '' }}
                                            </span>
                                        </small>
                                    </td>
                                    <td>
                                        @if($pdf->pdf_compressed)
                                            <span class="badge bg-success">Compressed</span>
                                        @else
                                            <span class="badge bg-secondary">Original</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pdf-storage.download', $pdf->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No PDFs generated yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage Settings Info --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⚙️ Storage Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Auto-Delete</h6>
                            <p class="mb-0">
                                <span class="badge {{ $settings->auto_delete_enabled ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $settings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                                @if($settings->auto_delete_enabled)
                                    <br><small class="text-muted">PDFs deleted after {{ $settings->auto_delete_days }} days</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Archive Before Delete</h6>
                            <p class="mb-0">
                                <span class="badge {{ $settings->archive_before_delete ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $settings->archive_before_delete ? 'Yes' : 'No' }}
                                </span>
                                @if($settings->archive_before_delete)
                                    <br><small class="text-muted">Storage: {{ ucfirst($settings->archive_storage) }}</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Compression</h6>
                            <p class="mb-0">
                                <span class="badge {{ $settings->compression_enabled ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $settings->compression_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                                @if($settings->compression_enabled)
                                    <br><small class="text-muted">After {{ $settings->compression_days }} days</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a href="{{ route('admin.pdf-storage.settings') }}" class="btn btn-outline-secondary">
                            View Full Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
