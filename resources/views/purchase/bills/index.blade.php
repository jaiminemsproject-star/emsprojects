@extends('layouts.erp')

@section('title', 'Purchase Bills')

@section('content')
@php
    $rows = $bills->getCollection();
    $draftCount = $rows->where('status', 'draft')->count();
    $postedCount = $rows->where('status', 'posted')->count();
    $cancelledCount = $rows->where('status', 'cancelled')->count();
    $pagePayable = (float) $rows->sum(fn($b) => (float) (($b->total_amount ?? 0) + ($b->tcs_amount ?? 0) - ($b->tds_amount ?? 0)));
@endphp
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0"><i class="bi bi-receipt-cutoff me-1"></i> Purchase Bills</h1>
            <div class="small text-muted">Invoice posting, tax impact, and net payable tracking.</div>
        </div>
        <a href="{{ route('purchase.bills.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Purchase Bill
        </a>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-3 col-6">
            <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Draft</div><div class="h5 mb-0">{{ $draftCount }}</div></div></div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Posted</div><div class="h5 mb-0">{{ $postedCount }}</div></div></div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Cancelled</div><div class="h5 mb-0">{{ $cancelledCount }}</div></div></div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Page Net Payable</div><div class="h5 mb-0">{{ number_format($pagePayable, 2) }}</div></div></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control form-control-sm"
                           placeholder="Bill no / invoice no">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Supplier / Contractor</label>
                    <select name="supplier_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>
                                {{ $p->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="posted" @selected(request('status') === 'posted')>Posted</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-2 mt-2 d-flex gap-1">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                    <a href="{{ route('purchase.bills.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 16%">Bill No</th>
                        <th style="width: 12%">Posting / Bill</th>
                        <th>Supplier</th>
                        <th>Project</th>
                        <th style="width: 11%" class="text-end">Basic</th>
                        <th style="width: 11%" class="text-end">GST</th>
                        <th style="width: 11%" class="text-end">Invoice Total</th>
                        <th style="width: 10%" class="text-end">TDS</th>
                        <th style="width: 11%" class="text-end">Net Payable</th>
                        <th style="width: 9%">Status</th>
                        <th style="width: 14%" class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($bills as $bill)
                        @php
                            $invoice = (float) ($bill->total_amount ?? 0);
                            $tcs     = (float) ($bill->tcs_amount ?? 0);
                            $tds     = (float) ($bill->tds_amount ?? 0);
                            $net     = ($invoice + $tcs) - $tds;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $bill->bill_number }}</div>
                                @if($bill->status === 'posted' && $bill->voucher)
                                    <div class="small text-muted">Voucher: {{ $bill->voucher->voucher_no ?? ('#' . $bill->voucher->id) }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ ($bill->posting_date ?: $bill->bill_date)?->format('d-m-Y') }}</div>
                                <div class="small text-muted">Bill: {{ $bill->bill_date?->format('d-m-Y') }}</div>
                            </td>
                            <td>{{ $bill->supplier?->name }}</td>
                            <td>
                                @php
                                    $proj = $bill->project ?? $bill->purchaseOrder?->project;
                                    $multi = false;

                                    if (!$proj && $bill->relationLoaded('expenseLines')) {
                                        $unique = $bill->expenseLines
                                            ->map(fn($l) => $l->project)
                                            ->filter()
                                            ->unique('id')
                                            ->values();
                                        if ($unique->count() === 1) {
                                            $proj = $unique->first();
                                        } elseif ($unique->count() > 1) {
                                            $multi = true;
                                        }
                                    }
                                @endphp
                                @if($proj)
                                    <div class="fw-semibold">
                                        {{ $proj->code }}
                                        @if($multi)
                                            <span class="badge text-bg-info ms-1">Multiple</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">{{ $proj->name }}</div>
                                @elseif($multi)
                                    <span class="badge text-bg-info">Multiple</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $bill->total_basic, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $bill->total_tax, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($invoice, 2) }}</td>
                            <td class="text-end">{{ number_format($tds, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($net, 2) }}</td>
                            <td>
                                @if($bill->status === 'posted')
                                    <span class="badge text-bg-success">Posted</span>
                                @elseif($bill->status === 'cancelled')
                                    <span class="badge text-bg-danger">Cancelled</span>
                                @else
                                    <span class="badge text-bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('purchase.bills.show', $bill) }}"
                                   class="btn btn-sm btn-outline-secondary">View</a>

                                @if($bill->status !== 'posted')
                                    <a href="{{ route('purchase.bills.edit', $bill) }}"
                                       class="btn btn-sm btn-outline-primary ms-1">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">No purchase bills found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($bills->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $bills->count() }} of {{ $bills->total() }} bills</small>
                {{ $bills->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


