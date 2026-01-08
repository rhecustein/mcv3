@extends('layouts.admin')

@section('title', 'PDF Storage Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">⚙️ PDF Storage Settings</h1>
                <a href="{{ route('admin.pdf-storage.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if($usingGlobal)
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        <strong>Note:</strong> Your account is using global system settings. These settings are managed by the system administrator.
    </div>
    @else
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        <strong>Custom Settings:</strong> Your account has custom storage settings configured by the administrator.
    </div>
    @endif

    {{-- Settings Display (Read-Only) --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Current Storage Settings</h5>
                </div>
                <div class="card-body">

                    {{-- Auto-Delete Settings --}}
                    <h6 class="text-primary mb-3">🗑️ Auto-Delete Configuration</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Auto-Delete Status</strong></label>
                                <div>
                                    <span class="badge {{ $settings->auto_delete_enabled ? 'bg-success' : 'bg-secondary' }} fs-6">
                                        {{ $settings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Delete After</strong></label>
                                <div>
                                    <span class="fs-5">{{ $settings->auto_delete_days }} days</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Archive Settings --}}
                    <h6 class="text-primary mb-3">📦 Archive Configuration</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Archive Before Delete</strong></label>
                                <div>
                                    <span class="badge {{ $settings->archive_before_delete ? 'bg-info' : 'bg-secondary' }} fs-6">
                                        {{ $settings->archive_before_delete ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Archive Storage</strong></label>
                                <div>
                                    <span class="fs-5">{{ ucfirst($settings->archive_storage) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Compression Settings --}}
                    <h6 class="text-primary mb-3">🗜️ Compression Configuration</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Compression Status</strong></label>
                                <div>
                                    <span class="badge {{ $settings->compression_enabled ? 'bg-success' : 'bg-secondary' }} fs-6">
                                        {{ $settings->compression_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Compress After</strong></label>
                                <div>
                                    <span class="fs-5">{{ $settings->compression_days ?? '-' }} days</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Storage Quota --}}
                    @if($settings->storage_quota_gb)
                    <h6 class="text-primary mb-3">💾 Storage Quota</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Quota Limit</strong></label>
                                <div>
                                    <span class="fs-5">{{ $settings->storage_quota_gb }} GB</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Quota Exceeded Action</strong></label>
                                <div>
                                    <span class="fs-5">{{ ucfirst($settings->quota_exceeded_action ?? 'block') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @endif

                    {{-- Alerts Settings --}}
                    <h6 class="text-primary mb-3">🔔 Storage Alerts</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Alerts Status</strong></label>
                                <div>
                                    <span class="badge {{ $settings->alert_enabled ? 'bg-success' : 'bg-secondary' }} fs-6">
                                        {{ $settings->alert_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Alert Threshold</strong></label>
                                <div>
                                    <span class="fs-5">{{ $settings->alert_threshold_percent }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

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
                    <h6 class="fw-bold">What these settings mean:</h6>

                    <h6 class="mt-3"><i class="bi bi-trash text-danger"></i> Auto-Delete</h6>
                    <p class="small text-muted">
                        PDFs older than the specified days will be automatically deleted to free up storage space.
                    </p>

                    <h6 class="mt-3"><i class="bi bi-archive text-info"></i> Archive</h6>
                    <p class="small text-muted">
                        Before deletion, PDFs are archived to {{ ucfirst($settings->archive_storage) }} storage for compliance and potential restoration.
                    </p>

                    <h6 class="mt-3"><i class="bi bi-file-zip text-success"></i> Compression</h6>
                    <p class="small text-muted">
                        After {{ $settings->compression_days ?? '-' }} days, PDFs are automatically compressed to save storage space (typically 85-90% reduction).
                    </p>

                    @if($settings->storage_quota_gb)
                    <h6 class="mt-3"><i class="bi bi-hdd text-warning"></i> Storage Quota</h6>
                    <p class="small text-muted">
                        Your account has a storage limit of {{ $settings->storage_quota_gb }} GB. When exceeded, new PDFs may be {{ $settings->quota_exceeded_action ?? 'blocked' }}.
                    </p>
                    @endif

                    <hr>

                    <h6 class="fw-bold">Need Changes?</h6>
                    <p class="small text-muted">
                        To modify these settings, please contact your system administrator.
                    </p>

                    <div class="d-grid">
                        <a href="mailto:{{ config('mail.from.address') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope"></i> Contact Admin
                        </a>
                    </div>
                </div>
            </div>

            {{-- Storage Tips --}}
            <div class="card border-success mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">💡 Storage Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Download important PDFs for your records</li>
                        <li>Regularly review and clean old certificates</li>
                        <li>Compression happens automatically - no action needed</li>
                        <li>Archived PDFs can be restored by admin if needed</li>
                        <li>Monitor your storage quota to avoid interruptions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
