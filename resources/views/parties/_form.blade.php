@php
    /** @var \App\Models\Party|null $party */
    $isEdit = isset($party) && $party->exists;
@endphp

<form method="POST"
      action="{{ $isEdit ? route('parties.update', $party) : route('parties.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Top section: code, name, legal name --}}
    <div class="row mb-3">
        <div class="col-md-3">
            @if($isEdit)
                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text"
                       id="code"
                       name="code"
                       class="form-control @error('code') is-invalid @enderror"
                       value="{{ old('code', $party->code ?? '') }}"
                       maxlength="50"
                       required>
                @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <label class="form-label d-block">Code</label>
                <div class="form-control-plaintext">
                    <span class="text-muted">Will be auto-generated on save</span>
                </div>
                <input type="hidden" name="code" value="{{ old('code') }}">
            @endif
        </div>

        <div class="col-md-5">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $party->name ?? '') }}"
                   maxlength="200"
                   required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="legal_name" class="form-label">Legal Name</label>
            <input type="text"
                   id="legal_name"
                   name="legal_name"
                   class="form-control @error('legal_name') is-invalid @enderror"
                   value="{{ old('legal_name', $party->legal_name ?? '') }}"
                   maxlength="250">
            @error('legal_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Types + GSTIN/PAN/MSME/Active --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label d-block">Party Type</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="checkbox"
                       id="is_supplier"
                       name="is_supplier"
                       value="1"
                       {{ old('is_supplier', $party->is_supplier ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_supplier">Supplier</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="checkbox"
                       id="is_contractor"
                       name="is_contractor"
                       value="1"
                       {{ old('is_contractor', $party->is_contractor ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_contractor">Contractor</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="checkbox"
                       id="is_client"
                       name="is_client"
                       value="1"
                       {{ old('is_client', $party->is_client ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_client">Client</label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="gstin" class="form-label">GSTIN</label>
            <input type="text"
                   id="gstin"
                   name="gstin"
                   class="form-control @error('gstin') is-invalid @enderror"
                   value="{{ old('gstin', $party->gstin ?? '') }}"
                   maxlength="20">
            @error('gstin')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2">
            <label for="pan" class="form-label">PAN</label>
            <input type="text"
                   id="pan"
                   name="pan"
                   class="form-control @error('pan') is-invalid @enderror"
                   value="{{ old('pan', $party->pan ?? '') }}"
                   maxlength="20">
            @error('pan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="msme_no" class="form-label">MSME No.</label>
            <input type="text"
                   id="msme_no"
                   name="msme_no"
                   class="form-control @error('msme_no') is-invalid @enderror"
                   value="{{ old('msme_no', $party->msme_no ?? '') }}"
                   maxlength="50">
            @error('msme_no')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div class="form-check mt-2">
                <input class="form-check-input"
                       type="checkbox"
                       id="is_active"
                       name="is_active"
                       value="1"
                       {{ old('is_active', $party->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Active
                </label>
            </div>
        </div>
    </div>

    {{-- Primary contact --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="primary_phone" class="form-label">Primary Phone</label>
            <input type="text"
                   id="primary_phone"
                   name="primary_phone"
                   class="form-control @error('primary_phone') is-invalid @enderror"
                   value="{{ old('primary_phone', $party->primary_phone ?? '') }}"
                   maxlength="50">
            @error('primary_phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="primary_email" class="form-label">Primary Email</label>
            <input type="email"
                   id="primary_email"
                   name="primary_email"
                   class="form-control @error('primary_email') is-invalid @enderror"
                   value="{{ old('primary_email', $party->primary_email ?? '') }}"
                   maxlength="150">
            @error('primary_email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Address --}}
    <h6 class="mt-4">Address</h6>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="address_line1" class="form-label">Address Line 1</label>
            <input type="text"
                   id="address_line1"
                   name="address_line1"
                   class="form-control @error('address_line1') is-invalid @enderror"
                   value="{{ old('address_line1', $party->address_line1 ?? '') }}"
                   maxlength="200">
            @error('address_line1')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="address_line2" class="form-label">Address Line 2</label>
            <input type="text"
                   id="address_line2"
                   name="address_line2"
                   class="form-control @error('address_line2') is-invalid @enderror"
                   value="{{ old('address_line2', $party->address_line2 ?? '') }}"
                   maxlength="200">
            @error('address_line2')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text"
                   id="city"
                   name="city"
                   class="form-control @error('city') is-invalid @enderror"
                   value="{{ old('city', $party->city ?? '') }}"
                   maxlength="100">
            @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="state" class="form-label">State</label>
            <input type="text"
                   id="state"
                   name="state"
                   class="form-control @error('state') is-invalid @enderror"
                   value="{{ old('state', $party->state ?? '') }}"
                   maxlength="100">
            @error('state')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2">
            <label for="pincode" class="form-label">Pincode</label>
            <input type="text"
                   id="pincode"
                   name="pincode"
                   class="form-control @error('pincode') is-invalid @enderror"
                   value="{{ old('pincode', $party->pincode ?? '') }}"
                   maxlength="20">
            @error('pincode')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="country" class="form-label">Country</label>
            <input type="text"
                   id="country"
                   name="country"
                   class="form-control @error('country') is-invalid @enderror"
                   value="{{ old('country', $party->country ?? 'India') }}"
                   maxlength="100">
            @error('country')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Buttons --}}
    <div class="d-flex justify-content-between">
        <a href="{{ route('parties.index') }}" class="btn btn-outline-secondary">
            Cancel
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Update Party' : 'Create Party' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gstInput = document.getElementById('gstin');
    const panInput = document.getElementById('pan');

    if (!gstInput || !panInput) {
        return;
    }

    gstInput.addEventListener('input', function () {
        let gst = gstInput.value || '';
        gst = gst.toUpperCase().replace(/\s+/g, '');

        if (gst.length === 15) {
            const pan = gst.substring(2, 12); // chars 3–12 (0-based index 2..11)
            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/;

            if (panRegex.test(pan)) {
                panInput.value = pan;
            }
        }
    });
});
</script>
@endpush
