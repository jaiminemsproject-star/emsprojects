@extends('layouts.erp')

@section('title', 'Purchase Orders')

@section('content')
    @php
        $rows = $orders->getCollection();
        $draftCount = $rows->where('status', 'draft')->count();
        $approvedCount = $rows->where('status', 'approved')->count();
        $cancelledCount = $rows->where('status', 'cancelled')->count();
        $pageTotal = (float) $rows->sum(fn($o) => (float) ($o->total_amount ?? 0));
    @endphp
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0"><i class="bi bi-file-earmark-text me-1"></i> Purchase Orders</h1>
                <div class="small text-muted">Monitor vendor commitments, approvals, and delivery timelines.</div>
            </div>

            <a href="{{ route('purchase-rfqs.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-link-45deg me-1"></i> From RFQs
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-2 mb-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Draft</div><div class="h5 mb-0">{{ $draftCount }}</div></div></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Approved</div><div class="h5 mb-0">{{ $approvedCount }}</div></div></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Cancelled</div><div class="h5 mb-0">{{ $cancelledCount }}</div></div></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light"><div class="card-body py-2"><div class="small text-muted">Page Total</div><div class="h5 mb-0">{{ number_format($pageTotal, 2) }}</div></div></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" class="form-control form-control-sm" value="{{ request('q') }}" placeholder="PO number or vendor">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(($vendors ?? collect()) as $v)
                                <option value="{{ $v->id }}" @selected((string) request('vendor_id') === (string) $v->id)>
                                    {{ $v->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(($projects ?? collect()) as $p)
                                <option value="{{ $p->id }}" @selected((string) request('project_id') === (string) $p->id)>
                                    {{ $p->code }} - {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" type="submit">Go</button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th style="width: 120px;">PO No</th>
                            <th>Vendor</th>
                            <th>Project</th>
                            <th style="width: 110px;">PO Date</th>
                            <th style="width: 120px;">Expected Del.</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 130px;" class="text-end">Total</th>
                            <th style="width: 120px;" class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('purchase-orders.show', $order) }}" class="fw-semibold text-decoration-none">
                                        {{ $order->code }}
                                    </a>
                                </td>
                                <td>{{ optional($order->vendor)->name ?? '-' }}</td>
                                <td>
                                    @if($order->project)
                                        {{ $order->project->code }} - {{ $order->project->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ optional($order->po_date)?->format('d-m-Y') ?: '-' }}</td>
                                <td>{{ optional($order->expected_delivery_date)?->format('d-m-Y') ?: '-' }}</td>
                                <td>
                                    <span class="badge
                                        @if($order->status === 'approved')
                                            bg-success
                                        @elseif($order->status === 'cancelled')
                                            bg-danger
                                        @else
                                            bg-secondary
                                        @endif
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">
                                    @if($order->total_amount !== null)
                                        {{ number_format($order->total_amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('purchase-orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No purchase orders found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($orders instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="card-footer py-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Showing {{ $orders->count() }} of {{ $orders->total() }} purchase orders</small>
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
