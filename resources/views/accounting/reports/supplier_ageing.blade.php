@extends('layouts.erp')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Supplier Ageing</h4>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select class="form-select form-select-sm" name="supplier_id">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected($selectedSupplierId == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control form-control-sm" name="as_of_date" value="{{ optional($asOfDate)->toDateString() }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm">Filter</button>
        </div>
    </form>

    <div class="card">
        <div class="card-header"><strong>Summary</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th class="text-end">Not Due</th>
                        <th class="text-end">0-30</th>
                        <th class="text-end">31-60</th>
                        <th class="text-end">61-90</th>
                        <th class="text-end">91-180</th>
                        <th class="text-end">>180</th>
                        <th class="text-end">Total</th>
                        @if($dnEnabled)
                            <th class="text-end">Debit Notes</th>
                            <th class="text-end">Net</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaryRows as $r)
                        <tr>
                            <td>{{ $r['party']->name }}</td>
                            <td class="text-end">{{ number_format($r['buckets']['not_due'],2) }}</td>
                            <td class="text-end">{{ number_format($r['buckets']['0_30'],2) }}</td>
                            <td class="text-end">{{ number_format($r['buckets']['31_60'],2) }}</td>
                            <td class="text-end">{{ number_format($r['buckets']['61_90'],2) }}</td>
                            <td class="text-end">{{ number_format($r['buckets']['91_180'],2) }}</td>
                            <td class="text-end">{{ number_format($r['buckets']['over_180'],2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($r['total_outstanding'],2) }}</td>
                            @if($dnEnabled)
                                <td class="text-end">{{ number_format($r['debit_notes'],2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($r['net_outstanding'],2) }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $dnEnabled ? 10 : 8 }}" class="text-muted">No data</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-semibold">
                        <td>Total</td>
                        <td class="text-end">{{ number_format($grand['not_due'],2) }}</td>
                        <td class="text-end">{{ number_format($grand['0_30'],2) }}</td>
                        <td class="text-end">{{ number_format($grand['31_60'],2) }}</td>
                        <td class="text-end">{{ number_format($grand['61_90'],2) }}</td>
                        <td class="text-end">{{ number_format($grand['91_180'],2) }}</td>
                        <td class="text-end">{{ number_format($grand['over_180'],2) }}</td>
                        <td class="text-end">{{ number_format($grand['total'],2) }}</td>
                        @if($dnEnabled)
                            <td class="text-end">{{ number_format($grand['debit_notes'],2) }}</td>
                            <td class="text-end">{{ number_format($grand['net_total'],2) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
