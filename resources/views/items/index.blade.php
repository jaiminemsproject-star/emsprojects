@extends('layouts.erp')

@section('title', 'Items')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Items</h1>
        <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">+ Add Item</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">

            <!-- Code -->
            <div class="col-md-2">
                <label class="form-label">Code</label>
                <input type="text" name="code" value="{{ request('code') }}" class="form-control form-control-sm"
                    placeholder="Item Code">
            </div>

            <!-- Name -->
            <div class="col-md-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm"
                    placeholder="Item Name">
            </div>

            <!-- Short Name -->
            <div class="col-md-2">
                <label class="form-label">Short Name</label>
                <input type="text" name="short_name" value="{{ request('short_name') }}" class="form-control form-control-sm"
                    placeholder="Short Name">
            </div>

            <!-- Type -->
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="material_type_id" class="form-select form-select-sm">
                    <option value="">-- All Types --</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" @selected(request('material_type_id') == $type->id)>
                            {{ $type->code }} - {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select name="material_category_id" class="form-select form-select-sm">
                    <option value="">-- All Categories --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('material_category_id') == $cat->id)>
                            {{ $cat->code }} - {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Subcategory -->
            <div class="col-md-3">
                <label class="form-label">Subcategory</label>
                <select name="material_subcategory_id" class="form-select form-select-sm">
                    <option value="">-- All Subcategories --</option>
                    @foreach($subcategories as $sub)
                        <option value="{{ $sub->id }}" @selected(request('material_subcategory_id') == $sub->id)>
                            {{ $sub->code }} - {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 mt-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search me-1"></i> Filter
                </button>

                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">
                    Reset
                </a>
            </div>

        </form>

        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th style="width: 8%">Code</th>
                    <th>Name</th>
                    <th style="width: 10%">Type</th>
                    <th style="width: 12%">Category</th>
                    <th style="width: 14%">Subcategory</th>
                    <th style="width: 8%">UoM</th>
                    <th style="width: 15%">Expense Account</th>
                    <th style="width: 6%">Active</th>
                    <th style="width: 10%" class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->type?->code }}</td>
                        <td>{{ $item->category?->code }}</td>
                        <td>{{ $item->subcategory?->code }}</td>
                        <td>{{ $item->uom?->code }}</td>
                        <td>{{ $item->expenseAccount?->name ?? '—' }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">
                            No items found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="card-footer">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
