@extends('layouts.erp')

@section('title', 'Vouchers')

@section('content')
<div class="container-fluid">
    @php
        $isWipToCogs = $isWipToCogs ?? request()->boolean('wip_to_cogs');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">
            Vouchers
            @if($isWipToCogs)
                <span class="badge bg-info text-dark ms-2">WIP → COGS Drafts</span>
            @endif
        </h1>

        <div class="d-flex gap-2">
            @if($isWipToCogs)
                <a href="{{ route('accounting.vouchers.index') }}" class="btn btn-outline-secondary btn-sm">
                    View All
                </a>
            @endif

            @can('accounting.vouchers.create')
                <a href="{{ route('accounting.vouchers.create') }}" class="btn btn-primary btn-sm">Add Voucher</a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>No.</th>
                        <th>Type</th>
                        <th>Project</th>
                        <th>Cost Center</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Posted At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($vouchers))
                        @foreach($vouchers as $voucher)
                            <tr>
                                <td>{{ $voucher->voucher_date?->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('accounting.vouchers.show', $voucher) }}" class="text-decoration-none">
                                        {{ $voucher->voucher_no }}
                                    </a>
                                    @if($voucher->reversal_of_voucher_id)
                                        <span class="badge bg-secondary ms-1">Reversal</span>
                                    @endif
                                    @if($voucher->reversal_voucher_id)
                                        <span class="badge bg-warning text-dark ms-1">Reversed</span>
                                    @endif

                                    {{-- ✅ CN/DN badge + link --}}
                                    @include('accounting.vouchers._voucher_doc_badge', ['voucher' => $voucher, 'docLinks' => $docLinks ?? []])
                                </td>
                                <td>{{ $voucher->voucher_type }}</td>
                                <td>{{ $voucher->project?->code }}</td>
                                <td>{{ $voucher->costCenter?->name }}</td>
                                <td class="text-end">{{ number_format($voucher->amount_base, 2) }}</td>
                                <td>
                                    @php
                                        $status = (string) ($voucher->status ?? '');
                                        $badge = match($status) {
                                            'posted' => 'bg-success',
                                            'draft' => 'bg-secondary',
                                            default => 'bg-light text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ ucfirst($status ?: 'n/a') }}</span>
                                </td>
                                <td class="small text-muted">{{ $voucher->posted_at?->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No vouchers yet.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if($vouchers->hasPages())
            <div class="card-footer">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
