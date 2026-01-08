@extends('layouts.admin')

@section('title', 'Compression Management')

@push('styles')
<style>
.compression-card {
    transition: transform 0.2s;
}
.compression-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.progress-ring {
    transform: rotate(-90deg);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">🗜️ Compression Management</h1>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#compressUncompressedModal">
                        <i class="bi bi-lightning-charge"></i> Compress Uncompressed PDFs
                    </button>
                    <a href="{{ route('superadmin.pdf-storage.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Compression Overview Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card compression-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Compressed PDFs</h6>
                            <h2 class="mb-0">{{ number_format($stats['compressed_count']) }}</h2>
                            <small class="text-success">{{ $stats['compression_percentage'] }}% of total</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-file-zip" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card compression-card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Uncompressed PDFs</h6>
                            <h2 class="mb-0">{{ number_format($stats['uncompressed_count']) }}</h2>
                            <small class="text-muted">Ready to compress</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-file-earmark-pdf" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card compression-card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Space Saved</h6>
                            <h2 class="mb-0">{{ $stats['total_savings_formatted'] }}</h2>
                            <small class="text-success">{{ $stats['avg_compression_ratio'] }}% avg ratio</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-hdd" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card compression-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Compression Jobs</h6>
                            <h2 class="mb-0">{{ $stats['pending_jobs'] }}</h2>
                            <small class="text-muted">In queue</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Compression Settings --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⚙️ Global Compression Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.pdf-storage.compression.update-settings') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Compression Method</strong>
                            </label>
                            <select name="compression_method" class="form-select">
                                <option value="ghostscript" {{ $settings->compression_method === 'ghostscript' ? 'selected' : '' }}>
                                    Ghostscript (Recommended)
                                </option>
                                <option value="imagick" {{ $settings->compression_method === 'imagick' ? 'selected' : '' }}>
                                    ImageMagick
                                </option>
                            </select>
                            <small class="text-muted">Tool used for PDF compression</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Compression Quality</strong>
                            </label>
                            <select name="compression_quality" class="form-select">
                                <option value="screen" {{ $settings->compression_quality === 'screen' ? 'selected' : '' }}>
                                    Screen (Lowest quality, smallest size)
                                </option>
                                <option value="ebook" {{ $settings->compression_quality === 'ebook' ? 'selected' : '' }}>
                                    eBook (Medium quality)
                                </option>
                                <option value="printer" {{ $settings->compression_quality === 'printer' ? 'selected' : '' }}>
                                    Printer (High quality)
                                </option>
                                <option value="prepress" {{ $settings->compression_quality === 'prepress' ? 'selected' : '' }}>
                                    Prepress (Highest quality, largest size)
                                </option>
                            </select>
                            <small class="text-muted">Balance between quality and file size</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="auto_compress" class="form-check-input" id="autoCompress"
                                    {{ $settings->auto_compress ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoCompress">
                                    <strong>Auto-compress new PDFs</strong>
                                </label>
                            </div>
                            <small class="text-muted">Automatically compress PDFs when generated</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Minimum Size for Compression (KB)</strong>
                            </label>
                            <input type="number" name="min_size_kb" class="form-control"
                                value="{{ $settings->min_compression_size_kb ?? 100 }}" min="0">
                            <small class="text-muted">Only compress PDFs larger than this size</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 Compression Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Overall Compression Rate</span>
                            <strong>{{ $stats['compression_percentage'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['compression_percentage'] }}%">
                                {{ $stats['compression_percentage'] }}%
                            </div>
                        </div>
                    </div>

                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Total PDFs</strong></td>
                                <td class="text-end">{{ number_format($stats['total_pdfs']) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Compressed</strong></td>
                                <td class="text-end text-success">{{ number_format($stats['compressed_count']) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Uncompressed</strong></td>
                                <td class="text-end text-warning">{{ number_format($stats['uncompressed_count']) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Original Total Size</strong></td>
                                <td class="text-end">{{ $stats['original_size_formatted'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Total Size</strong></td>
                                <td class="text-end">{{ $stats['current_size_formatted'] }}</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Total Savings</strong></td>
                                <td class="text-end"><strong>{{ $stats['total_savings_formatted'] }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Average Ratio</strong></td>
                                <td class="text-end">{{ $stats['avg_compression_ratio'] }}%</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            You're saving <strong>{{ $stats['total_savings_formatted'] }}</strong> of storage space through compression!
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Compression Jobs --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⏱️ Recent Compression Jobs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Certificate Code</th>
                                    <th>Tenant</th>
                                    <th class="text-end">Original Size</th>
                                    <th class="text-end">Compressed Size</th>
                                    <th class="text-end">Savings</th>
                                    <th class="text-end">Ratio</th>
                                    <th>Method</th>
                                    <th>Compressed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCompressions as $result)
                                <tr>
                                    <td><code>{{ $result->unique_code }}</code></td>
                                    <td><small>{{ $result->tenant_id }}</small></td>
                                    <td class="text-end">{{ formatBytes($result->pdf_original_size_bytes) }}</td>
                                    <td class="text-end">{{ formatBytes($result->pdf_size_bytes) }}</td>
                                    <td class="text-end text-success">
                                        <strong>{{ formatBytes($result->pdf_original_size_bytes - $result->pdf_size_bytes) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ $result->pdf_compression_ratio }}%</span>
                                    </td>
                                    <td><small>{{ $result->pdf_compression_method }}</small></td>
                                    <td><small>{{ $result->pdf_compressed_at?->diffForHumans() }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No recent compressions</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($recentCompressions->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $recentCompressions->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Compress Uncompressed PDFs Modal --}}
<div class="modal fade" id="compressUncompressedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compress Uncompressed PDFs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('superadmin.pdf-storage.compression.compress-uncompressed') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Warning:</strong> This will queue {{ number_format($stats['uncompressed_count']) }} PDFs for compression.
                    </div>

                    <p>This action will:</p>
                    <ul>
                        <li>Add all uncompressed PDFs to the compression queue</li>
                        <li>Process them in the background</li>
                        <li>Save approximately {{ $stats['estimated_savings_formatted'] ?? 'unknown' }} of storage</li>
                    </ul>

                    <div class="mb-3">
                        <label class="form-label"><strong>Tenant Filter (Optional)</strong></label>
                        <input type="text" name="tenant_id" class="form-control" placeholder="Leave empty for all tenants">
                        <small class="text-muted">Specify tenant ID to compress only their PDFs</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Batch Size</strong></label>
                        <input type="number" name="batch_size" class="form-control" value="100" min="1" max="1000">
                        <small class="text-muted">Number of PDFs to process per batch</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-lightning-charge"></i> Start Compression
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
