@extends('layouts.admin')

@section('title', 'My Properties')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold mb-0">My Properties</h4>
            <a href="{{ route('properties.create') }}" class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i> Add Property
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Desktop: DataTable --}}
        <div class="card d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="propertiesTable" class="table table-striped table-borderless">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Property Name</th>
                                <th>Type</th>
                                <th>Total Rooms</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($properties as $property)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $property->property_name }}</td>
                                <td><span class="badge badge-info text-capitalize">{{ $property->property_type ?? 'N/A' }}</span></td>
                                <td>{{ $property->total_rooms }}</td>
                                <td>{{ $property->city }}</td>
                                <td>{{ $property->state }}</td>
                                <td>
                                    <a href="{{ route('properties.rooms.index', $property) }}"
                                       class="btn btn-sm btn-success me-1" title="Manage Rooms">
                                        <i class="mdi mdi-door"></i>
                                    </a>
                                    <a href="{{ route('properties.edit', $property) }}"
                                       class="btn btn-sm btn-warning me-1" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('properties.destroy', $property) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this property?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
            @forelse($properties as $property)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="font-weight-bold mb-1">{{ $property->property_name }}</h6>
                            <span class="badge badge-info text-capitalize mb-2">{{ $property->property_type ?? 'N/A' }}</span>
                            <p class="text-muted small mb-1">
                                <i class="mdi mdi-home me-1"></i> {{ $property->total_rooms }} Rooms
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="mdi mdi-map-marker me-1"></i>
                                {{ $property->city }}, {{ $property->state }}
                                @if($property->pincode) — {{ $property->pincode }} @endif
                            </p>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('properties.rooms.index', $property) }}"
                               class="btn btn-sm btn-success" title="Rooms">
                                <i class="mdi mdi-door"></i>
                            </a>
                            <a href="{{ route('properties.edit', $property) }}"
                               class="btn btn-sm btn-warning">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <form action="{{ route('properties.destroy', $property) }}" method="POST"
                                  onsubmit="return confirm('Delete this property?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
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
                    <i class="mdi mdi-home-outline" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0">No properties found. <a href="{{ route('properties.create') }}">Add one</a></p>
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
        $('#propertiesTable').DataTable({
            pageLength: 10,
            columnDefs: [{ orderable: false, targets: 6 }]
        });
    });
</script>
@endpush
