@extends('layouts.admin')

@section('title', 'Room ' . $room->room_number . ' — ' . $property->property_name)

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
@php
    $activeCount = $activeAssignments->count();
    $slots       = $room->capacity - $activeCount;
@endphp

<div class="row">
    <div class="col-12 grid-margin">

        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Properties</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.rooms.index', $property) }}">{{ $property->property_name }}</a></li>
                <li class="breadcrumb-item active">Room {{ $room->room_number }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold mb-0">Room {{ $room->room_number }}</h4>
                <small class="text-muted">
                    {{ $property->property_name }} &mdash;
                    <span class="{{ $slots <= 0 ? 'text-danger' : ($activeCount > 0 ? 'text-warning' : 'text-success') }} font-weight-bold">
                        {{ $activeCount }}/{{ $room->capacity }} occupied
                    </span>
                </small>
            </div>
            <a href="{{ route('properties.rooms.index', $property) }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">

            {{-- LEFT: Room Details --}}
            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold mb-3">
                            <i class="mdi mdi-door me-1 text-primary"></i> Room Details
                        </h6>
                        <form method="POST" action="{{ route('properties.rooms.update', [$property, $room]) }}">
                            @csrf @method('PUT')
                            @include('rooms.form')
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-content-save me-1"></i> Update Room
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Tenants Panel --}}
            <div class="col-lg-7 mb-4">

                {{-- Current Tenants --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold mb-3">
                            <i class="mdi mdi-account-group me-1 text-success"></i>
                            Current Tenants
                            <span class="badge {{ $slots <= 0 ? 'badge-danger' : 'badge-success' }} ms-1">
                                {{ $activeCount }}/{{ $room->capacity }}
                            </span>
                        </h6>

                        @forelse($activeAssignments as $assignment)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center gap-3">
                                    @if($assignment->tenant->photo)
                                        <img src="{{ Storage::url($assignment->tenant->photo) }}"
                                             class="rounded-circle" width="44" height="44"
                                             style="object-fit:cover;" alt="">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                             style="width:44px;height:44px;">
                                            <i class="mdi mdi-account text-muted" style="font-size:1.4rem;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $assignment->tenant->name }}</div>
                                        <a href="https://wa.me/{{ preg_replace('/\D/','',$assignment->tenant->mobile) }}"
                                           target="_blank" class="text-success small">
                                            <i class="mdi mdi-whatsapp"></i> {{ $assignment->tenant->mobile }}
                                        </a>
                                        <div class="text-muted small">
                                            ₹{{ number_format($assignment->rent_amount, 0) }}/mo
                                            &nbsp;|&nbsp; Since {{ $assignment->start_date->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-warning"
                                            onclick="openEditTenant(
                                                {{ $assignment->tenant->id }},
                                                '{{ addslashes($assignment->tenant->name) }}',
                                                '{{ $assignment->tenant->mobile }}',
                                                '{{ $assignment->tenant->email }}',
                                                '{{ $assignment->tenant->id_proof_number }}',
                                                '{{ addslashes($assignment->tenant->address ?? '') }}'
                                            )" title="Edit Tenant">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <form method="POST"
                                          action="{{ route('room.tenants.vacate', [$property, $room, $assignment]) }}"
                                          onsubmit="return confirm('Vacate {{ $assignment->tenant->name }}?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Vacate">
                                            <i class="mdi mdi-home-remove"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0">
                                <i class="mdi mdi-account-off" style="font-size:1.5rem;"></i><br>
                                No tenants assigned yet.
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Add Tenant --}}
                @if($slots > 0)
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold mb-3">
                            <i class="mdi mdi-account-plus me-1 text-primary"></i>
                            Add Tenant
                            <small class="text-muted font-weight-normal">({{ $slots }} slot(s) free)</small>
                        </h6>

                        <form method="POST"
                              action="{{ route('room.tenants.store', [$property, $room]) }}"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tenant_mode" id="tenant_mode" value="new">

                            {{-- ① Assignment Details — always visible at top --}}
                            <div class="bg-light rounded p-3 mb-3">
                                <p class="small font-weight-bold text-muted mb-2">
                                    <i class="mdi mdi-clipboard-text me-1"></i> Assignment Details
                                </p>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group mb-0">
                                            <label class="small mb-1">Rent (₹) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control form-control-sm"
                                                   name="rent_amount"
                                                   value="{{ old('rent_amount', $room->rent_amount) }}"
                                                   min="0" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group mb-0">
                                            <label class="small mb-1">Meter Start</label>
                                            <input type="number" class="form-control form-control-sm"
                                                   name="electricity_meter_start"
                                                   value="{{ old('electricity_meter_start', 0) }}"
                                                   min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group mb-0">
                                            <label class="small mb-1">Move-in Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-sm"
                                                   name="start_date"
                                                   value="{{ old('start_date', date('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ② New / Existing toggle --}}
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="mode_toggle" id="mode_new" checked>
                                <label class="btn btn-outline-primary btn-sm" for="mode_new"
                                       onclick="switchMode('new')">
                                    <i class="mdi mdi-account-plus me-1"></i> New Tenant
                                </label>
                                <input type="radio" class="btn-check" name="mode_toggle" id="mode_existing">
                                <label class="btn btn-outline-secondary btn-sm" for="mode_existing"
                                       onclick="switchMode('existing')">
                                    <i class="mdi mdi-account-search me-1"></i> Existing Tenant
                                    @if($unassignedTenants->count())
                                        <span class="badge badge-secondary ms-1">{{ $unassignedTenants->count() }}</span>
                                    @endif
                                </label>
                            </div>

                            {{-- ③ New Tenant Fields --}}
                            <div id="newTenantFields">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="name" placeholder="Tenant name"
                                                   value="{{ old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small">Mobile <span class="text-danger">*</span> <small class="text-muted">(WhatsApp)</small></label>
                                            <input type="tel" class="form-control form-control-sm"
                                                   name="mobile" placeholder="9876543210"
                                                   value="{{ old('mobile') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small">Email</label>
                                            <input type="email" class="form-control form-control-sm"
                                                   name="email" placeholder="optional"
                                                   value="{{ old('email') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small">ID Proof No.</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="id_proof_number" placeholder="Aadhar / PAN"
                                                   value="{{ old('id_proof_number') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="small">Address</label>
                                    <textarea class="form-control form-control-sm" name="address"
                                              rows="2" placeholder="Permanent address">{{ old('address') }}</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="form-label small">Photo</label>
                                        <div class="upload-box border rounded p-2 text-center"
                                             onclick="document.getElementById('nt_photo').click()"
                                             style="cursor:pointer;min-height:80px;">
                                            <img id="nt_photo_preview" class="d-none img-fluid rounded mb-1"
                                                 style="max-height:60px;object-fit:cover;">
                                            <div id="nt_photo_ph">
                                                <i class="mdi mdi-camera text-muted" style="font-size:1.5rem;"></i>
                                                <p class="text-muted mb-0" style="font-size:10px;">Click to upload</p>
                                            </div>
                                        </div>
                                        <input type="file" id="nt_photo" name="photo" accept="image/*" class="d-none"
                                               onchange="previewImg(this,'nt_photo_preview','nt_photo_ph')">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">ID Front</label>
                                        <div class="upload-box border rounded p-2 text-center"
                                             onclick="document.getElementById('nt_id_front').click()"
                                             style="cursor:pointer;min-height:80px;">
                                            <img id="nt_id_front_preview" class="d-none img-fluid rounded mb-1"
                                                 style="max-height:60px;object-fit:cover;">
                                            <div id="nt_id_front_ph">
                                                <i class="mdi mdi-card-account-details text-muted" style="font-size:1.5rem;"></i>
                                                <p class="text-muted mb-0" style="font-size:10px;">Click to upload</p>
                                            </div>
                                        </div>
                                        <input type="file" id="nt_id_front" name="id_proof" accept="image/*,.pdf" class="d-none"
                                               onchange="previewImg(this,'nt_id_front_preview','nt_id_front_ph')">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">ID Back</label>
                                        <div class="upload-box border rounded p-2 text-center"
                                             onclick="document.getElementById('nt_id_back').click()"
                                             style="cursor:pointer;min-height:80px;">
                                            <img id="nt_id_back_preview" class="d-none img-fluid rounded mb-1"
                                                 style="max-height:60px;object-fit:cover;">
                                            <div id="nt_id_back_ph">
                                                <i class="mdi mdi-card-account-details-outline text-muted" style="font-size:1.5rem;"></i>
                                                <p class="text-muted mb-0" style="font-size:10px;">Click to upload</p>
                                            </div>
                                        </div>
                                        <input type="file" id="nt_id_back" name="id_proof_back" accept="image/*,.pdf" class="d-none"
                                               onchange="previewImg(this,'nt_id_back_preview','nt_id_back_ph')">
                                    </div>
                                </div>
                            </div>

                            {{-- ④ Existing Tenant Fields --}}
                            <div id="existingTenantFields" class="d-none">
                                @if($unassignedTenants->count())
                                    <div class="form-group">
                                        <label class="small">Select Tenant <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm" name="tenant_id">
                                            <option value="">-- Choose Tenant --</option>
                                            @foreach($unassignedTenants as $t)
                                                <option value="{{ $t->id }}">
                                                    {{ $t->name }} — {{ $t->mobile }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="alert alert-info py-2 small mb-0">
                                        No unassigned tenants. <a href="{{ route('tenants.create') }}">Create one</a> first.
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-success btn-sm mt-3 w-100">
                                <i class="mdi mdi-account-plus me-1"></i> Add Tenant to Room
                            </button>
                        </form>
                    </div>
                </div>
                @else
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert me-1"></i>
                        Room is full ({{ $room->capacity }}/{{ $room->capacity }}). Vacate a tenant to add more.
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

{{-- Edit Tenant Modal --}}
<div class="modal fade" id="editTenantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Edit Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editTenantForm" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="et_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="mobile" id="et_mobile" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="et_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID Proof No.</label>
                                <input type="text" class="form-control" name="id_proof_number" id="et_id_proof_number">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="et_address" rows="2"></textarea>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label class="form-label small">Replace Photo</label>
                            <input type="file" class="form-control form-control-sm" name="photo" accept="image/*">
                        </div>
                        <div class="col-4">
                            <label class="form-label small">Replace ID Front</label>
                            <input type="file" class="form-control form-control-sm" name="id_proof" accept="image/*,.pdf">
                        </div>
                        <div class="col-4">
                            <label class="form-label small">Replace ID Back</label>
                            <input type="file" class="form-control form-control-sm" name="id_proof_back" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-content-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchMode(mode) {
    document.getElementById('tenant_mode').value = mode;
    document.getElementById('newTenantFields').classList.toggle('d-none', mode !== 'new');
    document.getElementById('existingTenantFields').classList.toggle('d-none', mode !== 'existing');
}

function openEditTenant(id, name, mobile, email, idProof, address) {
    const base = "{{ url('properties/' . $property->id . '/rooms/' . $room->id . '/tenants') }}";
    document.getElementById('editTenantForm').action       = base + '/' + id;
    document.getElementById('et_name').value               = name;
    document.getElementById('et_mobile').value             = mobile;
    document.getElementById('et_email').value              = email || '';
    document.getElementById('et_id_proof_number').value    = idProof || '';
    document.getElementById('et_address').value            = address || '';
    new bootstrap.Modal(document.getElementById('editTenantModal')).show();
}

function previewImg(input, previewId, placeholderId) {
    const preview = document.getElementById(previewId);
    const ph      = document.getElementById(placeholderId);
    const file    = input.files[0];
    if (!file) return;
    if (file.type === 'application/pdf') {
        ph.innerHTML = '<i class="mdi mdi-file-pdf text-danger" style="font-size:1.5rem;"></i>'
                     + '<p style="font-size:10px;" class="mb-0">' + file.name + '</p>';
        ph.classList.remove('d-none');
        preview.classList.add('d-none');
        return;
    }
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        ph.classList.add('d-none');
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
