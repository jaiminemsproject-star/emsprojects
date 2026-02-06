@extends('layouts.erp')

@section('title', 'Fund Flow Statement')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Fund Flow (Sources &amp; Applications of Funds)</h1>

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

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm mt-3 mt-md-0">
                        <i class="bi bi-filter"></i> View
                    </button>
                    <a href="{{ route('accounting.reports.fund-flow') }}"
                       class="btn btn-outline-secondary btn-sm mt-3 mt-md-0">
                        Reset
                    </a>
                </div>

                <div class="col-md-2 text-end small text-muted">
                    Company #{{ $companyId }}
                </div>
            </form>

            <div class="mt-2 small text-muted">
                Based on movements in Balance Sheet groups (nature: asset / liability / equity).
                Income and expense groups are excluded; difference between total sources and
                applications roughly corresponds to P&amp;L impact for the period.
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Sources of funds --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <div class="fw-semibold small">Sources of Funds</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 65%;">Particulars</th>
                                    <th style="width: 35%;" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sources as $row)
                                    <tr>
                                        <td class="small">
                                            {{ $row['label'] }}
                                            @if(! empty($row['note']))
                                                <span class="text-muted">({{ $row['note'] }})</span>
                                            @endif
                                        </td>
                                        <td class="small text-end">
                                            {{ number_format($row['amount'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="small text-muted text-center py-2">
                                            No sources identified for this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-dark text-white fw-semibold">
                                    <td class="small text-end">Total Sources</td>
                                    <td class="small text-end">{{ number_format($totalSources, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Applications of funds --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <div class="fw-semibold small">Applications of Funds</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 65%;">Particulars</th>
                                    <th style="width: 35%;" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $row)
                                    <tr>
                                        <td class="small">
                                            {{ $row['label'] }}
                                            @if(! empty($row['note']))
                                                <span class="text-muted">({{ $row['note'] }})</span>
                                            @endif
                                        </td>
                                        <td class="small text-end">
                                            {{ number_format($row['amount'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="small text-muted text-center py-2">
                                            No applications identified for this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-dark text-white fw-semibold">
                                    <td class="small text-end">Total Applications</td>
                                    <td class="small text-end">{{ number_format($totalApps, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reconciliation --}}
    <div class="card">
        <div class="card-body py-2 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Period: {{ optional($fromDate)->toDateString() }} to {{ optional($toDate)->toDateString() }}
            </div>
            <div class="small fw-semibold">
                Difference (Sources - Applications):
                {{ number_format($difference, 2) }}
            </div>
        </div>
    </div>
</div>
@endsection
