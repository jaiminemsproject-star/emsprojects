@extends('layouts.erp')

@section('title', 'Purchase Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Purchase Orders</h1>
        {{-- Manual PO create can be added later --}}
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

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 110px;">PO No</th>
                        <th>Vendor</th>
                        <th>Project</th>
                        <th style="width: 110px;">PO Date</th>
                        <th style="width: 120px;">Expected Del.</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-end">Total</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-orders.show', $order) }}">
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
                            <td class="text-end">
                                @if($order->total_amount !== null)
                                    {{ number_format($order->total_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                No purchase orders found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer py-2">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
