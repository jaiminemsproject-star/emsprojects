@php
$isEdit = isset($company) && $company->exists;
@endphp

<form id="companyForm" method="POST"
      action="{{ $isEdit ? route('companies.update', $company) : route('companies.store') }}">
@csrf
@if($isEdit) @method('PUT') @endif

{{-- BASIC INFO --}}
<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label class="form-label">Code *</label>
        <input type="text" name="code" class="form-control"
               placeholder="CMP001"
               value="{{ old('code', $company->code ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control"
               placeholder="Company name"
               value="{{ old('name', $company->name ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Legal Name</label>
        <input type="text" name="legal_name" class="form-control"
               placeholder="Registered name"
               value="{{ old('legal_name', $company->legal_name ?? '') }}">
    </div>
</div>

{{-- TAX --}}
<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label class="form-label">GSTIN</label>
        <input type="text" name="gst_number" class="form-control"
               placeholder="22AAAAA0000A1Z5"
               value="{{ old('gst_number', $company->gst_number ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">PAN</label>
        <input type="text" name="pan" class="form-control"
               placeholder="AAAAA0000A"
               value="{{ old('pan', $company->pan ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               placeholder="email@example.com"
               value="{{ old('email', $company->email ?? '') }}">
    </div>
</div>

{{-- CONTACT --}}
<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control"
               placeholder="9876543210"
               value="{{ old('phone', $company->phone ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Website</label>
        <input type="url" name="website" class="form-control"
               placeholder="www.example.com"
               value="{{ old('website', $company->website ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Country</label>
        <input type="text" name="country" class="form-control"
               placeholder="India"
               value="{{ old('country', $company->country ?? 'India') }}">
    </div>
</div>

{{-- ADDRESS --}}
<div class="row mb-3">
    <div class="col-md-4 mb-3">
        <label class="form-label">Address Line 1</label>
        <input type="text" name="address_line1" class="form-control"
               placeholder="Street address"
               value="{{ old('address_line1', $company->address_line1 ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Address Line 2</label>
        <input type="text" name="address_line2" class="form-control"
               placeholder="Area / Landmark"
               value="{{ old('address_line2', $company->address_line2 ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">City</label>
        <input type="text" name="city" class="form-control"
               placeholder="City"
               value="{{ old('city', $company->city ?? '') }}">
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <label class="form-label">State</label>
        <input type="text" name="state" class="form-control"
               placeholder="State"
               value="{{ old('state', $company->state ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">PIN Code</label>
        <input type="text" name="pincode" class="form-control"
               placeholder="380001"
               value="{{ old('pincode', $company->pincode ?? '') }}">
    </div>

    <div class="col-md-4 d-flex align-items-end gap-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_default" value="1"
                   {{ old('is_default', $company->is_default ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Default</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $company->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label">Active</label>
        </div>
    </div>
</div>

{{-- ACTIONS --}}
<div class="d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Update' : 'Create' }}
    </button>
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>
</form>
