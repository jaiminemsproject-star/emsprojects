@extends('layouts.erp')

@section('title', 'Trial Balance')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Trial Balance (Group-wise)</h1>

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
                    <label class="form-label form-label-sm">Project (optional)</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">-- All Projects (Company TB) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected((string) $projectId === (string) $p->id)>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text small">
                        If a project is selected, TB shows <strong>only voucher movements</strong> tagged to that project (opening balances are company-level and are excluded).
                    </div>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Apply
                    </button>

                    <a href="{{ route('accounting.reports.trial-balance') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>

                    <a href="{{ route('accounting.reports.trial-balance', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(abs($difference) > 0.01)
        <div class="alert alert-danger">
            <div class="small">
                <strong>Trial Balance is not matching.</strong>
                Difference (Dr - Cr): <strong>{{ number_format($difference, 2) }}</strong>.
                This usually means some voucher(s) are unbalanced or data is incomplete.
                You can check the <strong>Unbalanced Vouchers</strong> report.
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div class="fw-semibold small">
                Trial Balance as on {{ optional($asOfDate)->toDateString() }}
                @if($projectId)
                    <span class="text-muted">(Project filtered)</span>
                @endif
            </div>
            <div class="small text-muted">
                Posted vouchers only
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 26%;">Group</th>
                            <th style="width: 10%;">Account Code</th>
                            <th style="width: 34%;">Account Name</th>
                            <th style="width: 15%;" class="text-end">Debit</th>
                            <th style="width: 15%;" class="text-end">Credit</th>
                        </tr>
                    </thead>
                    @php
                        $ledgerTo = optional($asOfDate)->toDateString();
                        $ledgerFrom = optional($asOfDate)->copy()->startOfMonth()->toDateString();
                    @endphp
                    <tbody>
                        @php
                            $currentGroupId = null;
                            $currentGroupName = '';
                            $currentGroupNature = '';
                            $groupDebit = 0.0;
                            $groupCredit = 0.0;
                            $hasRows = count($rows) > 0;
                        @endphp

                        @if(count($rows))
                        @foreach($rows as $row)
                            @php
                                $group = $row['group'];
                                $account = $row['account'];
                                $gid = $group?->id ?? 0;
                            @endphp

                            @if($currentGroupId !== $gid)
                                {{-- Print previous group subtotal if any --}}
                                @if(!is_null($currentGroupId))
                                    <tr class="table-light fw-semibold">
                                        <td class="small text-end" colspan="3">
                                            Total {{ $currentGroupName }}
                                        </td>
                                        <td class="small text-end">
                                            {{ number_format($groupDebit, 2) }}
                                        </td>
                                        <td class="small text-end">
                                            {{ number_format($groupCredit, 2) }}
                                        </td>
                                    </tr>
                                @endif

                                {{-- Reset counters for new group --}}
                                @php
                                    $currentGroupId = $gid;
                                    $currentGroupName = $group?->name ?? 'Ungrouped';
                                    $currentGroupNature = $group?->nature ?? '';
                                    $groupDebit = 0.0;
                                    $groupCredit = 0.0;
                                @endphp

                                {{-- Group header row --}}
                                <tr class="table-secondary">
                                    <td class="small fw-semibold" colspan="5">
                                        {{ $currentGroupName }}
                                        @if($currentGroupNature)
                                            <span class="text-muted">({{ ucfirst($currentGroupNature) }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            {{-- Account row --}}
                            @php
                                $groupDebit += $row['debit'];
                                $groupCredit += $row['credit'];
                            @endphp
                            <tr>
                                <td class="small"></td>
                                <td class="small">{{ $account->code }}</td>
                                <td class="small">
                                    <a href="{{ route('accounting.reports.ledger', array_filter([
                                        'account_id' => $account->id,
                                        'from_date' => $ledgerFrom,
                                        'to_date' => $ledgerTo,
                                        'project_id' => $projectId,
                                    ])) }}" class="text-decoration-none">
                                        {{ $account->name }}
                                    </a>
                                </td>
                                <td class="small text-end">
                                    {{ number_format($row['debit'], 2) }}
                                </td>
                                <td class="small text-end">
                                    {{ number_format($row['credit'], 2) }}
                                </td>
                            </tr>
                                                @endforeach
                        @else

                            <tr>
                                <td colspan="5" class="text-center small text-muted py-2">
                                    No data for the selected filters.
                                </td>
                            </tr>
                                                @endif

                        {{-- Last group subtotal --}}
                        @if($hasRows && !is_null($currentGroupId))
                            <tr class="table-light fw-semibold">
                                <td class="small text-end" colspan="3">
                                    Total {{ $currentGroupName }}
                                </td>
                                <td class="small text-end">
                                    {{ number_format($groupDebit, 2) }}
                                </td>
                                <td class="small text-end">
                                    {{ number_format($groupCredit, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    @if(count($rows))
                        <tfoot>
                            <tr class="table-dark fw-semibold text-white">
                                <td colspan="3" class="text-end small">Grand Total</td>
                                <td class="small text-end">{{ number_format($grandDebit, 2) }}</td>
                                <td class="small text-end">{{ number_format($grandCredit, 2) }}</td>
                            </tr>
                            <tr class="{{ abs($difference) > 0.01 ? 'table-danger' : 'table-light' }} fw-semibold">
                                <td colspan="3" class="text-end small">Difference (Dr - Cr)</td>
                                <td class="small text-end">{{ number_format($difference, 2) }}</td>
                                <td class="small"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
