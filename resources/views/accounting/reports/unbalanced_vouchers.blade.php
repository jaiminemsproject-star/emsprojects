@extends('layouts.erp')

@section('title', 'Unbalanced Vouchers')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Unbalanced Vouchers (Validation)</h1>

    <div class="alert alert-warning">
        <div class="small">
            This report lists <strong>posted vouchers</strong> where total <strong>Debit</strong> is not equal to total <strong>Credit</strong>.
            If this list is empty, it is a good sign: your double-entry data is internally consistent.
        </div>
    </div>

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
                    <label class="form-label form-label-sm">Voucher type (optional)</label>
                    <select name="voucher_type" class="form-select form-select-sm">
                        <option value="">-- All types --</option>
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
                        <option value="">-- All projects --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected((int)request('project_id') === $p->id)>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> View
                    </button>
                    <a href="{{ route('accounting.reports.unbalanced-vouchers') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>

                    <a href="{{ route('accounting.reports.unbalanced-vouchers', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="btn btn-outline-success btn-sm float-end">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div class="fw-semibold small">
                Unbalanced vouchers: {{ optional($fromDate)->toDateString() }} to {{ optional($toDate)->toDateString() }}
                @if($type)
                    ({{ strtoupper($type) }} only)
                @endif
            </div>
            <div class="small text-muted">
                Company #{{ $companyId }}
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">Date</th>
                            <th style="width: 12%;">Voucher No</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 18%;">Project</th>
                            <th style="width: 18%;">Reference</th>
                            <th style="width: 20%;">Narration</th>
                            <th style="width: 10%;" class="text-end">Debit</th>
                            <th style="width: 10%;" class="text-end">Credit</th>
                            <th style="width: 10%;" class="text-end">Diff</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($rows))
                        @foreach($rows as $r)
                            @php
                                $diff = (float) $r->diff;
                                $proj = $r->project_id ? ($projectMap[$r->project_id]->code ?? ('#' . $r->project_id)) : '';
                            @endphp
                            <tr class="{{ abs($diff) >= 0.01 ? 'table-warning' : '' }}">
                                <td class="small">{{ $r->voucher_date }}</td>
                                <td class="small">
                                    <a href="{{ route('accounting.vouchers.show', $r->id) }}" class="text-decoration-none">
                                        {{ $r->voucher_no }}
                                    </a>
                                </td>
                                <td class="small text-uppercase">{{ $r->voucher_type }}</td>
                                <td class="small">{{ $proj }}</td>
                                <td class="small">{{ $r->reference }}</td>
                                <td class="small">{{ $r->narration }}</td>
                                <td class="small text-end">{{ number_format((float)$r->debit_total, 2) }}</td>
                                <td class="small text-end">{{ number_format((float)$r->credit_total, 2) }}</td>
                                <td class="small text-end fw-semibold">
                                    {{ number_format($diff, 2) }}
                                </td>
                            </tr>
                                                @endforeach
                        @else

                            <tr>
                                <td colspan="9" class="text-center small text-muted py-3">
                                    No unbalanced vouchers found for the selected criteria.
                                </td>
                            </tr>
                                                @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
