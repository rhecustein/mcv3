@extends('layouts.admin')

@section('title', 'PDF Cleanup Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">📜 PDF Cleanup Logs</h1>
                <a href="{{ route('superadmin.pdf-storage.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cleanup History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Executed At</th>
                                    <th>Tenant</th>
                                    <th>Days Old</th>
                                    <th class="text-end">Archived</th>
                                    <th class="text-end">Deleted</th>
                                    <th class="text-end">Errors</th>
                                    <th class="text-end">Freed Space</th>
                                    <th>Triggered By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->executed_at->format('Y-m-d H:i:s') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->executed_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($log->tenant_id)
                                            <code>{{ $log->tenant_id }}</code>
                                        @else
                                            <span class="badge bg-secondary">All Tenants</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $log->days_old }} days</span>
                                    </td>
                                    <td class="text-end">
                                        @if($log->archive_enabled)
                                            <span class="badge bg-success">{{ number_format($log->archived_count) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">{{ number_format($log->deleted_count) }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($log->error_count > 0)
                                            <span class="badge bg-warning">{{ number_format($log->error_count) }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ $log->freed_size }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->triggered_by == 'schedule' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ ucfirst($log->triggered_by ?? 'manual') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->notes)
                                            <small class="text-muted">{{ $log->notes }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No cleanup logs found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>
                </div>

                {{-- Summary Stats --}}
                @if($logs->total() > 0)
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="mb-0">
                                <small class="text-muted d-block">Total Cleanups</small>
                                <strong>{{ number_format($logs->total()) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-0">
                                <small class="text-muted d-block">Total Archived</small>
                                <strong class="text-success">{{ number_format($logs->sum('archived_count')) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-0">
                                <small class="text-muted d-block">Total Deleted</small>
                                <strong class="text-danger">{{ number_format($logs->sum('deleted_count')) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-0">
                                <small class="text-muted d-block">Total Space Freed</small>
                                <strong class="text-info">
                                    @php
                                        $totalFreed = $logs->sum('freed_bytes');
                                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                        for ($i = 0; $totalFreed > 1024 && $i < 4; $i++) {
                                            $totalFreed /= 1024;
                                        }
                                        echo round($totalFreed, 2) . ' ' . $units[$i];
                                    @endphp
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
