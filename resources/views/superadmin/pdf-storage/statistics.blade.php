@extends('layouts.admin')

@section('title', 'PDF Storage Statistics')

@push('styles')
<style>
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.chart-container {
    position: relative;
    height: 400px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">📊 Storage Statistics & Analytics</h1>
                <div>
                    <button class="btn btn-outline-primary" onclick="refreshCharts()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                    <a href="{{ route('superadmin.pdf-storage.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total PDFs</h6>
                            <h2 class="mb-0" id="totalPdfs">{{ number_format($stats['total']['pdfs']) }}</h2>
                            <small class="text-muted">Certificates</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-file-earmark-pdf" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Storage</h6>
                            <h2 class="mb-0" id="totalStorage">{{ $stats['total']['size_formatted'] }}</h2>
                            <small class="text-muted">Disk usage</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-hdd" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Compressed</h6>
                            <h2 class="mb-0" id="compressedPdfs">{{ number_format($stats['compression']['compressed_count']) }}</h2>
                            <small class="text-success">{{ $stats['compression']['avg_ratio'] }}% avg savings</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-file-zip" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Monthly Cost</h6>
                            <h2 class="mb-0" id="monthlyCost">${{ number_format($stats['cost']['monthly'], 2) }}</h2>
                            <small class="text-muted">Estimated S3 cost</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-cash-stack" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📈 Storage Growth Over Time</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="storageGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🥧 Storage by Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="tenantPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 PDF Generation Trends</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="generationTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🗜️ Compression Savings</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="compressionSavingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Tenants Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🏆 Top 10 Tenants by Storage Usage</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tenant ID</th>
                                    <th class="text-end">PDF Count</th>
                                    <th class="text-end">Total Size</th>
                                    <th class="text-end">Compressed</th>
                                    <th class="text-end">Savings</th>
                                    <th class="text-end">Monthly Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['top_tenants'] as $index => $tenant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $tenant->tenant_id }}</code></td>
                                    <td class="text-end">{{ number_format($tenant->pdf_count) }}</td>
                                    <td class="text-end"><strong>{{ formatBytes($tenant->total_bytes) }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $tenant->compressed_count > 0 ? 'success' : 'secondary' }}">
                                            {{ number_format($tenant->compressed_count) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($tenant->total_savings > 0)
                                            <span class="text-success">{{ formatBytes($tenant->total_savings) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">${{ number_format($tenant->monthly_cost, 2) }}</td>
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
// Chart Data from Backend
const chartData = @json($chartData);

// Chart Colors
const colors = {
    primary: '#0d6efd',
    success: '#198754',
    info: '#0dcaf0',
    warning: '#ffc107',
    danger: '#dc3545',
    purple: '#6f42c1',
    orange: '#fd7e14',
    teal: '#20c997'
};

// Storage Growth Chart (Line)
const storageGrowthCtx = document.getElementById('storageGrowthChart').getContext('2d');
const storageGrowthChart = new Chart(storageGrowthCtx, {
    type: 'line',
    data: {
        labels: chartData.monthly.labels,
        datasets: [{
            label: 'Storage Size (GB)',
            data: chartData.monthly.storage,
            borderColor: colors.primary,
            backgroundColor: colors.primary + '20',
            fill: true,
            tension: 0.4
        }, {
            label: 'PDF Count',
            data: chartData.monthly.count,
            borderColor: colors.success,
            backgroundColor: colors.success + '20',
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Storage (GB)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'PDF Count'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Tenant Pie Chart
const tenantPieCtx = document.getElementById('tenantPieChart').getContext('2d');
const tenantPieChart = new Chart(tenantPieCtx, {
    type: 'doughnut',
    data: {
        labels: chartData.tenants.labels,
        datasets: [{
            data: chartData.tenants.sizes,
            backgroundColor: [
                colors.primary,
                colors.success,
                colors.warning,
                colors.info,
                colors.danger,
                colors.purple,
                colors.orange,
                colors.teal
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Generation Trends Chart (Bar)
const generationTrendsCtx = document.getElementById('generationTrendsChart').getContext('2d');
const generationTrendsChart = new Chart(generationTrendsCtx, {
    type: 'bar',
    data: {
        labels: chartData.monthly.labels,
        datasets: [{
            label: 'PDFs Generated',
            data: chartData.monthly.generated,
            backgroundColor: colors.info
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Compression Savings Chart
const compressionSavingsCtx = document.getElementById('compressionSavingsChart').getContext('2d');
const compressionSavingsChart = new Chart(compressionSavingsCtx, {
    type: 'bar',
    data: {
        labels: ['Original Size', 'After Compression', 'Savings'],
        datasets: [{
            label: 'Storage (GB)',
            data: [
                chartData.compression.original_size,
                chartData.compression.compressed_size,
                chartData.compression.savings
            ],
            backgroundColor: [colors.danger, colors.success, colors.warning]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Storage (GB)'
                }
            }
        }
    }
});

// Refresh Charts
function refreshCharts() {
    location.reload();
}
</script>
@endpush
@endsection
