@extends('layouts.erp')

@section('title', 'TDS Certificates')

@section('content')
@php
    $doneLabel = ($direction === 'payable') ? 'Issued' : 'Received';
@endphp

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">TDS Certificates</h4>
            <div class="text-muted" style="font-size: 0.9rem;">
                Total TDS (filtered): <strong>{{ number_format((float) $totalTds, 2) }}</strong>
            </div>
        </div>

        @if($direction === 'payable')
            <form method="POST" action="{{ route('accounting.reports.tds-certificates.sync-payable') }}"
                  onsubmit="return confirm('Sync payable-side certificate rows for already posted Purchase Bills and Subcontractor RA Bills?');">
                @csrf
                <input type="hidden" name="company_id" value="{{ $companyId }}">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-arrow-repeat"></i> Sync Payable Bills
                </button>
            </form>
        @endif
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('accounting.reports.tds-certificates') }}" class="row g-3">
                <input type="hidden" name="company_id" value="{{ $companyId }}">

                <div class="col-md-2">
                    <label class="form-label">Direction</label>
                    <select name="direction" class="form-select">
                        <option value="receivable" {{ $direction === 'receivable' ? 'selected' : '' }}>Receivable (Client)</option>
                        <option value="payable" {{ $direction === 'payable' ? 'selected' : '' }}>Payable (Supplier/Subcontractor)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="received" {{ $status === 'received' ? 'selected' : '' }}>{{ $doneLabel }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Party</label>
                    <select name="party_account_id" class="form-select">
                        <option value="">-- All Parties --</option>
                        @foreach($parties as $p)
                            <option value="{{ $p->id }}" {{ (string) $partyId === (string) $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('accounting.reports.tds-certificates') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Voucher</th>
                            <th>Party</th>
                            <th>Section</th>
                            <th class="text-end">Rate %</th>
                            <th class="text-end">TDS Amount</th>
                            <th>Certificate No</th>
                            <th>Certificate Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>
                                    {{ optional($row->voucher)->voucher_date ? optional($row->voucher)->voucher_date->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    @if($row->voucher)
                                        <a href="{{ route('accounting.vouchers.show', $row->voucher->id) }}">{{ $row->voucher->voucher_no }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($row->partyAccount)->name ?? '-' }}</td>
                                <td>{{ $row->tds_section ?? '-' }}</td>
                                <td class="text-end">{{ $row->tds_rate ? number_format((float) $row->tds_rate, 4) : '-' }}</td>
                                <td class="text-end">{{ number_format((float) $row->tds_amount, 2) }}</td>
                                <td>{{ $row->certificate_no ?: '-' }}</td>
                                <td>
                                    {{ $row->certificate_date ? $row->certificate_date->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    @if($row->certificate_no)
                                        <span class="badge bg-success">{{ $doneLabel }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('accounting.reports.tds-certificates.edit', $row->id) }}">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
