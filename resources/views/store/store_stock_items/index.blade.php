@extends('layouts.erp')

@section('title', 'Store Stock Items')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Store Stock Items</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('store-stock-items.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">-- All Projects --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}"
                                {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->code }} - {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Item</label>
                    <select name="item_id" class="form-select form-select-sm">
                        <option value="">-- All Items --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}"
                                {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="material_category" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($materialCategories as $value => $label)
                            <option value="{{ $value }}"
                                {{ request('material_category') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}"
                                {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Material Type</label>
                    <select name="is_client_material" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        <option value="0" {{ request('is_client_material') === '0' ? 'selected' : '' }}>Own</option>
                        <option value="1" {{ request('is_client_material') === '1' ? 'selected' : '' }}>Client</option>
                    </select>
                </div>

                <div class="col-md-2 mt-2">
                    <label class="form-label">Grade</label>
                    <input type="text" name="grade"
                           value="{{ request('grade') }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-12 mt-2 d-flex justify-content-end">
                    <a href="{{ route('store-stock-items.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                        Reset
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Project</th>
                        <th>Grade</th>
                        <th>T (mm)</th>
                        <th>W (mm)</th>
                        <th>L (mm)</th>
                        <th>Section</th>
                        <th>Qty Avl</th>
                        <th>Wt Avl (kg)</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($stockItems as $row)
                        <tr>
                            <td>
                                {{ $row->item->name ?? 'Item #'.$row->item_id }}
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $row->material_category)) }}</td>
                            <td>
                                @if($row->project)
                                    {{ $row->project->code }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $row->grade ?? '-' }}</td>
                            <td>{{ $row->thickness_mm ?? '-' }}</td>
                            <td>{{ $row->width_mm ?? '-' }}</td>
                            <td>{{ $row->length_mm ?? '-' }}</td>
                            <td>{{ $row->section_profile ?? '-' }}</td>
                            <td>{{ $row->qty_pcs_available }}</td>
                            <td>{{ $row->weight_kg_available ?? '-' }}</td>
                            <td>
                                {{ $row->is_client_material ? 'Client' : 'Own' }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ strtoupper($row->status) }}</span>
                            </td>
                            <td>{{ $row->location ?? '-' }}</td>
                            <td class="text-end">
                                @can('store.stock_item.update')
                                    <a href="{{ route('store-stock-items.edit', $row) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted py-3">
                                No stock items found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($stockItems->hasPages())
            <div class="card-footer pb-0">
                {{ $stockItems->links() }}
            </div>
        @endif
    </div>
@endsection
