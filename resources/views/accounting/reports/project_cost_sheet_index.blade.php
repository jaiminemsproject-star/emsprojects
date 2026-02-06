@extends('layouts.app')

@section('title', 'Project Cost Sheet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0">Project Cost Sheet</h1>
            <small class="text-muted">Project-wise cost analysis as of {{ $asOfDate->format('d-M-Y') }}</small>
        </div>
        <div>
            <form method="GET" class="d-flex gap-2">
                <input type="date" name="as_of_date" class="form-control form-control-sm" 
                       value="{{ $asOfDate->format('Y-m-d') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Material Cost</h6>
                    <h4 class="mb-0">₹ {{ number_format($grandTotals['material'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Consumables</h6>
                    <h4 class="mb-0">₹ {{ number_format($grandTotals['consumables'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Subcontractor Cost</h6>
                    <h4 class="mb-0">₹ {{ number_format($grandTotals['subcontractor'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Project Cost</h6>
                    <h4 class="mb-0">₹ {{ number_format($grandTotals['total'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Project-wise Cost Summary</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Project</th>
                            <th class="text-end">Material</th>
                            <th class="text-end">Consumables</th>
                            <th class="text-end">Subcontractor</th>
                            <th class="text-end">Other Direct</th>
                            <th class="text-end">Total Cost</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projectCosts as $data)
                            <tr>
                                <td>
                                    <a href="{{ route('accounting.reports.project-cost-sheet.show', $data['project']) }}">
                                        <strong>{{ $data['project']->name }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $data['project']->code }}</small>
                                </td>
                                <td class="text-end">₹ {{ number_format($data['costs']['material'] ?? 0, 2) }}</td>
                                <td class="text-end">₹ {{ number_format($data['costs']['consumables'] ?? 0, 2) }}</td>
                                <td class="text-end">₹ {{ number_format($data['costs']['subcontractor'] ?? 0, 2) }}</td>
                                <td class="text-end">₹ {{ number_format($data['costs']['other_direct'] ?? 0, 2) }}</td>
                                <td class="text-end">
                                    <strong>₹ {{ number_format($data['total_cost'], 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('accounting.reports.project-cost-sheet.show', $data['project']) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('accounting.reports.project-cost-sheet.export', $data['project']) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download"></i> Export
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No project costs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($projectCosts) > 0)
                    <tfoot class="table-secondary">
                        <tr class="fw-bold">
                            <td>GRAND TOTAL</td>
                            <td class="text-end">₹ {{ number_format($grandTotals['material'], 2) }}</td>
                            <td class="text-end">₹ {{ number_format($grandTotals['consumables'], 2) }}</td>
                            <td class="text-end">₹ {{ number_format($grandTotals['subcontractor'], 2) }}</td>
                            <td class="text-end">₹ {{ number_format($grandTotals['other_direct'], 2) }}</td>
                            <td class="text-end">₹ {{ number_format($grandTotals['total'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
