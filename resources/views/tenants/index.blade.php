@extends('layouts.admin')

@section('title', 'Tenants')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold mb-0">Tenants</h4>
            <a href="{{ route('tenants.create') }}" class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i> Add Tenant
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Desktop: DataTable --}}
        <div class="card d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tenantsTable" class="table table-striped table-borderless">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Current Room</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenants as $tenant)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $tenant->name }}</strong></td>
                                <td>
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $tenant->mobile) }}"
                                       target="_blank" class="text-success">
                                        <i class="mdi mdi-whatsapp me-1"></i>{{ $tenant->mobile }}
                                    </a>
                                </td>
                                <td>{{ $tenant->email ?? '—' }}</td>
                                <td>
                                    @if($tenant->activeAssignment)
                                        <span class="badge badge-info">
                                            {{ $tenant->activeAssignment->room->property->property_name }}
                                            — Room {{ $tenant->activeAssignment->room->room_number }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tenant->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                        @if(!$tenant->activeAssignment)
                                            <small class="text-muted d-block">No room assigned</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!$tenant->activeAssignment)
                                        <a href="{{ route('assignments.create', $tenant) }}"
                                           class="btn btn-sm btn-success me-1" title="Assign Room">
                                            <i class="mdi mdi-home-plus"></i>
                                        </a>
                                    @else
                                        <form action="{{ route('assignments.vacate', $tenant->activeAssignment) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Vacate {{ $tenant->name }} from room?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-warning me-1" title="Vacate">
                                                <i class="mdi mdi-home-remove"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($tenant->phone)
                                        <a href="{{ route('owner.whatsapp.compose', $tenant) }}"
                                           class="btn btn-sm btn-info me-1" title="Send WhatsApp Message">
                                            <i class="mdi mdi-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('tenants.edit', $tenant) }}"
                                       class="btn btn-sm btn-warning me-1" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('tenants.destroy', $tenant) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete {{ $tenant->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Mobile: Card List --}}
        <div class="d-md-none">
            @forelse($tenants as $tenant)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="font-weight-bold mb-0">{{ $tenant->name }}</h6>
                                @if($tenant->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </div>
                            @if($tenant->status === 'inactive' && !$tenant->activeAssignment)
                                <small class="text-muted">No room assigned</small>
                            @endif
                            <p class="small mb-1">
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $tenant->mobile) }}"
                                   target="_blank" class="text-success">
                                    <i class="mdi mdi-whatsapp me-1"></i>{{ $tenant->mobile }}
                                </a>
                            </p>
                            @if($tenant->email)
                                <p class="text-muted small mb-1">
                                    <i class="mdi mdi-email me-1"></i>{{ $tenant->email }}
                                </p>
                            @endif
                            <p class="small mb-0">
                                @if($tenant->activeAssignment)
                                    <span class="badge badge-info">
                                        {{ $tenant->activeAssignment->room->property->property_name }}
                                        — Room {{ $tenant->activeAssignment->room->room_number }}
                                    </span>
                                @else
                                    <span class="text-muted"><i class="mdi mdi-home-off me-1"></i>Not assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="d-flex flex-column gap-1 ms-2">
                            @if(!$tenant->activeAssignment)
                                <a href="{{ route('assignments.create', $tenant) }}"
                                   class="btn btn-sm btn-success" title="Assign Room">
                                    <i class="mdi mdi-home-plus"></i>
                                </a>
                            @else
                                <form action="{{ route('assignments.vacate', $tenant->activeAssignment) }}"
                                      method="POST"
                                      onsubmit="return confirm('Vacate {{ $tenant->name }}?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-warning" title="Vacate">
                                        <i class="mdi mdi-home-remove"></i>
                                    </button>
                                </form>
                            @endif
                            @if($tenant->phone)
                                <a href="{{ route('owner.whatsapp.compose', $tenant) }}" class="btn btn-sm btn-info">
                                    <i class="mdi mdi-whatsapp"></i>
                                </a>
                            @endif
                            <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-sm btn-warning">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <form action="{{ route('tenants.destroy', $tenant) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $tenant->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="mdi mdi-account-group-outline" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0">No tenants yet. <a href="{{ route('tenants.create') }}">Add one</a></p>
                </div>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css') }}">
<script src="{{ asset('assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#tenantsTable').DataTable({
            pageLength: 10,
            columnDefs: [{ orderable: false, targets: 6 }]
        });
    });
</script>
@endpush
