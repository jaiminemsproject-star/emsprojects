@extends('layouts.erp')

@section('title', 'Supplier Ageing Detail')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">Supplier Ageing - Bills</h1>

    <div class="mb-2 small">
        <a href="{{ route('accounting.reports.supplier-ageing', ['supplier_id' => $party->id, 'as_of_date' => optional($asOfDate)->toDateString()]) }}">
            &laquo; Back to Supplier Ageing
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold small">
                    {{ $party->code }} - {{ $party->name }}
                </div>
                <div class="small text-muted">
                    Ledger: {{ $account->code }} - {{ $account->name }}
                </div>
            </div>
            <div class="small text-muted">
                Ageing as on {{ optional($asOfDate)->toDateString() }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 14%;">Bill No</th>
                            <th style="width: 12%;">Bill Date</th>
                            <th style="width: 12%;">Due Date</th>
                            <th style="width: 10%;" class="text-end">Bill Amount</th>
                            <th style="width: 10%;" class="text-end">Allocated</th>
                            <th style="width: 10%;" class="text-end">Outstanding</th>
                            <th style="width: 8%;" class="text-end">Days</th>
                            <th style="width: 24%;">Bucket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $bill = $row['bill'];
                                $label = $bucketLabels[$row['bucket']] ?? $row['bucket'];
                            @endphp
                            <tr>
                                <td class="small">{{ $row['bill_number'] }}</td>
                                <td class="small">{{ optional($row['bill_date'])->toDateString() }}</td>
                                <td class="small">{{ optional($row['due_date'])->toDateString() }}</td>
                                <td class="small text-end">{{ number_format($row['bill_amount'], 2) }}</td>
                                <td class="small text-end">{{ number_format($row['allocated'], 2) }}</td>
                                <td class="small text-end fw-semibold">{{ number_format($row['outstanding'], 2) }}</td>
                                <td class="small text-end">{{ $row['days'] }}</td>
                                <td class="small">{{ $label }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center small text-muted py-2">
                                    No outstanding bills for this supplier / ledger.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rows))
                        <tfoot>
                            <tr class="table-light fw-semibold">
                                <td colspan="3" class="small text-end">Total</td>
                                <td class="small text-end">{{ number_format($grandTotals['bill_amount'], 2) }}</td>
                                <td class="small text-end">{{ number_format($grandTotals['allocated'], 2) }}</td>
                                <td class="small text-end">{{ number_format($grandTotals['outstanding'], 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
