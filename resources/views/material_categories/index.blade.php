@extends('layouts.erp')

@section('title', 'Material Categories')

@section('content')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Material Categories</h1>

            @can('core.material_category.create')
                <a href="{{ route('material-categories.create') }}" class="btn btn-primary btn-sm">
                    + Add Category
                </a>
            @endcan
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
        <form method="GET" action="{{ route('material-categories.index') }}" class="row g-2 align-items-end">

            <!-- Code -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Code</label>
                <input type="text" name="code" value="{{ request('code') }}" class="form-control form-control-sm"
                    placeholder="Category Code">
            </div>

            <!-- Name -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Name</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm"
                    placeholder="Category Name">
            </div>

            <!-- Description -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Description</label>
                <input type="text" name="description" value="{{ request('description') }}" class="form-control form-control-sm"
                    placeholder="Description">
            </div>

            <!-- Type -->
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">Type</label>
                <select name="material_type_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ request('material_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->code }} — {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="col-6 col-md-2">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                <a href="{{ route('material-categories.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset
                </a>
            </div>

        </form>

            </div>
        </div>

        @php
    $sort = (string) request('sort', '');
    $dir = strtolower((string) request('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

    $sortLink = function (string $col) use ($sort, $dir) {
        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir]);
    };
        @endphp

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 12%">
                            <a href="{{ $sortLink('material_type_id') }}" class="text-decoration-none text-dark">
                                Type
                                @if($sort === 'material_type_id')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 12%">
                            <a href="{{ $sortLink('code') }}" class="text-decoration-none text-dark">
                                Code
                                @if($sort === 'code')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortLink('name') }}" class="text-decoration-none text-dark">
                                Name
                                @if($sort === 'name')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 25%">Description</th>
                        <th style="width: 8%">
                            <a href="{{ $sortLink('sort_order') }}" class="text-decoration-none text-dark">
                                Sort
                                @if($sort === 'sort_order')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 8%">
                            <a href="{{ $sortLink('is_active') }}" class="text-decoration-none text-dark">
                                Active
                                @if($sort === 'is_active')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 12%">
                            <a href="{{ $sortLink('subcategories_count') }}" class="text-decoration-none text-dark">
                                Subcategories
                                @if($sort === 'subcategories_count')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 15%" class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->type?->code }}</td>
                            <td>{{ $category->code }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($category->description, 40) }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $category->subcategories_count }}</td>
                            <td class="text-end">
                                @can('core.material_category.update')
                                    <a href="{{ route('material-categories.edit', $category) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                @endcan

                                @can('core.material_category.delete')
                                    <form action="{{ route('material-categories.destroy', $category) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this material category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger ms-1">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                No material categories found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="card-footer">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
@endsection
