@extends('layouts.erp')

@section('title', 'Balance Sheet')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Balance Sheet</h1>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm">As on date</label>
                    <input type="date"
                           name="as_of_date"
                           value="{{ request('as_of_date', optional($asOfDate)->toDateString()) }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                    <a href="{{ route('accounting.reports.balance-sheet') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>

                    <a href="{{ route('accounting.reports.balance-sheet', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>

                <div class="col-md-4 text-end small text-muted">
                    Company #{{ $companyId }}
                </div>
            </form>
        </div>
    </div>

    @php
        // Profit/Loss balancing logic: positive = Profit (credit) goes to Liabilities side
        // negative = Loss (debit) goes to Assets side
        $plIsProfit = $plBalance > 0;
        $plAmount = abs($plBalance);
    @endphp

    <div class="alert alert-info">
        <div class="small">
            This Balance Sheet is generated from <strong>Opening Balances</strong> + <strong>Posted Vouchers</strong> up to the selected date.
            Income &amp; Expense ledgers are excluded and a Profit/Loss balancing line is shown.
            Amounts show <strong>Dr/Cr</strong> to make contra balances visible.
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2 fw-semibold small">Assets</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Particulars</th>
                                    <th style="width: 30%" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assetGroups as $g)
                                    <tr class="table-secondary">
                                        <td class="small fw-semibold" colspan="2">{{ $g['group']->name }}</td>
                                    </tr>

                                    @foreach($g['accounts'] as $row)
                                        @php
                                            $amt = (float) $row['amount'];
                                            $drcr = $amt >= 0 ? 'Dr' : 'Cr';
                                        @endphp
                                        <tr>
                                            <td class="small ps-3">{{ $row['account']->name }}</td>
                                            <td class="small text-end">
                                                {{ number_format(abs($amt), 2) }} <span class="fw-semibold">{{ $drcr }}</span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @php
                                        $gt = (float) $g['total'];
                                        $gDrCr = $gt >= 0 ? 'Dr' : 'Cr';
                                    @endphp
                                    <tr class="table-light fw-semibold">
                                        <td class="small text-end">Total {{ $g['group']->name }}</td>
                                        <td class="small text-end">
                                            {{ number_format(abs($gt), 2) }} {{ $gDrCr }}
                                        </td>
                                    </tr>
                                @endforeach

                                @if(!empty($assetGroups) && !$plIsProfit && $plAmount > 0.005)
                                    <tr class="table-warning fw-semibold">
                                        <td class="small">Loss (Balancing)</td>
                                        <td class="small text-end">{{ number_format($plAmount, 2) }} Dr</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                @php
                                    $assetsDrCr = $totalAssets >= 0 ? 'Dr' : 'Cr';
                                @endphp
                                <tr class="table-dark text-white fw-semibold">
                                    <td class="small text-end">Total Assets</td>
                                    <td class="small text-end">{{ number_format(abs($totalAssets), 2) }} {{ $assetsDrCr }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2 fw-semibold small">Liabilities &amp; Equity</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Particulars</th>
                                    <th style="width: 30%" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($liabilityGroups as $g)
                                    <tr class="table-secondary">
                                        <td class="small fw-semibold" colspan="2">{{ $g['group']->name }}</td>
                                    </tr>

                                    @foreach($g['accounts'] as $row)
                                        @php
                                            $amt = (float) $row['amount'];
                                            // In controller, liabilities amounts are stored as credit-positive
                                            $drcr = $amt >= 0 ? 'Cr' : 'Dr';
                                        @endphp
                                        <tr>
                                            <td class="small ps-3">{{ $row['account']->name }}</td>
                                            <td class="small text-end">
                                                {{ number_format(abs($amt), 2) }} <span class="fw-semibold">{{ $drcr }}</span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @php
                                        $gt = (float) $g['total'];
                                        $gDrCr = $gt >= 0 ? 'Cr' : 'Dr';
                                    @endphp
                                    <tr class="table-light fw-semibold">
                                        <td class="small text-end">Total {{ $g['group']->name }}</td>
                                        <td class="small text-end">
                                            {{ number_format(abs($gt), 2) }} {{ $gDrCr }}
                                        </td>
                                    </tr>
                                @endforeach

                                @if(!empty($liabilityGroups) && $plIsProfit && $plAmount > 0.005)
                                    <tr class="table-warning fw-semibold">
                                        <td class="small">Profit (Balancing)</td>
                                        <td class="small text-end">{{ number_format($plAmount, 2) }} Cr</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                @php
                                    $liabDrCr = $totalLiabilities >= 0 ? 'Cr' : 'Dr';
                                @endphp
                                <tr class="table-dark text-white fw-semibold">
                                    <td class="small text-end">Total Liabilities</td>
                                    <td class="small text-end">{{ number_format(abs($totalLiabilities), 2) }} {{ $liabDrCr }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
