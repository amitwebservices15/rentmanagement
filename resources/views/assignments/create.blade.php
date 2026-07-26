@extends('layouts.admin')

@section('title', 'Assign Room — ' . $tenant->name)

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-lg-7 grid-margin mx-auto">

        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('tenants.index') }}">Tenants</a></li>
                <li class="breadcrumb-item active">Assign Room</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold mb-0">Assign Room</h4>
                <small class="text-muted">
                    <i class="mdi mdi-account me-1"></i>{{ $tenant->name }}
                    &nbsp;|&nbsp;
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $tenant->mobile) }}"
                       target="_blank" class="text-success">
                        <i class="mdi mdi-whatsapp me-1"></i>{{ $tenant->mobile }}
                    </a>
                </small>
            </div>
            <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('assignments.store', $tenant) }}">
                    @csrf

                    {{-- Property selector --}}
                    <div class="form-group">
                        <label for="property_id">Select Property <span class="text-danger">*</span></label>
                        <select class="form-select" id="property_id" required>
                            <option value="">-- Choose Property --</option>
                            @foreach($properties as $property)
                                @php
                                    $availableRooms = $property->rooms->filter(fn($r) => $r->active_count < $r->capacity)->count();
                                @endphp
                                <option value="{{ $property->id }}">
                                    {{ $property->property_name }} — {{ $property->city }}
                                    ({{ $availableRooms }} room(s) with space)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Room selector --}}
                    <div class="form-group">
                        <label for="room_id">Select Room <span class="text-danger">*</span></label>
                        <select class="form-select @error('room_id') is-invalid @enderror"
                                id="room_id" name="room_id" required>
                            <option value="">-- Choose Property first --</option>
                            @foreach($properties as $property)
                                @foreach($property->rooms as $room)
                                    @php
                                        $slots     = $room->capacity - $room->active_count;
                                        $isFull    = $slots <= 0;
                                    @endphp
                                    <option value="{{ $room->id }}"
                                            data-property="{{ $property->id }}"
                                            data-rent="{{ $room->rent_amount }}"
                                            data-full="{{ $isFull ? '1' : '0' }}"
                                            {{ old('room_id') == $room->id ? 'selected' : '' }}
                                            {{ $isFull ? 'disabled' : '' }}>
                                        Room {{ $room->room_number }}
                                        @if($room->floor) ({{ $room->floor }}) @endif
                                        — {{ $room->active_count }}/{{ $room->capacity }} occupied
                                        — ₹{{ number_format($room->rent_amount, 0) }}/mo
                                        {{ $isFull ? '[FULL]' : "({$slots} slot(s) free)" }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('room_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        <small class="text-muted">Rooms marked [FULL] have reached their capacity.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rent_amount">
                                    Rent Amount (₹) <span class="text-danger">*</span>
                                    <small class="text-muted">override if needed</small>
                                </label>
                                <input type="number"
                                       class="form-control @error('rent_amount') is-invalid @enderror"
                                       id="rent_amount" name="rent_amount"
                                       value="{{ old('rent_amount') }}"
                                       min="0" step="0.01"
                                       placeholder="Auto-filled from room" required>
                                @error('rent_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="electricity_meter_start">Electricity Meter Start</label>
                                <input type="number"
                                       class="form-control @error('electricity_meter_start') is-invalid @enderror"
                                       id="electricity_meter_start" name="electricity_meter_start"
                                       value="{{ old('electricity_meter_start', 0) }}"
                                       min="0" step="0.01"
                                       placeholder="Current meter reading">
                                @error('electricity_meter_start') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Move-in Date <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date"
                               value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="mdi mdi-home-plus me-1"></i> Assign Room
                        </button>
                        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const propertySelect = document.getElementById('property_id');
    const roomSelect     = document.getElementById('room_id');
    const rentInput      = document.getElementById('rent_amount');
    const allOptions     = Array.from(roomSelect.querySelectorAll('option[data-property]'));

    function filterRooms() {
        const pid = propertySelect.value;
        roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
        if (!pid) return;

        allOptions.forEach(o => {
            if (o.dataset.property === pid) {
                roomSelect.appendChild(o.cloneNode(true));
            }
        });
    }

    propertySelect.addEventListener('change', filterRooms);

    roomSelect.addEventListener('change', function () {
        const selected = roomSelect.options[roomSelect.selectedIndex];
        if (selected && selected.dataset.rent) {
            rentInput.value = selected.dataset.rent;
        }
    });
});
</script>
@endpush
