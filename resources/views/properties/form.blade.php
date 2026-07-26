{{-- Shared form fields for create and edit --}}

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="property_name">Property Name <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('property_name') is-invalid @enderror"
                   id="property_name" name="property_name"
                   value="{{ old('property_name', $property->property_name ?? '') }}"
                   placeholder="e.g. Sunrise Hostel" required>
            @error('property_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="property_type">Property Type</label>
            <select class="form-select @error('property_type') is-invalid @enderror"
                    id="property_type" name="property_type">
                <option value="">-- Select Type --</option>
                @foreach(['hostel','pg','rooms','flats','commercial'] as $type)
                    <option value="{{ $type }}"
                        {{ old('property_type', $property->property_type ?? '') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            @error('property_type')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="total_rooms">Total Rooms <span class="text-danger">*</span></label>
            <input type="number"
                   class="form-control @error('total_rooms') is-invalid @enderror"
                   id="total_rooms" name="total_rooms"
                   value="{{ old('total_rooms', $property->total_rooms ?? '') }}"
                   min="1" placeholder="e.g. 10" required>
            @error('total_rooms')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label for="address_line_1">Address Line 1 <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('address_line_1') is-invalid @enderror"
                   id="address_line_1" name="address_line_1"
                   value="{{ old('address_line_1', $property->address_line_1 ?? '') }}"
                   placeholder="Street / Building" required>
            @error('address_line_1')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="address_line_2">Address Line 2</label>
            <input type="text"
                   class="form-control @error('address_line_2') is-invalid @enderror"
                   id="address_line_2" name="address_line_2"
                   value="{{ old('address_line_2', $property->address_line_2 ?? '') }}"
                   placeholder="Landmark / Area (optional)">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="city">City <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('city') is-invalid @enderror"
                   id="city" name="city"
                   value="{{ old('city', $property->city ?? '') }}"
                   placeholder="e.g. Mumbai" required>
            @error('city')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="state">State <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('state') is-invalid @enderror"
                   id="state" name="state"
                   value="{{ old('state', $property->state ?? '') }}"
                   placeholder="e.g. Maharashtra" required>
            @error('state')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pincode">Pincode</label>
            <input type="text"
                   class="form-control @error('pincode') is-invalid @enderror"
                   id="pincode" name="pincode"
                   value="{{ old('pincode', $property->pincode ?? '') }}"
                   placeholder="e.g. 400001">
            @error('pincode')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
