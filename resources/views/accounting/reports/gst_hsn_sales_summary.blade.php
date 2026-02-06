@extends('layouts.erp')

@section('title', 'GST Sales SAC/HSN Summary')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">GST Sales SAC/HSN Summary</h1>

    @if(!empty($missingTables))
        <div class="alert alert-warning py-2">
            <strong>Not available:</strong>
            Missing required tables: {{ implode(', ', $missingTables) }}.
            <div class="small mt-1">
                Please run pending migrations for Client RA Bills module before using this report.
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

                <div class="col-md-2">
                    <label class="form-label form-label-sm">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        @php
    $s = request('status', $status ?? 'posted');
@endphp
                        <option value="posted" {{ $s === 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="approved" {{ $s === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="submitted" {{ $s === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="draft" {{ $s === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="all" {{ $s === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <button class="btn btn-sm btn-primary">Apply</button>
                </div>

                <div class="col-md-12">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('accounting.reports.gst-hsn-sales-summary.export', request()->all()) }}">
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
                                <th class="text-muted" style="width: 180px;">Taxable Value</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['taxable'] ?? 0) }}</td>

                                <th class="text-muted" style="width: 120px;">CGST</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['cgst'] ?? 0) }}</td>

                                <th class="text-muted" style="width: 120px;">SGST</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['sgst'] ?? 0) }}</td>

                                <th class="text-muted" style="width: 120px;">IGST</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['igst'] ?? 0) }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Total GST</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['gst_total'] ?? 0) }}</td>

                                <th class="text-muted">Gross Total</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['gross_total'] ?? 0) }}</td>

                                <th class="text-muted">Rows</th>
                                <td class="text-end" colspan="5">{{ $totals['rows'] ?? 0 }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead>
                            <tr>
                                <th>SAC/HSN</th>
                                <th class="text-end">GST Rate (%)</th>
                                <th class="text-end">Taxable</th>
                                <th class="text-end">CGST</th>
                                <th class="text-end">SGST</th>
                                <th class="text-end">IGST</th>
                                <th class="text-end">GST Total</th>
                                <th class="text-end">Gross Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
    $hasRows = isset($rows) && method_exists($rows, 'count') && $rows->count() > 0;
@endphp

                            @if($hasRows)
                                @foreach($rows as $r)
                                    @php
                                        $taxablePaise = \App\Support\MoneyHelper::toPaise($r->taxable ?? 0);
                                        $cgstPaise    = \App\Support\MoneyHelper::toPaise($r->cgst ?? 0);
                                        $sgstPaise    = \App\Support\MoneyHelper::toPaise($r->sgst ?? 0);
                                        $igstPaise    = \App\Support\MoneyHelper::toPaise($r->igst ?? 0);
                                        $gstPaise     = $cgstPaise + $sgstPaise + $igstPaise;
                                        $grossPaise   = $taxablePaise + $gstPaise;
                                    @endphp
                                    <tr>
                                        <td>{{ $r->hsn_sac }}</td>
                                        <td class="text-end">{{ $r->gst_rate }}</td>
                                        <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($taxablePaise) }}</td>
                                        <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($cgstPaise) }}</td>
                                        <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($sgstPaise) }}</td>
                                        <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($igstPaise) }}</td>
                                        <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($gstPaise) }}</td>
                                        <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($grossPaise) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No sales lines found for selected filters.
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mt-3 mb-0">
                        Notes:
                        <br>• This summary allocates bill-level GST totals proportionally to line <code>current_amount</code>.
                        <br>• Ensure you maintain <code>sac_hsn_code</code> on Client RA Bill lines for accurate grouping.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
