@extends('layouts.admin')

@section('title', 'Detail Legacy Result')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-medical me-2"></i>Detail Legacy Result
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('legacy-results.index') }}">Legacy Results</a></li>
                    <li class="breadcrumb-item active">{{ $result->unique_code }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('legacy-results.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            @if(!$result->deleted_at)
                <a href="{{ route('results.download', Crypt::encrypt($result->id)) }}" 
                   class="btn btn-success">
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informasi Surat
                    </h6>
                    <span class="badge {{ $result->type == 'mc' ? 'bg-danger' : 'bg-success' }} fs-6">
                        {{ $result->type == 'mc' ? 'Medical Certificate' : 'Surat Keterangan Sehat' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">Kode Unik</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary fs-6 me-2">{{ $result->unique_code }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="copyToClipboard('{{ $result->unique_code }}')" title="Copy">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">Status</h6>
                            @if($result->deleted_at)
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-trash me-1"></i>Deleted
                                </span>
                            @elseif($result->archived_at)
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-archive me-1"></i>Archived
                                </span>
                            @else
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check me-1"></i>Active
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($result->type == 'mc')
                        <!-- Medical Certificate Details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Tanggal Mulai Sakit</h6>
                                <p class="mb-0">{{ $result->start_date ? \Carbon\Carbon::parse($result->start_date)->format('d F Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Tanggal Selesai Sakit</h6>
                                <p class="mb-0">{{ $result->end_date ? \Carbon\Carbon::parse($result->end_date)->format('d F Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Lama Sakit</h6>
                                <p class="mb-0">
                                    @if($result->start_date && $result->end_date)
                                        {{ \Carbon\Carbon::parse($result->start_date)->diffInDays(\Carbon\Carbon::parse($result->end_date)) + 1 }} hari
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Istirahat</h6>
                                <p class="mb-0">{{ $result->rest_type ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($result->diagnosis && $result->diagnosis->count() > 0)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Diagnosis</h6>
                                @foreach($result->diagnosis as $diagnosis)
                                    <div class="alert alert-light border-start border-4 border-info">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $diagnosis->name }}</strong>
                                                @if($diagnosis->icd)
                                                    <span class="badge bg-secondary ms-2">{{ $diagnosis->icd->code }}</span>
                                                @endif
                                                @if($diagnosis->description)
                                                    <p class="mb-0 mt-2 text-muted">{{ $diagnosis->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- Health Certificate Details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Tanggal Pemeriksaan</h6>
                                <p class="mb-0">{{ $result->date ? \Carbon\Carbon::parse($result->date)->format('d F Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Keperluan</h6>
                                <p class="mb-0">{{ $result->purpose ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($result->vital_signs)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Tanda Vital</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-light p-2 rounded text-center">
                                            <small class="text-muted">Tekanan Darah</small>
                                            <div class="fw-bold">{{ $result->vital_signs['blood_pressure'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-light p-2 rounded text-center">
                                            <small class="text-muted">Nadi</small>
                                            <div class="fw-bold">{{ $result->vital_signs['pulse'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-light p-2 rounded text-center">
                                            <small class="text-muted">Suhu</small>
                                            <div class="fw-bold">{{ $result->vital_signs['temperature'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-light p-2 rounded text-center">
                                            <small class="text-muted">Berat Badan</small>
                                            <div class="fw-bold">{{ $result->vital_signs['weight'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if($result->notes)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Catatan</h6>
                            <div class="alert alert-light">
                                {{ $result->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Patient Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Data Pasien
                    </h6>
                </div>
                <div class="card-body">
                    @if($result->patient)
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-primary text-white mx-auto mb-2" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5 class="mb-1">{{ $result->patient->full_name }}</h5>
                            @if($result->patient->nik)
                                <small class="text-muted">NIK: {{ $result->patient->nik }}</small>
                            @endif
                        </div>

                        <hr>

                        <div class="mb-2">
                            <strong>Tanggal Lahir:</strong><br>
                            <small class="text-muted">
                                {{ $result->patient->birth_date ? \Carbon\Carbon::parse($result->patient->birth_date)->format('d F Y') : 'N/A' }}
                                @if($result->patient->birth_date)
                                    ({{ \Carbon\Carbon::parse($result->patient->birth_date)->age }} tahun)
                                @endif
                            </small>
                        </div>

                        <div class="mb-2">
                            <strong>Jenis Kelamin:</strong><br>
                            <small class="text-muted">{{ $result->patient->gender ?? 'N/A' }}</small>
                        </div>

                        <div class="mb-2">
                            <strong>Alamat:</strong><br>
                            <small class="text-muted">{{ $result->patient->address ?? 'N/A' }}</small>
                        </div>

                        @if($result->patient->phone)
                            <div class="mb-2">
                                <strong>Telepon:</strong><br>
                                <small class="text-muted">{{ $result->patient->phone }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Data pasien tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Doctor Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-md me-2"></i>Data Dokter
                    </h6>
                </div>
                <div class="card-body">
                    @if($result->doctor && $result->doctor->user)
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-success text-white mx-auto mb-2" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-user-md fa-2x"></i>
                            </div>
                            <h5 class="mb-1">{{ $result->doctor->user->name }}</h5>
                            @if($result->doctor->specialist)
                                <small class="text-muted">{{ $result->doctor->specialist }}</small>
                            @endif
                        </div>

                        <hr>

                        @if($result->doctor->license_number)
                            <div class="mb-2">
                                <strong>No. STR:</strong><br>
                                <small class="text-muted">{{ $result->doctor->license_number }}</small>
                            </div>
                        @endif

                        @if($result->doctor->user->email)
                            <div class="mb-2">
                                <strong>Email:</strong><br>
                                <small class="text-muted">{{ $result->doctor->user->email }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-md-slash fa-2x mb-2"></i>
                            <p>Data dokter tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Outlet Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-hospital me-2"></i>Data Outlet
                    </h6>
                </div>
                <div class="card-body">
                    @if($result->outlet)
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-info text-white mx-auto mb-2" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-hospital fa-2x"></i>
                            </div>
                            <h5 class="mb-1">{{ $result->outlet->name }}</h5>
                        </div>

                        <hr>

                        @if($result->outlet->address)
                            <div class="mb-2">
                                <strong>Alamat:</strong><br>
                                <small class="text-muted">{{ $result->outlet->address }}</small>
                            </div>
                        @endif

                        @if($result->outlet->city)
                            <div class="mb-2">
                                <strong>Kota:</strong><br>
                                <small class="text-muted">{{ $result->outlet->city }}</small>
                            </div>
                        @endif

                        @if($result->outlet->phone)
                            <div class="mb-2">
                                <strong>Telepon:</strong><br>
                                <small class="text-muted">{{ $result->outlet->phone }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-hospital-slash fa-2x mb-2"></i>
                            <p>Data outlet tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info me-2"></i>Metadata
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Dibuat:</strong><br>
                        <small class="text-muted">
                            {{ $result->created_at->format('d F Y, H:i') }}
                            ({{ $result->created_at->diffForHumans() }})
                        </small>
                    </div>

                    @if($result->updated_at && $result->updated_at != $result->created_at)
                        <div class="mb-2">
                            <strong>Diperbarui:</strong><br>
                            <small class="text-muted">
                                {{ $result->updated_at->format('d F Y, H:i') }}
                                ({{ $result->updated_at->diffForHumans() }})
                            </small>
                        </div>
                    @endif

                    @if($result->deleted_at)
                        <div class="mb-2">
                            <strong>Dihapus:</strong><br>
                            <small class="text-danger">
                                {{ $result->deleted_at->format('d F Y, H:i') }}
                                ({{ $result->deleted_at->diffForHumans() }})
                            </small>
                        </div>
                    @endif

                    @if($result->legacy_migrated_at)
                        <div class="mb-2">
                            <strong>Migrasi Legacy:</strong><br>
                            <small class="text-info">
                                {{ \Carbon\Carbon::parse($result->legacy_migrated_at)->format('d F Y, H:i') }}
                            </small>
                        </div>
                    @endif

                    @if($result->created_latitude && $result->created_longitude)
                        <div class="mb-2">
                            <strong>Lokasi Dibuat:</strong><br>
                            <small class="text-muted">
                                {{ $result->created_city ?? 'Unknown' }}<br>
                                <a href="https://maps.google.com/?q={{ $result->created_latitude }},{{ $result->created_longitude }}" 
                                   target="_blank" class="text-decoration-none">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $result->created_latitude }}, {{ $result->created_longitude }}
                                </a>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show bg-success text-white" role="alert">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Kode berhasil disalin!
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endpush