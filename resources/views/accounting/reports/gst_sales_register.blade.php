@extends('layouts.erp')

@section('title', 'GST Sales Register')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">GST Sales Register (Invoice-wise)</h1>

    @if(($missingTable ?? false) === true)
        <div class="alert alert-warning">
            Sales/RA tables are not available in this database (<code>client_ra_bills</code> missing).
            Please run migrations for the Client RA Bill module to enable this report.
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
                    <label class="form-label form-label-sm">Client</label>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">All clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (string) $clientId === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->name }}{{ $c->gstin ? ' - ' . $c->gstin : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label form-label-sm">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        @php
    $curStatus = request('status', $status ?? 'posted');
@endphp
<option value="posted" {{ $curStatus === 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="draft" {{ $curStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="cancelled" {{ $curStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="all" {{ $curStatus === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <button class="btn btn-sm btn-primary" {{ ($missingTable ?? false) ? 'disabled' : '' }}>Apply</button>
                </div>

                <div class="col-md-12">
                    <a class="btn btn-sm btn-outline-secondary {{ ($missingTable ?? false) ? 'disabled' : '' }}"
                       href="{{ ($missingTable ?? false) ? '#' : route('accounting.reports.gst-sales-register.export', request()->all()) }}">
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
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['total_gst'] ?? 0) }}</td>

                                <th class="text-muted">Invoice Total</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['invoice'] ?? 0) }}</td>

                                <th class="text-muted">TDS</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['tds_amount'] ?? 0) }}</td>

                                <th class="text-muted">Receivable</th>
                                <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($totals['receivable'] ?? 0) }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Bills</th>
                                <td class="text-end" colspan="7">{{ $bills->count() }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>RA No</th>
                                <th>Voucher No</th>
                                <th>Client</th>
                                <th>GSTIN</th>
                                <th>Project</th>
                                <th class="text-end">Taxable</th>
                                <th class="text-end">CGST</th>
                                <th class="text-end">SGST</th>
                                <th class="text-end">IGST</th>
                                <th class="text-end">GST Total</th>
                                <th class="text-end">Invoice</th>
                                <th class="text-end">TDS</th>
                                <th class="text-end">Receivable</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($bills))
                            @foreach($bills as $bill)
                                @php
                                    $taxablePaise = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('net_amount') ?? $bill->net_amount ?? 0);
                                    $cgstPaise    = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('cgst_amount') ?? $bill->cgst_amount ?? 0);
                                    $sgstPaise    = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('sgst_amount') ?? $bill->sgst_amount ?? 0);
                                    $igstPaise    = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('igst_amount') ?? $bill->igst_amount ?? 0);
                                    $gstPaise     = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('total_gst') ?? $bill->total_gst ?? 0);
                                    $invoicePaise = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('total_amount') ?? $bill->total_amount ?? 0);
                                    $tdsPaise     = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('tds_amount') ?? $bill->tds_amount ?? 0);
                                    $recvPaise    = \App\Support\MoneyHelper::toPaise($bill->getRawOriginal('receivable_amount') ?? $bill->receivable_amount ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ optional($bill->bill_date)->toDateString() }}</td>
                                    <td>{{ $bill->invoice_number }}</td>
                                    <td>{{ $bill->ra_number }}</td>
                                    <td>
                                        @if($bill->voucher)
                                            <a href="{{ route('accounting.vouchers.show', $bill->voucher) }}" class="text-decoration-none">
                                                {{ $bill->voucher->voucher_no }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $bill->client?->name }}</td>
                                    <td>{{ $bill->client?->gstin }}</td>
                                    <td>{{ $bill->project?->name }}</td>

                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($taxablePaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($cgstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($sgstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($igstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($gstPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($invoicePaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($tdsPaise) }}</td>
                                    <td class="text-end">{{ \App\Support\MoneyHelper::fromPaise($recvPaise) }}</td>

                                    <td>
                                        <span class="badge bg-{{ $bill->status === 'posted' ? 'success' : ($bill->status === 'draft' ? 'secondary' : 'danger') }}">
                                            {{ $bill->status }}
                                        </span>
                                    </td>
                                </tr>
                                                        @endforeach
                            @else

                                <tr>
                                    <td colspan="16" class="text-center text-muted py-4">
                                        No sales bills found for selected filters.
                                    </td>
                                </tr>
                                                        @endif
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mt-3 mb-0">
                        Notes:
                        <br>• This register uses Client RA Bills / Sales Invoices module totals (net, GST split, and receivable).
                        <br>• If you generate sales vouchers without Client RA Bills, they will not appear here (we can add a voucher-based sales register in a later phase if required).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
