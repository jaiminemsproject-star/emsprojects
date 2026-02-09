@extends('layouts.erp')

@section('title', 'Material Types')

@section('content')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Material Types</h1>

            @can('core.material_type.create')
                <a href="{{ route('material-types.create') }}" class="btn btn-primary btn-sm">
                    + Add Type
                </a>
            @endcan
        </div>

        {{-- Filters --}}<div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('material-types.index') }}" class="row g-2 align-items-end">

                    {{-- Code --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">Code</label>
                        <input type="text" name="code" value="{{ request('code') }}" class="form-control form-control-sm"
                            placeholder="Material Code">
                    </div>

                    {{-- Name --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">Name</label>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm"
                            placeholder="Material Name">
                    </div>

                    {{-- Description --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">Description</label>
                        <input type="text" name="description" value="{{ request('description') }}"
                            class="form-control form-control-sm" placeholder="Description">
                    </div>

                    {{-- Status --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="col-6 col-md-1 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-funnel"></i>Filter
                        </button>
                    </div>

                    @if(request()->query())
                        <div class="col-12 col-md-1">
                            <a href="{{ route('material-types.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                                Reset
                            </a>
                        </div>
                    @endif

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
                        <th style="width: 10%">
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
                        <th style="width: 10%">
                            <a href="{{ $sortLink('sort_order') }}" class="text-decoration-none text-dark">
                                Sort
                                @if($sort === 'sort_order')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 10%">
                            <a href="{{ $sortLink('is_active') }}" class="text-decoration-none text-dark">
                                Active
                                @if($sort === 'is_active')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 15%">
                            <a href="{{ $sortLink('categories_count') }}" class="text-decoration-none text-dark">
                                Categories
                                @if($sort === 'categories_count')
                                    <i class="bi {{ $dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 15%" class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($types as $type)
                        <tr>
                            <td>{{ $type->code }}</td>
                            <td>{{ $type->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($type->description, 40) }}</td>
                            <td>{{ $type->sort_order }}</td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $type->categories_count }}</td>
                            <td class="text-end">
                                @can('core.material_type.update')
                                    <a href="{{ route('material-types.edit', $type) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                @endcan

                                @can('core.material_type.delete')
                                    <form action="{{ route('material-types.destroy', $type) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this material type?');">
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
                            <td colspan="7" class="text-center text-muted py-3">
                                No material types found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($types->hasPages())
                <div class="card-footer">
                    {{ $types->links() }}
                </div>
            @endif
        </div>
@endsection
