{{-- Shared tenant form fields --}}

{{-- Basic Info --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Full Name <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name"
                   value="{{ old('name', $tenant->name ?? '') }}"
                   placeholder="e.g. Rahul Sharma" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="mobile">Mobile Number <span class="text-danger">*</span>
                <small class="text-muted">(WhatsApp)</small>
            </label>
            <input type="tel"
                   class="form-control @error('mobile') is-invalid @enderror"
                   id="mobile" name="mobile"
                   value="{{ old('mobile', $tenant->mobile ?? '') }}"
                   placeholder="e.g. 9876543210" required>
            @error('mobile') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email"
                   value="{{ old('email', $tenant->email ?? '') }}"
                   placeholder="optional">
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="id_proof_number">ID Proof Number</label>
            <input type="text"
                   class="form-control @error('id_proof_number') is-invalid @enderror"
                   id="id_proof_number" name="id_proof_number"
                   value="{{ old('id_proof_number', $tenant->id_proof_number ?? '') }}"
                   placeholder="Aadhar / PAN / Passport No.">
            @error('id_proof_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-select @error('status') is-invalid @enderror"
                    id="status" name="status" required>
                <option value="inactive" {{ old('status', $tenant->status ?? 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="active"   {{ old('status', $tenant->status ?? '') === 'active'   ? 'selected' : '' }}>Active</option>
            </select>
            <small class="text-muted">Status auto-updates based on room assignment</small>
            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="address">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror"
                      id="address" name="address" rows="2"
                      placeholder="Permanent address">{{ old('address', $tenant->address ?? '') }}</textarea>
            @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<hr class="my-4">
<h6 class="font-weight-bold mb-3 text-muted">
    <i class="mdi mdi-camera me-1"></i> Photo & Documents
</h6>

<div class="row">

    {{-- Tenant Photo --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">Tenant Photo</label>
        <div class="upload-box border rounded p-3 text-center" onclick="document.getElementById('photo').click()" style="cursor:pointer; min-height:160px;">
            <img id="photo_preview"
                 src="{{ isset($tenant) && $tenant->photo ? Storage::url($tenant->photo) : '' }}"
                 class="{{ isset($tenant) && $tenant->photo ? '' : 'd-none' }} img-fluid rounded mb-2"
                 style="max-height:120px; object-fit:cover;"
                 alt="Photo Preview">
            <div id="photo_placeholder" class="{{ isset($tenant) && $tenant->photo ? 'd-none' : '' }}">
                <i class="mdi mdi-account-circle text-muted" style="font-size:3rem;"></i>
                <p class="text-muted small mb-0 mt-1">Click to upload photo</p>
                <p class="text-muted" style="font-size:11px;">JPG, PNG — max 2MB</p>
            </div>
        </div>
        <input type="file" id="photo" name="photo" accept="image/*" class="d-none @error('photo') is-invalid @enderror"
               onchange="previewImage(this, 'photo_preview', 'photo_placeholder')">
        @error('photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- ID Proof Front --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">ID Proof — Front <small class="text-muted">(Aadhar/PAN/Passport)</small></label>
        <div class="upload-box border rounded p-3 text-center" onclick="document.getElementById('id_proof').click()" style="cursor:pointer; min-height:160px;">
            <img id="id_proof_preview"
                 src="{{ isset($tenant) && $tenant->id_proof ? Storage::url($tenant->id_proof) : '' }}"
                 class="{{ isset($tenant) && $tenant->id_proof ? '' : 'd-none' }} img-fluid rounded mb-2"
                 style="max-height:120px; object-fit:cover;"
                 alt="ID Front Preview">
            <div id="id_proof_placeholder" class="{{ isset($tenant) && $tenant->id_proof ? 'd-none' : '' }}">
                <i class="mdi mdi-card-account-details text-muted" style="font-size:3rem;"></i>
                <p class="text-muted small mb-0 mt-1">Click to upload front</p>
                <p class="text-muted" style="font-size:11px;">JPG, PNG, PDF — max 3MB</p>
            </div>
        </div>
        <input type="file" id="id_proof" name="id_proof" accept="image/*,.pdf" class="d-none @error('id_proof') is-invalid @enderror"
               onchange="previewImage(this, 'id_proof_preview', 'id_proof_placeholder')">
        @error('id_proof') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- ID Proof Back --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">ID Proof — Back</label>
        <div class="upload-box border rounded p-3 text-center" onclick="document.getElementById('id_proof_back').click()" style="cursor:pointer; min-height:160px;">
            <img id="id_proof_back_preview"
                 src="{{ isset($tenant) && $tenant->id_proof_back ? Storage::url($tenant->id_proof_back) : '' }}"
                 class="{{ isset($tenant) && $tenant->id_proof_back ? '' : 'd-none' }} img-fluid rounded mb-2"
                 style="max-height:120px; object-fit:cover;"
                 alt="ID Back Preview">
            <div id="id_proof_back_placeholder" class="{{ isset($tenant) && $tenant->id_proof_back ? 'd-none' : '' }}">
                <i class="mdi mdi-card-account-details-outline text-muted" style="font-size:3rem;"></i>
                <p class="text-muted small mb-0 mt-1">Click to upload back</p>
                <p class="text-muted" style="font-size:11px;">JPG, PNG, PDF — max 3MB</p>
            </div>
        </div>
        <input type="file" id="id_proof_back" name="id_proof_back" accept="image/*,.pdf" class="d-none @error('id_proof_back') is-invalid @enderror"
               onchange="previewImage(this, 'id_proof_back_preview', 'id_proof_back_placeholder')">
        @error('id_proof_back') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

</div>

@push('scripts')
<script>
function previewImage(input, previewId, placeholderId) {
    const preview     = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    const file        = input.files[0];

    if (!file) return;

    // For PDFs just show filename, not image preview
    if (file.type === 'application/pdf') {
        placeholder.innerHTML = '<i class="mdi mdi-file-pdf text-danger" style="font-size:3rem;"></i>'
            + '<p class="text-muted small mt-1 mb-0">' + file.name + '</p>';
        placeholder.classList.remove('d-none');
        preview.classList.add('d-none');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
