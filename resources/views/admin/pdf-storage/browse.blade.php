@extends('layouts.admin')

@section('title', 'Browse PDFs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">🔍 Browse PDF Certificates</h1>
                <a href="{{ route('admin.pdf-storage.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Browse Stats --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total PDFs</h6>
                    <h3 class="mb-0">{{ number_format($browsStats['total_count']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Size</h6>
                    <h3 class="mb-0">{{ formatBytes($browsStats['total_size']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Showing</h6>
                    <h3 class="mb-0">{{ number_format($browsStats['showing_count']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🔎 Search & Filter</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.pdf-storage.browse') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control"
                                       value="{{ request('search') }}"
                                       placeholder="Unique code, name, or NIK...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Compression</label>
                                <select name="compressed" class="form-select">
                                    <option value="">All</option>
                                    <option value="1" {{ request('compressed') === '1' ? 'selected' : '' }}>Compressed</option>
                                    <option value="0" {{ request('compressed') === '0' ? 'selected' : '' }}>Original</option>
                                </select>
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

                        @if(request()->hasAny(['search', 'date_from', 'date_to', 'compressed']))
                        <div class="mt-3">
                            <a href="{{ route('admin.pdf-storage.browse') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- PDFs List --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">📋 PDF Certificates</h5>
                        <span class="text-muted">{{ $pdfs->total() }} results</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Unique Code</th>
                                    <th>Patient Info</th>
                                    <th class="text-end">Size</th>
                                    <th>Generated</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pdfs as $pdf)
                                <tr>
                                    <td>
                                        <code class="text-primary">{{ $pdf->unique_code }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $pdf->name ?? '-' }}</strong>
                                        @if($pdf->nik)
                                            <br><small class="text-muted">NIK: {{ $pdf->nik }}</small>
                                        @endif
                                        @if($pdf->company)
                                            <br><small class="text-muted">Company: {{ $pdf->company }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($pdf->pdf_size_bytes)
                                            <strong>{{ formatBytes($pdf->pdf_size_bytes) }}</strong>
                                            @if($pdf->pdf_compressed && $pdf->pdf_original_size_bytes)
                                                <br>
                                                <small class="text-success">
                                                    <i class="bi bi-arrow-down"></i>
                                                    {{ $pdf->pdf_compression_ratio }}% saved
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pdf->pdf_generated_at)
                                            <small>
                                                {{ $pdf->pdf_generated_at->format('Y-m-d') }}
                                                <br>
                                                <span class="text-muted">{{ $pdf->pdf_generated_at->format('H:i') }}</span>
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pdf->pdf_compressed)
                                            <span class="badge bg-success">
                                                <i class="bi bi-file-zip"></i> Compressed
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-file-pdf"></i> Original
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.pdf-storage.download', $pdf->id) }}"
                                               class="btn btn-primary"
                                               title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#pdfInfoModal{{ $pdf->id }}"
                                                    title="View Details">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- PDF Info Modal --}}
                                <div class="modal fade" id="pdfInfoModal{{ $pdf->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">PDF Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th width="40%">Unique Code:</th>
                                                        <td><code>{{ $pdf->unique_code }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Patient Name:</th>
                                                        <td>{{ $pdf->name ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>NIK:</th>
                                                        <td>{{ $pdf->nik ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Company:</th>
                                                        <td>{{ $pdf->company ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>File Size:</th>
                                                        <td>{{ formatBytes($pdf->pdf_size_bytes) }}</td>
                                                    </tr>
                                                    @if($pdf->pdf_compressed)
                                                    <tr>
                                                        <th>Original Size:</th>
                                                        <td>{{ formatBytes($pdf->pdf_original_size_bytes) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Compression:</th>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                {{ $pdf->pdf_compression_ratio }}% reduction
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Compressed At:</th>
                                                        <td>{{ $pdf->pdf_compressed_at ? $pdf->pdf_compressed_at->format('Y-m-d H:i') : '-' }}</td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <th>Generated At:</th>
                                                        <td>{{ $pdf->pdf_generated_at ? $pdf->pdf_generated_at->format('Y-m-d H:i:s') : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Path:</th>
                                                        <td><small class="text-muted">{{ $pdf->pdf_path }}</small></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ route('admin.pdf-storage.download', $pdf->id) }}"
                                                   class="btn btn-primary">
                                                    <i class="bi bi-download"></i> Download PDF
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-2">No PDFs found matching your criteria</p>
                                        @if(request()->hasAny(['search', 'date_from', 'date_to', 'compressed']))
                                            <a href="{{ route('admin.pdf-storage.browse') }}" class="btn btn-outline-primary">
                                                Clear Filters
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($pdfs->hasPages())
                    <div class="mt-3">
                        {{ $pdfs->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
