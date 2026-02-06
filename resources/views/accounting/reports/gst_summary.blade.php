@extends('layouts.erp')

@section('title', 'GST Summary')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">GST Summary (Input vs Output)</h1>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm">From</label>
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date', optional($fromDate)->toDateString()) }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label form-label-sm">To</label>
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date', optional($toDate)->toDateString()) }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="bi bi-funnel me-1"></i> Apply
                    </button>

                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>

                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                    </a>

                    <div class="small text-muted align-self-center">
                        Totals are calculated from <span class="fw-semibold">posted</span> vouchers only.
                    </div>
                </div>
            </form>

            <div class="mt-3 small text-muted">
                Uses configured GST ledgers:
                <span class="badge bg-light text-dark border">Input CGST: {{ $codes['input_cgst'] }}</span>
                <span class="badge bg-light text-dark border">Input SGST: {{ $codes['input_sgst'] }}</span>
                <span class="badge bg-light text-dark border">Input IGST: {{ $codes['input_igst'] }}</span>
                <span class="badge bg-light text-dark border">Output CGST: {{ $codes['output_cgst'] }}</span>
                <span class="badge bg-light text-dark border">Output SGST: {{ $codes['output_sgst'] }}</span>
                <span class="badge bg-light text-dark border">Output IGST: {{ $codes['output_igst'] }}</span>
            </div>

            @if(!empty($missing))
                <div class="alert alert-warning mt-3 mb-0">
                    <div class="fw-semibold mb-1">Some GST ledger codes could not be resolved in Accounts:</div>
                    <ul class="mb-0">
                        @foreach($missing as $m)
                            <li>{{ $m }}</li>
                        @endforeach
                    </ul>
                    <div class="small mt-2">
                        Please ensure these accounts exist for the selected company and codes match <code>config/accounting.php</code>.
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">Tax</th>
                            <th class="text-end" style="width: 20%;">Input GST (Net Dr-Cr)</th>
                            <th class="text-end" style="width: 20%;">Output GST (Net Cr-Dr)</th>
                            <th class="text-end" style="width: 20%;">Net (Output - Input)</th>
                            <th style="width: 20%;">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            @php
                                $net = (float) $r['net'];
                                $remark = $net > 0 ? 'Payable' : ($net < 0 ? 'Refund / Credit' : 'Nil');
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $r['tax'] }}</td>
                                <td class="text-end">{{ number_format($r['input'], 2) }}</td>
                                <td class="text-end">{{ number_format($r['output'], 2) }}</td>
                                <td class="text-end {{ $net > 0 ? 'text-danger' : ($net < 0 ? 'text-success' : '') }}">
                                    {{ number_format($net, 2) }}
                                </td>
                                <td>{{ $remark }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        @php
                            $totNet = (float) $totNet;
                            $totRemark = $totNet > 0 ? 'Net Payable' : ($totNet < 0 ? 'Net Refund / Credit' : 'Nil');
                        @endphp
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-end">{{ number_format($totInput, 2) }}</th>
                            <th class="text-end">{{ number_format($totOutput, 2) }}</th>
                            <th class="text-end {{ $totNet > 0 ? 'text-danger' : ($totNet < 0 ? 'text-success' : '') }}">
                                {{ number_format($totNet, 2) }}
                            </th>
                            <th>{{ $totRemark }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="small text-muted mt-2">
        Note: This is a ledger-based summary. For return filing, invoice-wise GST registers (B2B/B2C/HSN summary) will be added in the next phase.
    </div>
</div>
@endsection



