@extends('layouts.erp')

@section('title', 'UOM Master')

@section('content')

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h4 mb-0">Unit of Measurement (UOM)</h1>
                    <div class="text-muted small">Manage units used across the system</div>
                </div>

                @can('core.uom.create')
                    <a href="{{ route('uoms.create') }}" class="btn btn-sm btn-primary">
                        + Add UOM 

                    </a>
                @endcan
            </div>

            {{-- Filters --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('uoms.index') }}">
                        <div class="row g-3 align-items-end">

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Code</label>
                                <input type="text" name="code" value="{{ request('code') }}" class="form-control form-control-sm"
                                    placeholder="UOM Code">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Name</label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm"
                                    placeholder="UOM Name">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Category</label>
                                <select name="category" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="length" @selected(request('category') === 'length')>Length</option>
                                    <option value="weight" @selected(request('category') === 'weight')>Weight</option>
                                    <option value="volume" @selected(request('category') === 'volume')>Volume</option>
                                    <option value="area" @selected(request('category') === 'area')>Area</option>
                                    <option value="count" @selected(request('category') === 'count')>Count / Unit</option>
                                    <option value="time" @selected(request('category') === 'time')>Time</option>
                                    <option value="temp" @selected(request('category') === 'temp')>Temperature</option>
                                    <option value="other" @selected(request('category') === 'other')>Other</option>
                                </select>
                            </div>


                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="active" @selected(request('status') === 'active')>Active</option>
                                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-flex gap-2">
                                <button class="btn btn-sm btn-primary w-100">
                                    Filter
                                </button>
                            </div>

                            @if(request()->query())
                                <div class="col-md-1">
                                    <a href="{{ route('uoms.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                                        Reset
                                    </a>
                                </div>
                            @endif

                        </div>
                    </form>
                </div>
            </div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

            @php
    $sort = request('sort');
    $dir = request('dir', 'asc');
    $sortLink = fn($col) =>
        request()->fullUrlWithQuery([
            'sort' => $col,
            'dir' => ($sort === $col && $dir === 'asc') ? 'desc' : 'asc',
        ]);
            @endphp

            {{-- Table --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <a href="{{ $sortLink('code') }}" class="text-dark text-decoration-none">
                                            Code
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ $sortLink('name') }}" class="text-dark text-decoration-none">
                                            Name
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ $sortLink('category') }}" class="text-dark text-decoration-none">
                                            Category
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ $sortLink('decimal_places') }}" class="text-dark text-decoration-none">
                                            Decimals
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ $sortLink('is_active') }}" class="text-dark text-decoration-none">
                                            Status
                                        </a>
                                    </th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($uoms as $uom)
                                    <tr>
                                        <td>{{ $uom->code }}</td>
                                        <td>{{ $uom->name }}</td>
                                        <td>{{ $uom->category ?? '—' }}</td>
                                        <td>{{ $uom->decimal_places }}</td>
                                        <td>
                                            <span class="badge {{ $uom->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                {{ $uom->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @can('core.uom.update')
                                                <a href="{{ route('uoms.edit', $uom) }}" class="btn btn-sm btn-outline-primary">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('core.uom.delete')
                                                <form action="{{ route('uoms.destroy', $uom) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this UOM?');">
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
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No UOMs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($uoms->hasPages())
                    <div class="card-footer d-flex justify-content-end">
                        {{ $uoms->links() }}
                    </div>
                @endif
            </div>

@endsection