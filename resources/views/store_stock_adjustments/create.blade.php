@extends('layouts.erp')

@section('title', 'New Stock Adjustment / Opening')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">New Stock Adjustment / Opening</h1>
    </div>

    @if($errors->has('general'))
        <div class="alert alert-danger">
            {{ $errors->first('general') }}
        </div>
    @endif

    <form action="{{ route('store-stock-adjustments.store') }}" method="POST">
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Adjustment Date</label>
                        <input type="date"
                               name="adjustment_date"
                               value="{{ old('adjustment_date', now()->format('Y-m-d')) }}"
                               class="form-control form-control-sm">
                        @error('adjustment_date')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Adjustment Type</label>
                        @php
                            $selectedType = old('adjustment_type', $type ?? 'opening');
                        @endphp
                        <select name="adjustment_type" id="adjustment_type" class="form-select form-select-sm">
                            <option value="opening" {{ $selectedType === 'opening' ? 'selected' : '' }}>Opening Balance</option>
                            <option value="increase" {{ $selectedType === 'increase' ? 'selected' : '' }}>Increase (Add to Stock)</option>
                            <option value="decrease" {{ $selectedType === 'decrease' ? 'selected' : '' }}>Decrease (Remove from Stock)</option>
                        </select>
                        @error('adjustment_type')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project (optional)</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">-- General / Store --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                        {{ (string) old('project_id') === (string) $project->id ? 'selected' : '' }}>
                                    {{ $project->code }} - {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Reason (optional)</label>
                        <input type="text" name="reason"
                               value="{{ old('reason') }}"
                               class="form-control form-control-sm">
                        @error('reason')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Remarks (optional)</label>
                        <input type="text" name="remarks"
                               value="{{ old('remarks') }}"
                               class="form-control form-control-sm">
                        @error('remarks')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Opening lines --}}
        <div class="card mb-3" id="opening-lines-card">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Opening Lines (create new stock)</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-opening-line">
                        + Add Line
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                        <tr>
                            <th style="width: 40%;">Item</th>
                            <th style="width: 15%;">UOM</th>
                            <th style="width: 20%;">Brand</th>
                            <th style="width: 12%;">Opening Qty</th>
                            <th style="width: 12%;">Rate</th>
                            <th style="width: 12%;">Amount</th>
                            <th>Remarks</th>
                            <th style="width: 40px;"></th>
                        </tr>
                        </thead>
                        <tbody id="opening-lines-body">
                        <tr data-index="0">
                            <td>
                                <select name="opening_lines[0][item_id]" class="form-select form-select-sm opening-item-select">
                                    <option value="">-- Select Item --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->code ?? '' }} {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('opening_lines.0.item_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <select name="opening_lines[0][uom_id]" class="form-select form-select-sm">
                                    <option value="">-- UOM --</option>
                                    @foreach($uoms as $uom)
                                        <option value="{{ $uom->id }}">
                                            {{ $uom->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('opening_lines.0.uom_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <select name="opening_lines[0][brand]" class="form-select form-select-sm opening-brand-select">
                                    <option value=""></option>
                                    @php $oldBrand = old('opening_lines.0.brand'); @endphp
                                    @if($oldBrand)
                                        <option value="{{ $oldBrand }}" selected>{{ $oldBrand }}</option>
                                    @endif
                                </select>
                                @error('opening_lines.0.brand')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number"
                                       name="opening_lines[0][quantity]"
                                       step="0.001"
                                       min="0.001"
                                       class="form-control form-control-sm text-end opening-qty">
                                @error('opening_lines.0.quantity')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number"
                                       name="opening_lines[0][unit_rate]"
                                       step="0.0001"
                                       min="0"
                                       class="form-control form-control-sm text-end opening-rate"
                                       placeholder="0.00">
                                @error('opening_lines.0.unit_rate')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-end">
                                <span class="opening-amount">0.00</span>
                            </td>
                            <td>
                                <input type="text"
                                       name="opening_lines[0][remarks]"
                                       class="form-control form-control-sm">
                                @error('opening_lines.0.remarks')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-line"
                                        title="Remove line">
                                    &times;
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Adjustment lines (increase / decrease) --}}
        <div class="card mb-3 d-none" id="adjustment-lines-card">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Adjustment Lines (existing stock)</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-adjustment-line">
                        + Add Line
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                        <tr>
                            <th style="width: 45%;">Stock Item</th>
                            <th style="width: 20%;">Adjust Qty</th>
                            <th>Remarks</th>
                            <th style="width: 40px;"></th>
                        </tr>
                        </thead>
                        <tbody id="adjustment-lines-body">
                        <tr data-index="0">
                            <td>
                                <select name="adjustment_lines[0][store_stock_item_id]" class="form-select form-select-sm">
                                    <option value="">-- Select Stock Item --</option>
                                    @foreach($stockItems as $stock)
                                        @php
                                            $labelParts = [];
                                            if ($stock->item) {
                                                $labelParts[] = ($stock->item->code ?? '') . ' ' . $stock->item->name;
                                            }
                                            if ($stock->project) {
                                                $labelParts[] = '[' . $stock->project->code . ']';
                                            }
                                            $qtyLabel = '';
                                            if (! is_null($stock->weight_kg_available)) {
                                                $qtyLabel = 'Avail: ' . number_format($stock->weight_kg_available, 3) . ' kg';
                                            }
                                        @endphp
                                        <option value="{{ $stock->id }}">
                                            #{{ $stock->id }} - {{ implode(' ', $labelParts) }} {{ $qtyLabel ? ' - ' . $qtyLabel : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('adjustment_lines.0.store_stock_item_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number"
                                       name="adjustment_lines[0][quantity]"
                                       step="0.001"
                                       min="0.001"
                                       class="form-control form-control-sm text-end">
                                @error('adjustment_lines.0.quantity')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="text"
                                       name="adjustment_lines[0][remarks]"
                                       class="form-control form-control-sm">
                                @error('adjustment_lines.0.remarks')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-line"
                                        title="Remove line">
                                    &times;
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('store-stock-adjustments.index') }}" class="btn btn-sm btn-secondary">
                Back to List
            </a>
            <button type="submit" class="btn btn-sm btn-primary">
                Save
            </button>
        </div>
    </form>

    {{-- Row templates --}}
    <template id="opening-line-template">
        <tr data-index="__INDEX__">
            <td>
                <select name="opening_lines[__INDEX__][item_id]" class="form-select form-select-sm opening-item-select">
                    <option value="">-- Select Item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->code ?? '' }} {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="opening_lines[__INDEX__][uom_id]" class="form-select form-select-sm">
                    <option value="">-- UOM --</option>
                    @foreach($uoms as $uom)
                        <option value="{{ $uom->id }}">
                            {{ $uom->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="opening_lines[__INDEX__][brand]" class="form-select form-select-sm opening-brand-select">
                    <option value=""></option>
                </select>
            </td>
            <td>
                <input type="number"
                       name="opening_lines[__INDEX__][quantity]"
                       step="0.001"
                       min="0.001"
                       class="form-control form-control-sm text-end opening-qty">
            </td>
            <td>
                <input type="number"
                       name="opening_lines[__INDEX__][unit_rate]"
                       step="0.0001"
                       min="0"
                       class="form-control form-control-sm text-end opening-rate"
                       placeholder="0.00">
            </td>
            <td class="text-end">
                <span class="opening-amount">0.00</span>
            </td>
            <td>
                <input type="text"
                       name="opening_lines[__INDEX__][remarks]"
                       class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <button type="button"
                        class="btn btn-sm btn-outline-danger remove-line"
                        title="Remove line">
                    &times;
                </button>
            </td>
        </tr>
    </template>

    <template id="adjustment-line-template">
        <tr data-index="__INDEX__">
            <td>
                <select name="adjustment_lines[__INDEX__][store_stock_item_id]" class="form-select form-select-sm">
                    <option value="">-- Select Stock Item --</option>
                    @foreach($stockItems as $stock)
                        @php
                            $labelParts = [];
                            if ($stock->item) {
                                $labelParts[] = ($stock->item->code ?? '') . ' ' . $stock->item->name;
                            }
                            if ($stock->project) {
                                $labelParts[] = '[' . $stock->project->code . ']';
                            }
                            $qtyLabel = '';
                            if (! is_null($stock->weight_kg_available)) {
                                $qtyLabel = 'Avail: ' . number_format($stock->weight_kg_available, 3) . ' kg';
                            }
                        @endphp
                        <option value="{{ $stock->id }}">
                            #{{ $stock->id }} - {{ implode(' ', $labelParts) }} {{ $qtyLabel ? ' - ' . $qtyLabel : '' }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number"
                       name="adjustment_lines[__INDEX__][quantity]"
                       step="0.001"
                       min="0.001"
                       class="form-control form-control-sm text-end">
            </td>
            <td>
                <input type="text"
                       name="adjustment_lines[__INDEX__][remarks]"
                       class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <button type="button"
                        class="btn btn-sm btn-outline-danger remove-line"
                        title="Remove line">
                    &times;
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const typeSelect = document.getElementById('adjustment_type');
                const openingCard = document.getElementById('opening-lines-card');
                const adjustmentCard = document.getElementById('adjustment-lines-card');


                // Item meta for Brand dropdown (from Item master)
                const ITEM_META = {!! $itemMetaJson ?? '[]' !!};

                function findItemMeta(id) {
                    return ITEM_META.find(x => String(x.id) === String(id));
                }

                function initSelect2IfAvailable(el) {
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                        window.jQuery(el).select2({
                            tags: true,
                            tokenSeparators: [','],
                            placeholder: 'Select or type brand',
                            width: '100%',
                            allowClear: true
                        });
                    }
                }

                function getAllBrands() {
                    const set = new Set();
                    ITEM_META.forEach(it => {
                        if (it && Array.isArray(it.brands)) {
                            it.brands.forEach(b => {
                                if (b) set.add(String(b));
                            });
                        }
                    });
                    return Array.from(set).sort((a, b) => a.localeCompare(b));
                }

                const ALL_BRANDS = getAllBrands();

                function recalcOpeningAmount(tr) {
                    if (!tr) return;
                    const qtyEl = tr.querySelector('.opening-qty');
                    const rateEl = tr.querySelector('.opening-rate');
                    const outEl = tr.querySelector('.opening-amount');

                    const qty = qtyEl ? parseFloat(qtyEl.value || '0') : 0;
                    const rate = rateEl ? parseFloat(rateEl.value || '0') : 0;
                    const amount = (isFinite(qty) ? qty : 0) * (isFinite(rate) ? rate : 0);

                    if (outEl) {
                        outEl.textContent = amount.toFixed(2);
                    }
                }

                function refreshOpeningBrandOptions(tr) {
                    if (!tr) return;

                    const itemSelect = tr.querySelector('.opening-item-select');
                    const brandSelect = tr.querySelector('.opening-brand-select');

                    if (!brandSelect) return;

                    const currentVal = brandSelect.value;

                    // Reset options
                    brandSelect.innerHTML = '<option value=""></option>';

                    // Prefer item-specific brands; fallback to all known brands
                    let options = ALL_BRANDS;
                    if (itemSelect && itemSelect.value) {
                        const meta = findItemMeta(itemSelect.value);
                        if (meta && Array.isArray(meta.brands) && meta.brands.length) {
                            options = meta.brands;
                        }
                    }

                    options.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b;
                        opt.textContent = b;
                        brandSelect.appendChild(opt);
                    });

                    // Keep current value (including custom typed tag)
                    if (currentVal && !Array.from(brandSelect.options).some(o => o.value === currentVal)) {
                        const opt = document.createElement('option');
                        opt.value = currentVal;
                        opt.textContent = currentVal;
                        opt.selected = true;
                        brandSelect.appendChild(opt);
                    }

                    brandSelect.value = currentVal;

                    initSelect2IfAvailable(brandSelect);
                }

                function toggleType() {
                    const type = typeSelect.value;
                    if (type === 'opening') {
                        openingCard.classList.remove('d-none');
                        adjustmentCard.classList.add('d-none');
                    } else {
                        openingCard.classList.add('d-none');
                        adjustmentCard.classList.remove('d-none');
                    }
                }

                if (typeSelect) {
                    typeSelect.addEventListener('change', toggleType);
                    toggleType();
                }

                let openingIndex = 1;
                let adjustmentIndex = 1;

                const openingBody = document.getElementById('opening-lines-body');
                const openingTemplate = document.getElementById('opening-line-template');

                const adjustmentBody = document.getElementById('adjustment-lines-body');
                const adjustmentTemplate = document.getElementById('adjustment-line-template');

                const addOpeningBtn = document.getElementById('add-opening-line');
                const addAdjustmentBtn = document.getElementById('add-adjustment-line');


                // Init Brand dropdown on existing opening rows + refresh when Item changes
                if (openingBody) {
                    openingBody.querySelectorAll('tr').forEach(tr => refreshOpeningBrandOptions(tr));

                    // Initial amount calc
                    openingBody.querySelectorAll('tr').forEach(tr => recalcOpeningAmount(tr));

                    openingBody.addEventListener('change', function (e) {
                        if (e.target.classList.contains('opening-item-select')) {
                            const row = e.target.closest('tr');
                            refreshOpeningBrandOptions(row);
                        }
                    });

                    openingBody.addEventListener('input', function (e) {
                        if (e.target.classList.contains('opening-qty') || e.target.classList.contains('opening-rate')) {
                            const row = e.target.closest('tr');
                            recalcOpeningAmount(row);
                        }
                    });
                }

                function addOpeningLine() {
                    if (!openingTemplate || !openingBody) return;
                    const html = openingTemplate.innerHTML.replace(/__INDEX__/g, String(openingIndex));
                    openingBody.insertAdjacentHTML('beforeend', html);

                    const newRow = openingBody.querySelector('tr[data-index="' + openingIndex + '"]');
                    refreshOpeningBrandOptions(newRow);
                    recalcOpeningAmount(newRow);

                    openingIndex++;
                }

                function addAdjustmentLine() {
                    if (!adjustmentTemplate) return;
                    const html = adjustmentTemplate.innerHTML.replace(/__INDEX__/g, String(adjustmentIndex));
                    adjustmentBody.insertAdjacentHTML('beforeend', html);
                    adjustmentIndex++;
                }

                if (addOpeningBtn) {
                    addOpeningBtn.addEventListener('click', addOpeningLine);
                }
                if (addAdjustmentBtn) {
                    addAdjustmentBtn.addEventListener('click', addAdjustmentLine);
                }

                document.body.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-line')) {
                        const row = e.target.closest('tr');
                        if (row && (row.parentElement.children.length > 1)) {
                            row.remove();
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection