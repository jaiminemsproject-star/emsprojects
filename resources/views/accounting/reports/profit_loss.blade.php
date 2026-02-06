@extends('layouts.erp')

@section('title', 'Profit & Loss')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Profit &amp; Loss</h1>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm">From date</label>
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date', optional($fromDate)->toDateString()) }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label form-label-sm">To date</label>
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date', optional($toDate)->toDateString()) }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                    <a href="{{ route('accounting.reports.profit-loss') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>

                    <a href="{{ route('accounting.reports.profit-loss', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download"></i> Export CSV
                    </a>

                    <span class="ms-2 small text-muted">Company #{{ $companyId }}</span>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div class="fw-semibold small">
                Period: {{ optional($fromDate)->toDateString() }} to {{ optional($toDate)->toDateString() }}
            </div>
            <div class="small text-muted">
                Based on posted vouchers in the selected period.
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-semibold mb-2">Income</h6>

                    @forelse($incomeGroups as $g)
                        <div class="border rounded mb-3">
                            <div class="bg-light px-3 py-2 small fw-semibold">
                                {{ $g['group']->name }}
                            </div>

                            <div class="p-2">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        @foreach($g['accounts'] as $row)
                                            <tr>
                                                <td class="small">{{ $row['account']->name }}</td>
                                                <td class="small text-end">{{ number_format($row['amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-light fw-semibold">
                                            <td class="small text-end">Total {{ $g['group']->name }}</td>
                                            <td class="small text-end">{{ number_format($g['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small">No income entries for the period.</div>
                    @endforelse

                    <div class="border rounded p-2 bg-dark text-white">
                        <div class="small">Total Income</div>
                        <div class="fw-semibold">{{ number_format($totalIncome, 2) }}</div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h6 class="fw-semibold mb-2">Expense</h6>

                    @forelse($expenseGroups as $g)
                        <div class="border rounded mb-3">
                            <div class="bg-light px-3 py-2 small fw-semibold">
                                {{ $g['group']->name }}
                            </div>

                            <div class="p-2">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        @foreach($g['accounts'] as $row)
                                            <tr>
                                                <td class="small">{{ $row['account']->name }}</td>
                                                <td class="small text-end">{{ number_format($row['amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-light fw-semibold">
                                            <td class="small text-end">Total {{ $g['group']->name }}</td>
                                            <td class="small text-end">{{ number_format($g['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small">No expense entries for the period.</div>
                    @endforelse

                    <div class="border rounded p-2 bg-dark text-white">
                        <div class="small">Total Expense</div>
                        <div class="fw-semibold">{{ number_format($totalExpense, 2) }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="alert {{ $profit >= 0 ? 'alert-success' : 'alert-danger' }} mb-0">
                        <div class="fw-semibold">
                            {{ $profit >= 0 ? 'Profit' : 'Loss' }}: {{ number_format(abs($profit), 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
