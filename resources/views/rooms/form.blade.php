{{-- Shared room form fields --}}

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="room_number">Room Number <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('room_number') is-invalid @enderror"
                   id="room_number" name="room_number"
                   value="{{ old('room_number', $room->room_number ?? '') }}"
                   placeholder="e.g. 101, A1, G-2" required>
            @error('room_number')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="floor">Floor</label>
            <input type="text"
                   class="form-control @error('floor') is-invalid @enderror"
                   id="floor" name="floor"
                   value="{{ old('floor', $room->floor ?? '') }}"
                   placeholder="e.g. Ground, 1st, 2nd">
            @error('floor')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="capacity">Capacity (persons) <span class="text-danger">*</span></label>
            <input type="number"
                   class="form-control @error('capacity') is-invalid @enderror"
                   id="capacity" name="capacity"
                   value="{{ old('capacity', $room->capacity ?? '') }}"
                   min="1" placeholder="e.g. 2" required>
            @error('capacity')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="rent_amount">Rent Amount (₹) <span class="text-danger">*</span></label>
            <input type="number"
                   class="form-control @error('rent_amount') is-invalid @enderror"
                   id="rent_amount" name="rent_amount"
                   value="{{ old('rent_amount', $room->rent_amount ?? '') }}"
                   min="0" step="0.01" placeholder="e.g. 5000" required>
            @error('rent_amount')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-select @error('status') is-invalid @enderror"
                    id="status" name="status" required>
                <option value="available" {{ old('status', $room->status ?? 'available') === 'available' ? 'selected' : '' }}>
                    Available
                </option>
                <option value="occupied" {{ old('status', $room->status ?? '') === 'occupied' ? 'selected' : '' }}>
                    Occupied
                </option>
            </select>
            @error('status')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description"
                      rows="3"
                      placeholder="Optional notes about this room">{{ old('description', $room->description ?? '') }}</textarea>
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
