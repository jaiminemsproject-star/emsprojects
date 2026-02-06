@extends('layouts.erp')

@section('title', 'Day Book')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Day Book</h1>

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

                <div class="col-md-3">
                    <label class="form-label form-label-sm">Voucher type</label>
                    <select name="voucher_type" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($voucherTypes as $vt)
                            <option value="{{ $vt }}" @selected($type === $vt)>
                                {{ strtoupper($vt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label form-label-sm">Project (optional)</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">-- All Projects --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected((string) $projectId === (string) $p->id)>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                    <a href="{{ route('accounting.reports.day-book') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>

                    <a href="{{ route('accounting.reports.day-book', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download"></i> Export CSV
                    </a>

                    <span class="ms-2 small text-muted">
                        Company #{{ $companyId }}
                    </span>
                </div>
            </form>
        </div>
    </div>

    @if(($unbalancedCount ?? 0) > 0)
        <div class="alert alert-warning">
            <div class="small">
                Warning: <strong>{{ $unbalancedCount }}</strong> voucher(s) in this range appear to be <strong>unbalanced</strong> (Debit ≠ Credit).
                Check the <a href="{{ route('accounting.reports.unbalanced-vouchers', array_merge(request()->all(), ['from_date' => optional($fromDate)->toDateString(), 'to_date' => optional($toDate)->toDateString()])) }}">Unbalanced Vouchers</a> report.
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div class="fw-semibold small">
                Vouchers from {{ optional($fromDate)->toDateString() }} to {{ optional($toDate)->toDateString() }}
                @if($type)
                    · Type: {{ strtoupper($type) }}
                @endif
                @if($projectId)
                    · Project filter applied
                @endif
            </div>
            <div class="small text-muted">
                Posted vouchers only.
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">Date</th>
                            <th style="width: 12%">Voucher No</th>
                            <th style="width: 10%">Type</th>
                            <th>Description</th>
                            <th style="width: 10%" class="text-end">Debit</th>
                            <th style="width: 10%" class="text-end">Credit</th>
                            <th style="width: 10%" class="text-end">Diff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandDebit = 0.0;
                            $grandCredit = 0.0;
                        @endphp

                        @if(count($vouchers))
                        @foreach($vouchers as $voucher)
                            @php
                                $debitTotal = (float) $voucher->lines->sum('debit');
                                $creditTotal = (float) $voucher->lines->sum('credit');
                                $diff = $debitTotal - $creditTotal;
                                $grandDebit += $debitTotal;
                                $grandCredit += $creditTotal;
                            @endphp
                            <tr class="{{ abs($diff) > 0.01 ? 'table-warning' : '' }}">
                                <td class="small">{{ optional($voucher->voucher_date)->toDateString() }}</td>
                                <td class="small fw-semibold">
                                    <a href="{{ route('accounting.vouchers.show', $voucher) }}" class="text-decoration-none">
                                        {{ $voucher->voucher_no }}
                                    </a>
                                </td>
                                <td class="small text-uppercase">{{ $voucher->voucher_type }}</td>
                                <td class="small">
                                    <div class="fw-semibold">{{ $voucher->narration ?: '-' }}</div>
                                    @if($voucher->reference)
                                        <div class="text-muted">Ref: {{ $voucher->reference }}</div>
                                    @endif
                                    @if($voucher->project)
                                        <div class="text-muted">Project: {{ $voucher->project->code }} - {{ $voucher->project->name }}</div>
                                    @endif
                                </td>
                                <td class="small text-end">{{ number_format($debitTotal, 2) }}</td>
                                <td class="small text-end">{{ number_format($creditTotal, 2) }}</td>
                                <td class="small text-end fw-semibold">{{ number_format($diff, 2) }}</td>
                            </tr>

                            {{-- Expanded lines --}}
                            <tr>
                                <td colspan="7" class="p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="small">Account</th>
                                                    <th class="small text-end" style="width: 15%">Debit</th>
                                                    <th class="small text-end" style="width: 15%">Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($voucher->lines as $line)
                                                    <tr>
                                                        <td class="small">
                                                            {{ $line->account?->name }}
                                                            <span class="text-muted">({{ $line->account?->code }})</span>
                                                            @if($line->description)
                                                                <div class="text-muted">{{ $line->description }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="small text-end">{{ number_format($line->debit, 2) }}</td>
                                                        <td class="small text-end">{{ number_format($line->credit, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                                                @endforeach
                        @else

                            <tr>
                                <td colspan="7" class="text-center small text-muted py-2">
                                    No vouchers found for the selected filters.
                                </td>
                            </tr>
                                                @endif

                        @if(count($vouchers))
                            <tr class="table-dark text-white fw-semibold">
                                <td colspan="4" class="text-end small">Grand Total</td>
                                <td class="small text-end">{{ number_format($grandDebit, 2) }}</td>
                                <td class="small text-end">{{ number_format($grandCredit, 2) }}</td>
                                <td class="small text-end">{{ number_format($grandDebit - $grandCredit, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
