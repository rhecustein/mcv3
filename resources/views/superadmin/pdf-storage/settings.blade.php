@extends('layouts.admin')

@section('title', 'PDF Storage Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">⚙️ PDF Storage Settings</h1>
                <a href="{{ route('superadmin.pdf-storage.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
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

    {{-- Global Settings --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">🌍 Global Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.pdf-storage.update-settings') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Auto-Delete Configuration</label>

                                <div class="mb-3">
                                    <label class="form-label">Delete PDFs older than (days)</label>
                                    <input type="number" name="auto_delete_days" class="form-control"
                                           value="{{ old('auto_delete_days', $globalSettings->auto_delete_days) }}"
                                           min="7" max="3650" required>
                                    <small class="text-muted">Range: 7-3650 days (1 week - 10 years)</small>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="auto_delete_enabled" value="1"
                                           class="form-check-input" id="autoDeleteEnabled"
                                           {{ old('auto_delete_enabled', $globalSettings->auto_delete_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autoDeleteEnabled">
                                        Enable auto-delete
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Archive Configuration</label>

                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="archive_before_delete" value="1"
                                           class="form-check-input" id="archiveBeforeDelete"
                                           {{ old('archive_before_delete', $globalSettings->archive_before_delete) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="archiveBeforeDelete">
                                        Archive before delete
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Archive Storage</label>
                                    <select name="archive_storage" class="form-select" required>
                                        <option value="s3" {{ old('archive_storage', $globalSettings->archive_storage) == 's3' ? 'selected' : '' }}>
                                            Amazon S3
                                        </option>
                                        <option value="glacier" {{ old('archive_storage', $globalSettings->archive_storage) == 'glacier' ? 'selected' : '' }}>
                                            Amazon Glacier (cheaper)
                                        </option>
                                        <option value="local" {{ old('archive_storage', $globalSettings->archive_storage) == 'local' ? 'selected' : '' }}>
                                            Local Storage
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Compression Configuration</label>

                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="compression_enabled" value="1"
                                           class="form-check-input" id="compressionEnabled"
                                           {{ old('compression_enabled', $globalSettings->compression_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="compressionEnabled">
                                        Enable PDF compression
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Compress PDFs older than (days)</label>
                                    <input type="number" name="compression_days" class="form-control"
                                           value="{{ old('compression_days', $globalSettings->compression_days) }}"
                                           min="1" placeholder="30">
                                    <small class="text-muted">Leave empty to disable. Recommended: 30 days</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Storage Alerts</label>

                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="alert_enabled" value="1"
                                           class="form-check-input" id="alertEnabled"
                                           {{ old('alert_enabled', $globalSettings->alert_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="alertEnabled">
                                        Enable storage alerts
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alert Threshold (%)</label>
                                    <input type="number" name="alert_threshold_percent" class="form-control"
                                           value="{{ old('alert_threshold_percent', $globalSettings->alert_threshold_percent) }}"
                                           min="1" max="100" required>
                                    <small class="text-muted">Alert when storage usage exceeds this percentage</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alert Email</label>
                                    <input type="email" name="alert_email" class="form-control"
                                           value="{{ old('alert_email', $globalSettings->alert_email) }}"
                                           placeholder="admin@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Panel --}}
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">ℹ️ Information</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Current Configuration</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Auto-delete:</strong>
                            <span class="badge {{ $globalSettings->auto_delete_enabled ? 'bg-success' : 'bg-secondary' }}">
                                {{ $globalSettings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </li>
                        <li class="mb-2">
                            <strong>Delete after:</strong> {{ $globalSettings->auto_delete_days }} days
                        </li>
                        <li class="mb-2">
                            <strong>Archive:</strong>
                            {{ $globalSettings->archive_before_delete ? 'Enabled' : 'Disabled' }}
                        </li>
                        <li class="mb-2">
                            <strong>Archive storage:</strong> {{ ucfirst($globalSettings->archive_storage) }}
                        </li>
                        <li class="mb-2">
                            <strong>Compression:</strong>
                            <span class="badge {{ $globalSettings->compression_enabled ? 'bg-success' : 'bg-secondary' }}">
                                {{ $globalSettings->compression_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold">Recommendations</h6>
                    <ul class="small">
                        <li>Enable auto-delete to prevent storage bloat</li>
                        <li>Always archive before delete for compliance</li>
                        <li>Use Glacier for long-term cold storage (cheapest)</li>
                        <li>Compress PDFs after 30 days to save 85% space</li>
                        <li>Set alerts to monitor storage usage</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold">Storage Costs (AWS)</h6>
                    <ul class="small">
                        <li><strong>S3 Standard:</strong> $0.023/GB/month</li>
                        <li><strong>S3 Glacier:</strong> $0.004/GB/month (83% cheaper)</li>
                        <li><strong>Compression:</strong> 85-90% size reduction</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
