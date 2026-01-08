@extends('layouts.admin')

@section('title', 'Real-time Monitoring')

@push('styles')
<style>
.metric-card {
    transition: all 0.3s ease;
}
.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}
.pulse {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}
.status-healthy { background-color: #28a745; }
.status-warning { background-color: #ffc107; }
.status-critical { background-color: #dc3545; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">📡 Real-time System Monitoring</h1>
                <div>
                    <button class="btn btn-primary" onclick="refreshMonitoring()">
                        <i class="bi bi-arrow-clockwise pulse"></i> Refresh
                    </button>
                    <a href="{{ route('superadmin.pdf-storage.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <small class="text-muted">Last updated: <span id="lastUpdate">{{ now()->format('Y-m-d H:i:s') }}</span></small>
        </div>
    </div>

    {{-- System Health Status --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">🏥 System Health Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="status-indicator {{ $health['storage'] === 'healthy' ? 'status-healthy' : ($health['storage'] === 'warning' ? 'status-warning' : 'status-critical') }}"></span>
                                <strong>Storage</strong>
                                <p class="mt-2">{{ ucfirst($health['storage']) }}</p>
                                <small class="text-muted">{{ $health['storage_usage'] }}% used</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="status-indicator {{ $health['queue'] === 'healthy' ? 'status-healthy' : 'status-warning' }}"></span>
                                <strong>Queue</strong>
                                <p class="mt-2">{{ ucfirst($health['queue']) }}</p>
                                <small class="text-muted">{{ $health['queue_jobs'] }} pending</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="status-indicator {{ $health['compression'] === 'healthy' ? 'status-healthy' : 'status-warning' }}"></span>
                                <strong>Compression</strong>
                                <p class="mt-2">{{ ucfirst($health['compression']) }}</p>
                                <small class="text-muted">{{ $health['compression_rate'] }}% rate</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="status-indicator {{ $health['cleanup'] === 'healthy' ? 'status-healthy' : 'status-warning' }}"></span>
                                <strong>Cleanup</strong>
                                <p class="mt-2">{{ ucfirst($health['cleanup']) }}</p>
                                <small class="text-muted">{{ $health['last_cleanup'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Real-time Metrics --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card metric-card border-primary">
                <div class="card-body">
                    <h6 class="text-muted mb-2">📁 Total PDFs</h6>
                    <h2 class="mb-1">{{ number_format($metrics['total_pdfs']) }}</h2>
                    <small class="text-success">+{{ $metrics['pdfs_today'] }} today</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card metric-card border-success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">💾 Storage Used</h6>
                    <h2 class="mb-1">{{ $metrics['storage_used_formatted'] }}</h2>
                    <small class="text-muted">{{ $metrics['storage_percentage'] }}% of capacity</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card metric-card border-info">
                <div class="card-body">
                    <h6 class="text-muted mb-2">⚡ Queue Jobs</h6>
                    <h2 class="mb-1">{{ number_format($metrics['queue_jobs']) }}</h2>
                    <small class="text-warning">{{ $metrics['failed_jobs'] }} failed</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card metric-card border-warning">
                <div class="card-body">
                    <h6 class="text-muted mb-2">📊 Avg Response</h6>
                    <h2 class="mb-1">{{ $metrics['avg_response_time'] }}ms</h2>
                    <small class="text-muted">Last 1000 requests</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Charts --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📈 PDF Generation Activity (Last 24 Hours)</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🎯 Top Active Tenants</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($topActiveTenants as $index => $tenant)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong>{{ $index + 1 }}.</strong> {{ $tenant->tenant_id }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $tenant->count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage & Performance --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">💿 Storage Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Active PDFs</span>
                            <strong>{{ formatBytes($storage['active']) }}</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: {{ $storage['active_percentage'] }}%">
                                {{ $storage['active_percentage'] }}%
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Archived PDFs</span>
                            <strong>{{ formatBytes($storage['archived']) }}</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" style="width: {{ $storage['archived_percentage'] }}%">
                                {{ $storage['archived_percentage'] }}%
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Compressed Savings</span>
                            <strong>{{ formatBytes($storage['savings']) }}</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-warning" style="width: {{ $storage['savings_percentage'] }}%">
                                {{ $storage['savings_percentage'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⚡ System Performance</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Queue Workers</strong></td>
                                <td class="text-end">
                                    <span class="badge {{ $performance['queue_workers'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $performance['queue_workers'] }} active
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Cache Hit Rate</strong></td>
                                <td class="text-end">{{ $performance['cache_hit_rate'] }}%</td>
                            </tr>
                            <tr>
                                <td><strong>Database Queries</strong></td>
                                <td class="text-end">{{ $performance['db_queries'] }} /sec</td>
                            </tr>
                            <tr>
                                <td><strong>Memory Usage</strong></td>
                                <td class="text-end">{{ $performance['memory_usage'] }} MB</td>
                            </tr>
                            <tr>
                                <td><strong>Disk I/O</strong></td>
                                <td class="text-end">
                                    <span class="{{ $performance['disk_io'] < 80 ? 'text-success' : 'text-warning' }}">
                                        {{ $performance['disk_io'] }}% util
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Last Cleanup</strong></td>
                                <td class="text-end">{{ $performance['last_cleanup'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🕐 Recent System Activities</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Activity</th>
                                    <th>Tenant</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td><small>{{ $activity->created_at->format('H:i:s') }}</small></td>
                                    <td>
                                        @if($activity->type === 'pdf_generated')
                                            <i class="bi bi-file-earmark-plus text-success"></i> PDF Generated
                                        @elseif($activity->type === 'pdf_compressed')
                                            <i class="bi bi-file-zip text-info"></i> PDF Compressed
                                        @elseif($activity->type === 'pdf_deleted')
                                            <i class="bi bi-trash text-danger"></i> PDF Deleted
                                        @else
                                            <i class="bi bi-clock-history"></i> {{ $activity->type }}
                                        @endif
                                    </td>
                                    <td><code>{{ $activity->tenant_id }}</code></td>
                                    <td><small>{{ Str::limit($activity->details ?? 'N/A', 40) }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $activity->status === 'success' ? 'success' : 'secondary' }}">
                                            {{ $activity->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Activity Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: @json($activityData['labels']),
        datasets: [{
            label: 'PDFs Generated',
            data: @json($activityData['data']),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Auto-refresh every 30 seconds
let refreshInterval;
function refreshMonitoring() {
    document.getElementById('lastUpdate').textContent = new Date().toLocaleString();
    location.reload();
}

// Optional: Enable auto-refresh
// refreshInterval = setInterval(refreshMonitoring, 30000);
</script>
@endpush
@endsection
