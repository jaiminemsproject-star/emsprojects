@extends('layouts.erp')

@section('title', 'Create Voucher')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Create Voucher</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('accounting.vouchers.store') }}" data-prevent-enter-submit="1">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Company ID</label>
                        <input type="number" name="company_id" class="form-control form-control-sm" value="{{ old('company_id', 1) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Voucher No.</label>
                        <input type="text" name="voucher_no" class="form-control form-control-sm" value="{{ old('voucher_no') }}" placeholder="Leave blank to auto-generate">
                        <div class="form-text small">Leave blank to auto-generate from Voucher Series.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Type</label>
                        <select name="voucher_type" class="form-select form-select-sm">
                            @foreach($voucherTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('voucher_type', 'journal') === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Date</label>
                        <input type="date" name="voucher_date" class="form-control form-control-sm" value="{{ old('voucher_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Exchange Rate</label>
                        <input type="number" step="0.000001" name="exchange_rate" class="form-control form-control-sm" value="{{ old('exchange_rate', 1) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Project</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">-- none --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" @selected((string) old('project_id') === (string) $p->id)>
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
                                <option value="{{ $cc->id }}" @selected((string) old('cost_center_id') === (string) $cc->id)>{{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label form-label-sm">Narration</label>
                    <textarea name="narration" class="form-control form-control-sm" rows="2">{{ old('narration') }}</textarea>
                </div>

                <hr>

                <h6>Lines</h6>

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
                    @for($i = 0; $i < 4; $i++)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <select name="lines[{{ $i }}][account_id]" class="form-select form-select-sm">
                                    <option value="">-- select --</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="lines[{{ $i }}][cost_center_id]" class="form-select form-select-sm">
                                    <option value="">-- none --</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="lines[{{ $i }}][description]" class="form-control form-control-sm">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="lines[{{ $i }}][debit]" class="form-control form-control-sm">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="lines[{{ $i }}][credit]" class="form-control form-control-sm">
                            </td>
                        </tr>
                    @endfor
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary btn-sm mt-3">Save Draft</button>

                @can('accounting.vouchers.update')
                    <button type="submit" name="post_now" value="1" class="btn btn-success btn-sm mt-3">
                        Save &amp; Post
                    </button>
                @endcan

                <a href="{{ route('accounting.vouchers.index') }}" class="btn btn-secondary btn-sm mt-3">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@include('accounting.vouchers._prevent_enter_submit')
