@extends('layouts.erp')

@section('title', 'GST Voucher Register')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">GST Voucher Register (Voucher-based)</h1>

    @if(!empty($missing))
        <div class="alert alert-warning py-2">
            <strong>Warning:</strong>
            Some GST ledgers configured in <code>config/accounting.php</code> are missing in Accounts master.
            This report will still work for the ledgers that exist.
            <div class="small mt-1">
                Missing: {{ implode(' | ', $missing) }}
            </div>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="company_id" value="{{ $companyId }}"/>

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

                <div class="col-md-3">
                    <label class="form-label form-label-sm">Project</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">All projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ (string) $projectId === (string) $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label form-label-sm">Mode</label>
                    <select name="mode" class="form-select form-select-sm">
                        @php
    $m = request('mode', $mode ?? 'all');
@endphp
<option value="all" {{ $m === 'all' ? 'selected' : '' }}>All</option>
                        <option value="input" {{ $m === 'input' ? 'selected' : '' }}>Input</option>
                        <option value="output" {{ $m === 'output' ? 'selected' : '' }}>Output</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label form-label-sm">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        @php
    $s = request('status', $status ?? 'posted');
@endphp
<option value="posted" {{ $s === 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="draft" {{ $s === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="all" {{ $s === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <button class="btn btn-sm btn-primary">Apply</button>
                </div>

                <div class="col-md-12">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('accounting.reports.gst-voucher-register.export', request()->all()) }}">
                        Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                            <tr>
                                <th class="text-muted" style="width: 170px;">Input GST</th>
                                <td class="text-end">
                                    {{ \App\Support\MoneyHelper::fromPaise($totals['input_total'] ?? 0) }}
                                </td>

                                <th class="text-muted" style="width: 170px;">Output GST</th>
                                <td class="text-end">
                                    {{ \App\Support\MoneyHelper::fromPaise($totals['output_total'] ?? 0) }}
                                </td>

                                <th class="text-muted" style="width: 170px;">Net (Output - Input)</th>
                                <td class="text-end">
                                    {{ \App\Support\MoneyHelper::fromPaise($totals['net'] ?? 0) }}
                                </td>

                                <th class="text-muted" style="width: 120px;">Vouchers</th>
                                <td class="text-end">
                                    {{ $totals['rows'] ?? 0 }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Voucher No</th>
                                <th>Type</th>
                                <th>Project</th>
                                <th>Party</th>
                                <th>GSTIN</th>
                                <th class="text-end">In CGST</th>
                                <th class="text-end">In SGST</th>
                                <th class="text-end">In IGST</th>
                                <th class="text-end">Out CGST</th>
                                <th class="text-end">Out SGST</th>
                                <th class="text-end">Out IGST</th>
                                <th class="text-end">Input Total</th>
                                <th class="text-end">Output Total</th>
                                <th class="text-end">Net</th>
                                <th class="text-end">Voucher Amt</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($rows))
                            @foreach($rows as $r)
                                @php
                                    $partyAcc = $r->party_account_id ? ($partyAccounts[$r->party_account_id] ?? null) : null;
                                    $party    = $partyAcc?->relatedModel;

                                    $inCgstPaise  = \App\Support\MoneyHelper::toPaise($r->input_cgst ?? 0);
                                    $inSgstPaise  = \App\Support\MoneyHelper::toPaise($r->input_sgst ?? 0);
                                    $inIgstPaise  = \App\Support\MoneyHelper::toPaise($r->input_igst ?? 0);

                                    $outCgstPaise = \App\Support\MoneyHelper::toPaise($r->output_cgst ?? 0);
                                    $outSgstPaise = \App\Support\MoneyHelper::toPaise($r->output_sgst ?? 0);
                                    $outIgstPaise = \App\Support\MoneyHelper::toPaise($r->output_igst ?? 0);

                                    $inputTotalPaise  = $inCgstPaise + $inSgstPaise + $inIgstPaise;
                                    $outputTotalPaise = $outCgstPaise + $outSgstPaise + $outIgstPaise;
                                    $netPaise         = $outputTotalPaise - $inputTotalPaise;

                                    $amountBasePaise  = \App\Support\MoneyHelper::toPaise($r->amount_base ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ $r->voucher_date }}</td>
                                    <td>
                                        <a href="{{ route('accounting.vouchers.show', $r->voucher_id) }}" class="text-decoration-none">
                                            {{ $r->voucher_no }}
                                        </a>
                                    </td>
                                    <td>{{ $r->voucher_type }}</td>
                                    <td>{{ $r->project_name }}</td>
                                    <td>{{ $party?->name ?? $partyAcc?->name }}</td>
                                    <td>{{ $party?->gstin ?? $partyAcc?->gstin }}</td>

                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($inCgstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($inSgstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($inIgstPaise) }}</td>

                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($outCgstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($outSgstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($outIgstPaise) }}</td>

                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($inputTotalPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($outputTotalPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($netPaise) }}</td>

                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($amountBasePaise) }}</td>

                                    <td>
                                        <span class="badge bg-{{ ($r->status ?? '') === 'posted' ? 'success' : 'secondary' }}">
                                            {{ $r->status }}
                                        </span>
                                    </td>
                                </tr>
                                                        @endforeach
                            @else

                                <tr>
                                    <td colspan="17" class="text-center text-muted py-4">
                                        No vouchers found for selected filters.
                                    </td>
                                </tr>
                                                        @endif
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mt-3 mb-0">
                        Notes:
                        <br>• This report is voucher-based and picks GST amounts from configured GST ledger accounts (Input + Output).
                        <br>• Use this to catch manual GST adjustment vouchers that won't appear in Purchase/Sales registers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
