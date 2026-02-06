@extends('layouts.erp')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Supplier Outstanding</h4>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select class="form-select form-select-sm" name="supplier_id">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected($selectedParty && $selectedParty->id == $s->id)>{{ $s->name }}</option>
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
                        <th class="text-end">Bill Amount</th>
                        <th class="text-end">Allocated</th>
                        <th class="text-end">Bill Outstanding</th>
                        @if($dnEnabled)
                            <th class="text-end">Debit Notes</th>
                            <th class="text-end">Net Outstanding</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaryRows as $r)
                        <tr>
                            <td>{{ $r['party']->name }}</td>
                            <td class="text-end">{{ number_format($r['bill_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($r['allocated'], 2) }}</td>
                            <td class="text-end">{{ number_format($r['outstanding'], 2) }}</td>
                            @if($dnEnabled)
                                <td class="text-end">{{ number_format($r['debit_notes'], 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($r['net_outstanding'], 2) }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $dnEnabled ? 6 : 4 }}" class="text-muted">No data</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-semibold">
                        <td>Total</td>
                        <td class="text-end">{{ number_format($grandTotalBill, 2) }}</td>
                        <td class="text-end">{{ number_format($grandTotalAlloc, 2) }}</td>
                        <td class="text-end">{{ number_format($grandTotalOutstd, 2) }}</td>
                        @if($dnEnabled)
                            <td class="text-end">{{ number_format($grandTotalDn, 2) }}</td>
                            <td class="text-end">{{ number_format($grandTotalNet, 2) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($selectedParty)
        <div class="card mt-3">
            <div class="card-header"><strong>Detail: {{ $selectedParty->name }}</strong></div>
            <div class="card-body">
                <h6>Open Bills</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Bill No</th><th>Date</th><th class="text-end">Outstanding</th></tr></thead>
                        <tbody>
                        @forelse($detailBills as $row)
                            @php $b = $row['bill']; @endphp
                            <tr>
                                <td>{{ $b->bill_number }}</td>
                                <td>{{ optional($b->bill_date)->toDateString() }}</td>
                                <td class="text-end">{{ number_format((float)$row['outstanding'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No open bills</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($dnEnabled)
                    <h6 class="mt-3">Purchase Debit Notes</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Note No</th><th>Date</th><th>Voucher</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                            @forelse($detailDebitNotes as $dn)
                                <tr>
                                    <td>{{ $dn->note_number }}</td>
                                    <td>{{ optional($dn->note_date)->toDateString() }}</td>
                                    <td>{{ $dn->voucher?->voucher_no }}</td>
                                    <td class="text-end">{{ number_format((float)$dn->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No debit notes</td></tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="fw-semibold">
                                    <td colspan="3">Debit Notes Total</td>
                                    <td class="text-end">{{ number_format((float)$detailDnTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
