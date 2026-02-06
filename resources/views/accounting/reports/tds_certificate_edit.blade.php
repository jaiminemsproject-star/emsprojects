@extends('layouts.erp')

@section('title', 'Edit TDS Certificate')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit TDS Certificate</h4>
        <a href="{{ route('accounting.reports.tds-certificates') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <div class="text-muted">Direction</div>
                    <div><strong>{{ ucfirst($certificate->direction) }}</strong></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Party</div>
                    <div><strong>{{ optional($certificate->partyAccount)->name ?? '-' }}</strong></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Voucher</div>
                    <div>
                        @if($certificate->voucher)
                            <a href="{{ route('accounting.vouchers.show', $certificate->voucher->id) }}">
                                {{ $certificate->voucher->voucher_no }} ({{ $certificate->voucher->voucher_date?->format('d-m-Y') }})
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">TDS Amount</div>
                    <div><strong>{{ number_format((float) $certificate->tds_amount, 2) }}</strong></div>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.reports.tds-certificates.update', $certificate->id) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label class="form-label">TDS Section</label>
                    <select name="tds_section" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach($tdsSections as $sec)
                            <option value="{{ $sec->code }}" {{ old('tds_section', $certificate->tds_section) == $sec->code ? 'selected' : '' }}>
                                {{ $sec->code }} - {{ $sec->name }} ({{ number_format((float) $sec->default_rate, 4) }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('tds_section')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">TDS Rate %</label>
                    <input type="number" step="0.0001" name="tds_rate" class="form-control" value="{{ old('tds_rate', $certificate->tds_rate) }}">
                    @error('tds_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">TDS Amount</label>
                    <input type="number" step="0.01" name="tds_amount" class="form-control" value="{{ old('tds_amount', $certificate->tds_amount) }}" required>
                    @error('tds_amount')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3"></div>

                <div class="col-md-4">
                    <label class="form-label">Certificate No</label>
                    <input type="text" name="certificate_no" class="form-control" value="{{ old('certificate_no', $certificate->certificate_no) }}" maxlength="100">
                    @error('certificate_no')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Certificate Date</label>
                    <input type="date" name="certificate_date" class="form-control" value="{{ old('certificate_date', optional($certificate->certificate_date)->format('Y-m-d')) }}">
                    @error('certificate_date')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-5">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $certificate->remarks) }}" maxlength="2000">
                    @error('remarks')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('accounting.reports.tds-certificates') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
