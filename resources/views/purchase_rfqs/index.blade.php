@extends('layouts.erp')

@section('title', 'Purchase RFQs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Purchase RFQs</h1>

        @can('purchase.rfq.create')
            <a href="{{ route('purchase-rfqs.create') }}" class="btn btn-sm btn-primary">
                + New RFQ
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">RFQ No</th>
                        <th>Project</th>
                        <th style="width: 120px;">RFQ Date</th>
                        <th style="width: 120px;">Due Date</th>
                        <th style="width: 120px;">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rfqs as $rfq)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-rfqs.show', $rfq) }}">
                                    {{ $rfq->code }}
                                </a>
                            </td>
                            <td>
                                @if($rfq->project)
                                    {{ $rfq->project->code }} - {{ $rfq->project->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ optional($rfq->rfq_date)?->format('d-m-Y') ?: '-' }}</td>
                            <td>{{ optional($rfq->due_date)?->format('d-m-Y') ?: '-' }}</td>
                            <td>{{ ucfirst($rfq->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No RFQs found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($rfqs instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer py-2">
                {{ $rfqs->links() }}
            </div>
        @endif
    </div>
@endsection
