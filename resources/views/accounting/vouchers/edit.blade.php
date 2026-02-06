@extends('layouts.erp')

@section('title', 'Edit Voucher')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Edit Voucher</h1>
        <a href="{{ route('accounting.vouchers.show', $voucher) }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    <div class="alert alert-info small">
        This voucher is in <strong>Draft</strong> status. Once you <strong>Post</strong> it, editing will be blocked. Use <strong>Reverse</strong> to correct posted vouchers.
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('accounting.vouchers.update', $voucher) }}" data-prevent-enter-submit="1">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Voucher No.</label>
                        <input type="text" name="voucher_no" class="form-control form-control-sm" value="{{ old('voucher_no', $voucher->voucher_no) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Type</label>
                        <select name="voucher_type" class="form-select form-select-sm">
                            @foreach($voucherTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('voucher_type', $voucher->voucher_type) === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Date</label>
                        <input type="date" name="voucher_date" class="form-control form-control-sm" value="{{ old('voucher_date', optional($voucher->voucher_date)->toDateString()) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Exchange Rate</label>
                        <input type="number" step="0.000001" name="exchange_rate" class="form-control form-control-sm" value="{{ old('exchange_rate', $voucher->exchange_rate ?? 1) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Project</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">-- none --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" @selected((string) old('project_id', $voucher->project_id) === (string) $p->id)>
                                    {{ $p->code ? ($p->code . ' - ') : '' }}{{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Cost Center</label>
                        <select name="cost_center_id" class="form-select form-select-sm">
                            <option value="">-- none --</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" @selected((string) old('cost_center_id', $voucher->cost_center_id) === (string) $cc->id)>{{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Reference</label>
                        <input type="text" name="reference" class="form-control form-control-sm" value="{{ old('reference', $voucher->reference) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label form-label-sm">Narration</label>
                    <textarea name="narration" class="form-control form-control-sm" rows="2">{{ old('narration', $voucher->narration) }}</textarea>
                </div>

                <hr>

                <h6>Lines</h6>

                @php
                    $existingLines = $voucher->lines->sortBy('line_no')->values();
                    $rowCount = max(6, $existingLines->count() + 2);
                @endphp

                <div class="table-responsive">
                    <table class="table table-sm" id="voucher-lines-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Account</th>
                            <th>Cost Center</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @for($i = 0; $i < $rowCount; $i++)
                            @php
                                $ln = $existingLines->get($i);
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <select name="lines[{{ $i }}][account_id]" class="form-select form-select-sm">
                                        <option value="">-- select --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                @selected((string) old("lines.$i.account_id", $ln?->account_id) === (string) $account->id)>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="lines[{{ $i }}][cost_center_id]" class="form-select form-select-sm">
                                        <option value="">-- none --</option>
                                        @foreach($costCenters as $cc)
                                            <option value="{{ $cc->id }}"
                                                @selected((string) old("lines.$i.cost_center_id", $ln?->cost_center_id) === (string) $cc->id)>
                                                {{ $cc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="lines[{{ $i }}][description]" class="form-control form-control-sm"
                                           value="{{ old("lines.$i.description", $ln?->description) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="lines[{{ $i }}][debit]" class="form-control form-control-sm"
                                           value="{{ old("lines.$i.debit", $ln?->debit) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="lines[{{ $i }}][credit]" class="form-control form-control-sm"
                                           value="{{ old("lines.$i.credit", $ln?->credit) }}">
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary btn-sm mt-3">Save Draft</button>

                @can('accounting.vouchers.update')
                    <button type="submit" name="post_now" value="1" class="btn btn-success btn-sm mt-3">
                        Save &amp; Post
                    </button>
                @endcan

                <a href="{{ route('accounting.vouchers.show', $voucher) }}" class="btn btn-outline-secondary btn-sm mt-3">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@include('accounting.vouchers._prevent_enter_submit')
