@extends('layouts.admin')

@section('title', 'Rooms — ' . $property->property_name)

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('properties.index') }}">Properties</a>
                </li>
                <li class="breadcrumb-item active">{{ $property->property_name }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold mb-0">Rooms</h4>
                <small class="text-muted">{{ $property->property_name }} &mdash; {{ $property->city }}</small>
            </div>
            <a href="{{ route('properties.rooms.create', $property) }}" class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i> Add Room
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats row --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-tale py-3 px-3">
                    <p class="mb-1 small">Total Rooms</p>
                    <h4 class="mb-0 font-weight-bold">{{ $rooms->count() }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-dark-blue py-3 px-3">
                    <p class="mb-1 small">Active Tenants</p>
                    <h4 class="mb-0 font-weight-bold">{{ $rooms->sum('active_count') }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-light-blue py-3 px-3">
                    <p class="mb-1 small">Free Slots</p>
                    <h4 class="mb-0 font-weight-bold">{{ $rooms->sum('capacity') - $rooms->sum('active_count') }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-light-danger py-3 px-3">
                    <p class="mb-1 small">Total Capacity</p>
                    <h4 class="mb-0 font-weight-bold">{{ $rooms->sum('capacity') }}</h4>
                </div>
            </div>
        </div>

        {{-- Desktop: DataTable --}}
        <div class="card d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="roomsTable" class="table table-striped table-borderless">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room No.</th>
                                <th>Floor</th>
                                <th>Occupancy</th>
                                <th>Rent (₹)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $room->room_number }}</strong></td>
                                <td>{{ $room->floor ?? '—' }}</td>
                                <td>{{ $room->active_count ?? 0 }}/{{ $room->capacity }}</td>
                                <td>₹{{ number_format($room->rent_amount, 2) }}</td>
                                <td>
                                    @php $slots = $room->capacity - ($room->active_count ?? 0); @endphp
                                    @if($slots <= 0)
                                        <span class="badge badge-danger">Full</span>
                                    @elseif($room->active_count > 0)
                                        <span class="badge badge-warning">Partial ({{ $slots }} free)</span>
                                    @else
                                        <span class="badge badge-success">Available</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($room->active_count ?? 0) > 0)
                                    <a href="{{ route('rent.create', [$property, $room]) }}"
                                       class="btn btn-sm btn-success me-1" title="Generate Rent">
                                        <i class="mdi mdi-receipt"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('rent.index', [$property, $room]) }}"
                                       class="btn btn-sm btn-info me-1" title="View Bills">
                                        <i class="mdi mdi-format-list-bulleted"></i>
                                    </a>
                                    <a href="{{ route('properties.rooms.edit', [$property, $room]) }}"
                                       class="btn btn-sm btn-warning me-1" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('properties.rooms.destroy', [$property, $room]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete Room {{ $room->room_number }}?')">
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
            @forelse($rooms as $room)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="font-weight-bold mb-0">Room {{ $room->room_number }}</h6>
                                @php $slots = $room->capacity - ($room->active_count ?? 0); @endphp
                            @if($slots <= 0)
                                <span class="badge badge-danger">Full</span>
                            @elseif($room->active_count > 0)
                                <span class="badge badge-warning">Partial</span>
                            @else
                                <span class="badge badge-success">Available</span>
                            @endif
                            </div>
                            @if($room->floor)
                                <p class="text-muted small mb-1">
                                    <i class="mdi mdi-layers me-1"></i> Floor: {{ $room->floor }}
                                </p>
                            @endif
                            <p class="text-muted small mb-1">
                                <i class="mdi mdi-account-group me-1"></i>
                                {{ $room->active_count ?? 0 }}/{{ $room->capacity }} occupied
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="mdi mdi-currency-inr me-1"></i> Rent: ₹{{ number_format($room->rent_amount, 2) }}/month
                            </p>
                            @if($room->description)
                                <p class="text-muted small mt-1 mb-0">{{ $room->description }}</p>
                            @endif
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @if(($room->active_count ?? 0) > 0)
                            <a href="{{ route('rent.create', [$property, $room]) }}"
                               class="btn btn-sm btn-success" title="Generate Rent">
                                <i class="mdi mdi-receipt"></i>
                            </a>
                            @endif
                            <a href="{{ route('rent.index', [$property, $room]) }}"
                               class="btn btn-sm btn-info" title="View Bills">
                                <i class="mdi mdi-format-list-bulleted"></i>
                            </a>
                            <a href="{{ route('properties.rooms.edit', [$property, $room]) }}"
                               class="btn btn-sm btn-warning">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <form action="{{ route('properties.rooms.destroy', [$property, $room]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete Room {{ $room->room_number }}?')">
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
                    <i class="mdi mdi-door-open" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0">No rooms yet. <a href="{{ route('properties.rooms.create', $property) }}">Add one</a></p>
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
        $('#roomsTable').DataTable({
            pageLength: 10,
            columnDefs: [{ orderable: false, targets: 6 }]
        });
    });
</script>
@endpush
