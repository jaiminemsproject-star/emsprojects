@extends('layouts.app')

@section('title', 'Project Cost Sheet - ' . $project->name)

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.reports.project-cost-sheet') }}">Project Cost Sheet</a></li>
                    <li class="breadcrumb-item active">{{ $project->code }}</li>
                </ol>
            </nav>
            <h1 class="h4 mb-0">{{ $project->name }}</h1>
            <small class="text-muted">Cost Sheet as of {{ $asOfDate->format('d-M-Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accounting.reports.project-cost-sheet.export', ['project' => $project, 'date_from' => $dateFrom?->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d')]) }}" 
               class="btn btn-success btn-sm">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="{{ route('accounting.reports.project-cost-sheet') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" 
                           value="{{ $dateFrom?->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" 
                           value="{{ $dateTo->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cost Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Material</h6>
                    <h5 class="text-primary mb-0">₹ {{ number_format($costSummary['material'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Consumables</h6>
                    <h5 class="text-info mb-0">₹ {{ number_format($costSummary['consumables'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Subcontractor</h6>
                    <h5 class="text-warning mb-0">₹ {{ number_format($costSummary['subcontractor'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Other Direct</h6>
                    <h5 class="text-secondary mb-0">₹ {{ number_format($costSummary['other_direct'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="mb-1">Total Project Cost</h6>
                    <h3 class="mb-0">₹ {{ number_format($totalCost, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Cost Breakdown Chart (Monthly) --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Monthly Cost Trend</h5>
                </div>
                <div class="card-body">
                    @if(count($monthlyBreakdown) > 0)
                        <canvas id="monthlyChart" height="200"></canvas>
                    @else
                        <p class="text-muted text-center py-4">No monthly data available.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cost Distribution Pie Chart --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cost Distribution</h5>
                </div>
                <div class="card-body">
                    @if($totalCost > 0)
                        <canvas id="distributionChart" height="200"></canvas>
                    @else
                        <p class="text-muted text-center py-4">No cost data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Transactions --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Cost Transaction Details</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Voucher No</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Account</th>
                            <th class="text-end">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($costDetails as $detail)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($detail['voucher_date'])->format('d-M-Y') }}</td>
                                <td>
                                    <a href="{{ route('accounting.vouchers.show', $detail['voucher_id']) }}">
                                        {{ $detail['voucher_no'] }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $detail['voucher_type'] === 'purchase' ? 'primary' : ($detail['voucher_type'] === 'store_issue' ? 'info' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $detail['voucher_type'])) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($detail['description'], 50) }}</td>
                                <td>
                                    <small>{{ $detail['account_name'] }}</small>
                                    <br>
                                    <small class="text-muted">{{ $detail['account_code'] }}</small>
                                </td>
                                <td class="text-end">{{ number_format($detail['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No transactions found for this project.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($costDetails) > 0)
                    <tfoot class="table-secondary">
                        <tr class="fw-bold">
                            <td colspan="5">TOTAL</td>
                            <td class="text-end">₹ {{ number_format(collect($costDetails)->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart
    @if(count($monthlyBreakdown) > 0)
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyBreakdown, 'month')) !!},
            datasets: [{
                label: 'Cost (₹)',
                data: {!! json_encode(array_column($monthlyBreakdown, 'amount')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹ ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    @endif

    // Distribution Pie Chart
    @if($totalCost > 0)
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Material', 'Consumables', 'Subcontractor', 'Other Direct'],
            datasets: [{
                data: [
                    {{ $costSummary['material'] ?? 0 }},
                    {{ $costSummary['consumables'] ?? 0 }},
                    {{ $costSummary['subcontractor'] ?? 0 }},
                    {{ $costSummary['other_direct'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.8)',
                    'rgba(13, 202, 240, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
});
</script>
@endpush
@endsection
