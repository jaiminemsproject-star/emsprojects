@extends('layouts.erp')

@section('title', 'Ledger')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Ledger Statement</h1>

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
                    <label class="form-label form-label-sm">Project (optional)</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">-- All Projects (Company Ledger) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected((string) $projectId === (string) $p->id)>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text small">
                        If a project is selected, opening balance is calculated from <strong>project-tagged movements</strong> only.
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label form-label-sm">Account</label>
                    <select name="account_id" class="form-select form-select-sm">
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}" @selected(optional($account)->id === $a->id)>
                                {{ $a->code }} - {{ $a->name }}@if(!$a->is_active) (Inactive)@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                    <a href="{{ route('accounting.reports.ledger') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>

                    @if($account)
                        <a href="{{ route('accounting.reports.ledger', array_merge(request()->all(), ['export' => 'csv'])) }}"
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-download"></i> Export CSV
                        </a>
                    @endif


                    <div class="form-check form-check-inline ms-2">
                        <input class="form-check-input" type="checkbox" id="show_breakdown" name="show_breakdown" value="1"
                               @checked(request()->boolean('show_breakdown'))>
                        <label class="form-check-label small" for="show_breakdown">
                            Show voucher break-up (TDS / GST / Retention etc.)
                        </label>
                    </div>

                    <span class="ms-2 small text-muted">
                        Company #{{ $companyId }}
                        @if($projectId)
                            · Project filter applied
                        @endif
                    </span>
                </div>
            </form>
        </div>
    </div>

    @if(!$account)
        <div class="alert alert-info">No accounts found.</div>
    @else
        @php
            $openingType = $openingBalance >= 0 ? 'Dr' : 'Cr';
            $closingType = $closingBalance >= 0 ? 'Dr' : 'Cr';
        @endphp

        <div class="card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <div class="fw-semibold small">
                    Ledger: {{ $account->name }}
                    <span class="text-muted">({{ $account->code }})</span>
                </div>
                <div class="small text-muted">
                    Period: {{ optional($fromDate)->toDateString() }} to {{ optional($toDate)->toDateString() }}
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Opening Balance</div>
                            <div class="fw-semibold">
                                {{ number_format(abs($openingBalance), 2) }} {{ $openingType }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Closing Balance</div>
                            <div class="fw-semibold">
                                {{ number_format(abs($closingBalance), 2) }} {{ $closingType }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                            <th style="width: 12%" class="text-end">Running</th>
                            <th style="width: 6%" class="text-center">Dr/Cr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $running = $openingBalance; @endphp

                        <tr class="table-light">
                            <td colspan="4" class="small fw-semibold">Opening Balance</td>
                            <td class="text-end small">&nbsp;</td>
                            <td class="text-end small">&nbsp;</td>
                            <td class="text-end small fw-semibold">{{ number_format(abs($running), 2) }}</td>
                            <td class="text-center small fw-semibold">{{ $running >= 0 ? 'Dr' : 'Cr' }}</td>
                        </tr>

                        @if(count($ledgerEntries))
                        @foreach($ledgerEntries as $entry)
                            @php
                                $running += ((float) $entry->debit - (float) $entry->credit);
                            @endphp
                            <tr>
                                <td class="small">{{ optional($entry->voucher->voucher_date)->toDateString() }}</td>
                                <td class="small">
                                    <a href="{{ route('accounting.vouchers.show', $entry->voucher) }}" class="text-decoration-none">
                                        {{ $entry->voucher->voucher_no }}
                                    </a>
                                </td>
                                <td class="small text-uppercase">{{ $entry->voucher->voucher_type }}</td>
                                <td class="small">
                                    <div class="fw-semibold">{{ $entry->description ?: ($entry->voucher->narration ?: '-') }}</div>
                                    @if($entry->costCenter)
                                        <div class="text-muted">Cost Center: {{ $entry->costCenter->name }}</div>
                                    @endif
                                    @if($entry->voucher->reference)
                                        <div class="text-muted">Ref: {{ $entry->voucher->reference }}</div>
                                    @endif
                                </td>
                                <td class="small text-end">{{ number_format($entry->debit, 2) }}</td>
                                <td class="small text-end">{{ number_format($entry->credit, 2) }}</td>
                                <td class="small text-end fw-semibold">{{ number_format(abs($running), 2) }}</td>
                                <td class="small text-center fw-semibold">{{ $running >= 0 ? 'Dr' : 'Cr' }}</td>
                            </tr>

                            @if(($showBreakdown ?? false) && isset($voucherLinesByVoucher))
                                @php
                                    $vLines = $voucherLinesByVoucher->get($entry->voucher_id, collect());
                                @endphp

                                @if($vLines->count() > 1)
                                    <tr class="table-light">
                                        <td colspan="8" class="p-2">
                                            <div class="small text-muted mb-1">Voucher break-up</div>
                                            <table class="table table-sm table-bordered mb-0 align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="small">Account</th>
                                                        <th class="small">Line Description</th>
                                                        <th class="small text-end" style="width: 12%">Debit</th>
                                                        <th class="small text-end" style="width: 12%">Credit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vLines as $vl)
                                                        <tr @class(['table-primary' => (int) $vl->id === (int) $entry->id])>
                                                            <td class="small">
                                                                {{ $vl->account?->code }} - {{ $vl->account?->name }}
                                                            </td>
                                                            <td class="small">{{ $vl->description }}</td>
                                                            <td class="small text-end">{{ number_format($vl->debit, 2) }}</td>
                                                            <td class="small text-end">{{ number_format($vl->credit, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                                                @endforeach
                        @else

                            <tr>
                                <td colspan="8" class="text-center small text-muted py-2">
                                    No ledger entries for the selected filters.
                                </td>
                            </tr>
                                                @endif

                        <tr class="table-dark text-white fw-semibold">
                            <td colspan="6" class="text-end small">Closing Balance</td>
                            <td class="text-end small">{{ number_format(abs($closingBalance), 2) }}</td>
                            <td class="text-center small">{{ $closingBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection