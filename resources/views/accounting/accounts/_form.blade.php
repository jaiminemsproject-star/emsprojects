@csrf

@php
    $companyId     = old('company_id', $account->company_id ?? config('accounting.default_company_id', 1));
    $gstApplicable = old('is_gst_applicable', $account->is_gst_applicable ?? false);
    $isPartyLedger = $account->exists && $account->related_model_type === \App\Models\Master\Party::class && $account->related_model_id;
    $linkedParty   = $isPartyLedger ? $account->relatedModel : null;
    $hasVouchers   = $hasVouchers ?? false;
    $isSystem      = $account->is_system ?? false;

    $ledgerMode   = config('accounting.ledger_code_mode', 'manual');
    $isNumericCode = !empty($account->code) && preg_match('/^\d+$/', (string) $account->code);
    $lockCode     = ($ledgerMode === 'numeric_auto') || $isPartyLedger || $isSystem || $isNumericCode;
@endphp

@if($isPartyLedger && $linkedParty)
    <div class="alert alert-info py-2 px-3 mb-3">
        <div class="small text-muted mb-1">Linked Party</div>
        <div class="fw-semibold">
            {{ $linkedParty->code }} - {{ $linkedParty->name }}
        </div>
        <div class="small text-muted">
            Identity fields (Code, Name, GSTIN, PAN, Credit days) are managed from Party master.
        </div>
    </div>
@endif
@if($isSystem)
    <div class="alert alert-warning py-2 px-3 mb-3">
        <div class="small text-muted mb-1">System Ledger</div>
        <div class="small">
            This ledger is used by system configuration. Code, group, type and active status are protected.
        </div>
    </div>
@endif


<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label form-label-sm">Company</label>
        <input type="hidden" name="company_id" value="{{ $companyId }}">
        <input type="text"
               class="form-control form-control-sm"
               value="Company #{{ $companyId }}"
               readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Group</label>
        <select name="account_group_id" id="account_group_id" class="form-select form-select-sm">
            @foreach($groups as $group)
                <option value="{{ $group->id }}"
                        @selected(old('account_group_id', $account->account_group_id ?? null) == $group->id)>
                    {{ $group->indent_name ?? $group->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Type <span class="text-muted">(auto as per Group)</span></label>
        <select name="type" id="account_type" class="form-select form-select-sm">
            @if(isset($ledgerTypes) && is_array($ledgerTypes))
                @foreach($ledgerTypes as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('type', $account->type ?? 'ledger') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            @else
                <option value="ledger"
                    @selected(old('type', $account->type ?? 'ledger') === 'ledger')>
                    Ledger
                </option>
            @endif
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Code</label>
        <input type="text" name="code" class="form-control form-control-sm"
               value="{{ old('code', $account->code ?? null) }}"
               @if($lockCode) readonly disabled @endif>
        @if($lockCode)
            <div class="form-text small">Code is system-generated and cannot be edited.</div>
        @endif
    </div>

    <div class="col-md-9">
        <label class="form-label form-label-sm">Name</label>
        <input type="text" name="name" class="form-control form-control-sm"
               value="{{ old('name', $account->name ?? null) }}"
               @if($isPartyLedger) readonly @endif>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Opening Balance</label>
        <input type="number" step="0.01" name="opening_balance" class="form-control form-control-sm"
               value="{{ old('opening_balance', $account->opening_balance ?? 0) }}"
               @if($hasVouchers) readonly @endif>
        @if($hasVouchers)
            <div class="form-text text-muted">
                Opening balance locked because vouchers exist.
            </div>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Opening Type</label>
        @php
            $openingType = old('opening_balance_type', $account->opening_balance_type ?? 'dr');
        @endphp
        <select name="opening_balance_type" class="form-select form-select-sm"
                @if($hasVouchers) disabled @endif>
            <option value="dr" @selected($openingType === 'dr')>Dr</option>
            <option value="cr" @selected($openingType === 'cr')>Cr</option>
        </select>
        @if($hasVouchers)
            <input type="hidden" name="opening_balance_type" value="{{ $openingType }}">
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Opening Balance As On</label>
        <input type="date" name="opening_balance_date" class="form-control form-control-sm"
               value="{{ old('opening_balance_date', $account->opening_balance_date ? $account->opening_balance_date->format('Y-m-d') : null) }}"
               @if($hasVouchers) readonly @endif>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Credit Limit</label>
        <input type="number" step="0.01" name="credit_limit" class="form-control form-control-sm"
               value="{{ old('credit_limit', $account->credit_limit ?? null) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Credit Days</label>
        <input type="number" name="credit_days" class="form-control form-control-sm"
               value="{{ old('credit_days', $account->credit_days ?? null) }}"
               @if($isPartyLedger) readonly @endif>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">GSTIN</label>
        <input type="text" name="gstin" class="form-control form-control-sm"
               value="{{ old('gstin', $account->gstin ?? null) }}"
               @if($isPartyLedger) readonly @endif>
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">PAN</label>
        <input type="text" name="pan" class="form-control form-control-sm"
               value="{{ old('pan', $account->pan ?? null) }}"
               @if($isPartyLedger) readonly @endif>
    </div>

    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $account->is_active ?? true))>
            <label class="form-check-label">Active</label>
        </div>
    </div>
</div>

<hr class="mt-4 mb-3">

<div class="row g-3 align-items-center">
    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_gst_applicable" id="is_gst_applicable" value="1"
                   @checked($gstApplicable)>
            <label class="form-check-label" for="is_gst_applicable">
                GST applicable on this ledger?
            </label>
        </div>
    </div>

    <div class="col-md-9">
        <small class="text-muted">
            Enable this if transactions in this ledger attract GST (e.g. expense ledgers, RCM ledgers, etc.).
        </small>
    </div>
</div>

<div id="gst-config-block" class="row g-3 mt-2" style="display: none;">
    <div class="col-md-3">
        <label class="form-label form-label-sm">HSN / SAC Code</label>
        <input type="text" name="hsn_sac_code" class="form-control form-control-sm"
               value="{{ old('hsn_sac_code') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">GST Rate (%)</label>
        <input type="number" step="0.01" name="gst_rate_percent" class="form-control form-control-sm"
               value="{{ old('gst_rate_percent') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label form-label-sm">Effective From</label>
        <input type="date" name="gst_effective_from" class="form-control form-control-sm"
               value="{{ old('gst_effective_from') }}">
    </div>

    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="is_reverse_charge" value="1"
                   @checked(old('is_reverse_charge', false))>
            <label class="form-check-label">Reverse Charge (RCM)</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary btn-sm">Save</button>
    <a href="{{ route('accounting.accounts.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // -----------------------------
    // 1) Group -> Type dynamic filter
    // -----------------------------
    const groupSelect = document.getElementById('account_group_id');
    const typeSelect  = document.getElementById('account_type');

    const allTypes = @json($ledgerTypes ?? []);
    const groupTypeMap = @json($groupTypeMap ?? []);

    function rebuildTypeOptions() {
        if (!groupSelect || !typeSelect) return;

        const groupId = String(groupSelect.value || '');
        let allowed = groupTypeMap[groupId] || Object.keys(allTypes);

        // Never show 'party' in UI
        allowed = allowed.filter(t => t !== 'party');

        // Keep only types that exist in master list
        allowed = allowed.filter(t => Object.prototype.hasOwnProperty.call(allTypes, t));

        if (allowed.length === 0) {
            allowed = Object.prototype.hasOwnProperty.call(allTypes, 'ledger') ? ['ledger'] : Object.keys(allTypes);
        }

        const current = typeSelect.value;

        // Clear options
        typeSelect.innerHTML = '';

        allowed.forEach(function (t) {
            const opt = document.createElement('option');
            opt.value = t;
            opt.textContent = allTypes[t] || t;
            typeSelect.appendChild(opt);
        });

        // Preserve previous selection if possible
        if (current && allowed.includes(current)) {
            typeSelect.value = current;
        } else {
            typeSelect.value = allowed[0] || '';
        }
    }

    if (groupSelect) {
        groupSelect.addEventListener('change', rebuildTypeOptions);
    }
    rebuildTypeOptions();

    // -----------------------------
    // 2) GST config toggle
    // -----------------------------
    const checkbox = document.getElementById('is_gst_applicable');
    const block    = document.getElementById('gst-config-block');

    if (checkbox && block) {
        const toggle = () => {
            block.style.display = checkbox.checked ? '' : 'none';
        };
        toggle();
        checkbox.addEventListener('change', toggle);
    }
});
</script>
@endpush