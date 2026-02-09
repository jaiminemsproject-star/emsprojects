@php
/** @var \App\Models\Party|null $party */
$isEdit = isset($party) && $party->exists;
@endphp

<form method="POST"
      action="{{ $isEdit ? route('parties.update', $party) : route('parties.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- BASIC DETAILS --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-3">Basic Details</h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Code</label>
                @if($isEdit)
                    <input type="text" name="code" class="form-control"
                           placeholder="PARTY-0001"
                           value="{{ old('code', $party->code) }}">
                @else
                    <input type="text" class="form-control"
                           placeholder="Auto-generated after save" disabled>
                @endif
            </div>

            <div class="col-md-6">
                <label class="form-label">Party Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       placeholder="ABC Traders"
                       value="{{ old('name', $party->name ?? '') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Legal Name</label>
                <input type="text" name="legal_name" class="form-control"
                       placeholder="ABC Traders Private Limited"
                       value="{{ old('legal_name', $party->legal_name ?? '') }}">
            </div>
        </div>
    </div>

    {{-- PARTY TYPE & TAX --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-3">Party Type & Tax Details</h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Party Type</label>
                <div>
                    @foreach (['supplier' => 'Supplier', 'contractor' => 'Contractor', 'client' => 'Client'] as $key => $label)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_{{ $key }}"
                                   value="1"
                                   {{ old('is_' . $key, $party->{'is_' . $key} ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">GSTIN</label>
                <input type="text" id="gstin" name="gstin" class="form-control"
                       placeholder="22AAAAA0000A1Z5"
                       value="{{ old('gstin', $party->gstin ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">PAN</label>
                <input type="text" id="pan" name="pan" class="form-control"
                       placeholder="AAAAA0000A"
                       value="{{ old('pan', $party->pan ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">MSME Number</label>
                <input type="text" name="msme_no" class="form-control"
                       placeholder="UDYAM-GJ-00-0000000"
                       value="{{ old('msme_no', $party->msme_no ?? '') }}">

                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox"
                           name="is_active" value="1"
                           {{ old('is_active', $party->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTACT DETAILS --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-3">Contact Details</h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Primary Phone</label>
                <input type="text" name="primary_phone" class="form-control"
                       placeholder="+91 98765 43210"
                       value="{{ old('primary_phone', $party->primary_phone ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Primary Email</label>
                <input type="email" name="primary_email" class="form-control"
                       placeholder="accounts@company.com"
                       value="{{ old('primary_email', $party->primary_email ?? '') }}">
            </div>
        </div>
    </div>

    {{-- ADDRESS --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-3">Address Details</h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Address Line 1</label>
                <input type="text" name="address_line1" class="form-control"
                       placeholder="Building / Street / Area"
                       value="{{ old('address_line1', $party->address_line1 ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Address Line 2</label>
                <input type="text" name="address_line2" class="form-control"
                       placeholder="Landmark (optional)"
                       value="{{ old('address_line2', $party->address_line2 ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control"
                       placeholder="Ahmedabad"
                       value="{{ old('city', $party->city ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control"
                       placeholder="Gujarat"
                       value="{{ old('state', $party->state ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control"
                       placeholder="380015"
                       value="{{ old('pincode', $party->pincode ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control"
                       placeholder="India"
                       value="{{ old('country', $party->country ?? 'India') }}">
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
            {{ $isEdit ? 'Update Party' : 'Create Party' }}
        </button>

        <a href="{{ route('parties.index') }}" class="btn btn-outline-secondary">
            Cancel
        </a>
    </div>

</form>
