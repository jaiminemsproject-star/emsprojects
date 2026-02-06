@extends('layouts.erp')

@section('title', 'Account Types')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Account Types</h1>
            <div class="small text-muted">Company ID: {{ $companyId }}</div>
        </div>
        @can('accounting.accounts.update')
            <a href="{{ route('accounting.account-types.create') }}" class="btn btn-primary btn-sm">
                Add Type
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text"
                           name="q"
                           value="{{ $q }}"
                           class="form-control form-control-sm"
                           placeholder="Search by code or name">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Filter</button>
                    <a href="{{ route('accounting.account-types.index') }}" class="btn btn-link btn-sm">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 160px;">Code</th>
                            <th>Name</th>
                            <th class="text-center" style="width: 110px;">Active</th>
                            <th class="text-center" style="width: 110px;">System</th>
                            <th class="text-end" style="width: 90px;">Sort</th>
                            <th class="text-end" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $t)
                            <tr>
                                <td class="fw-semibold">{{ $t->code }}</td>
                                <td>{{ $t->name }}</td>
                                <td class="text-center">
                                    @if($t->is_active)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($t->is_system)
                                        <span class="badge bg-secondary">Yes</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $t->sort_order }}</td>
                                <td class="text-end">
                                    @can('accounting.accounts.update')
                                        <a href="{{ route('accounting.account-types.edit', $t->id) }}" class="btn btn-outline-primary btn-sm">
                                            Edit
                                        </a>

                                        @if(! $t->is_system)
                                            <form action="{{ route('accounting.account-types.destroy', $t->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this type? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No account types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($types, 'links'))
                <div class="mt-3">
                    {{ $types->links() }}
                </div>
            @endif

            <div class="small text-muted mt-2">
                These types appear in the Account create/edit screen under <strong>Type</strong>.
                <br>
                System types are required by the accounting module and cannot be deleted.
            </div>
        </div>
    </div>
</div>
@endsection
