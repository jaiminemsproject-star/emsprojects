@extends('layouts.erp')

@section('title', 'Store Stock Register')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Store Stock Register</h1>
    </div>

    <form method="GET" class="card mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Item</label>
                <select name="item_id" class="form-select form-select-sm">
                    <option value="">All Items</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ $selectedItemId == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Project</label>
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>
                            {{ $project->code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="form-control form-control-sm">
            </div>


            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="include_raw" name="include_raw" {{ !empty($includeRaw) ? 'checked' : '' }}>
                    <label class="form-check-label" for="include_raw">
                        Include RAW (Plates/Sections)
                    </label>
                </div>
                <div class="form-text">
                    When enabled, register also shows Production Consume & Remnant movements.
                </div>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary flex-grow-1">Filter</button>
                <a href="{{ route('store-stock-register.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Ref No</th>
                        <th>Item</th>
                        <th>Project</th>
                        <th class="text-end">In (pcs)</th>
                        <th class="text-end">Out (pcs)</th>
                        <th class="text-end">In (kg)</th>
                        <th class="text-end">Out (kg)</th>
                        <th class="text-end">Balance (kg)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($movements as $row)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($row->txn_date)->format('d-m-Y') }}</td>
                            <td>{{ ucfirst($row->txn_type) }}</td>
                            <td>{{ $row->reference_number }}</td>
                            <td>{{ optional($items->firstWhere('id', $row->item_id))->name }}</td>
                            <td>{{ optional($projects->firstWhere('id', $row->project_id))->code }}</td>
                            <td class="text-end">{{ number_format($row->qty_in_pcs ?? 0) }}</td>
                            <td class="text-end">{{ number_format($row->qty_out_pcs ?? 0) }}</td>
                            <td class="text-end">{{ number_format($row->weight_in_kg ?? 0, 3) }}</td>
                            <td class="text-end">{{ number_format($row->weight_out_kg ?? 0, 3) }}</td>
                            <td class="text-end">
                                @if(property_exists($row, 'balance_kg'))
                                    {{ number_format($row->balance_kg, 3) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No movements found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

