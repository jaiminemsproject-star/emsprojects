@extends('layouts.erp')

@section('title', 'Storage Access')

@section('content')
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h1 class="h4 mb-1">Storage Access</h1>
                <div class="text-muted small">
                    Grant/revoke <code>{{ $storagePermission }}</code> at user level.
                </div>
            </div>

    <form method="GET" class="card card-body mb-3">
        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Name</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm"
                    placeholder="Search name">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Email</label>
                <input type="text" name="email" value="{{ request('email') }}" class="form-control form-control-sm"
                    placeholder="Search email">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Role</label>
                <input type="text" name="role" value="{{ request('role') }}" class="form-control form-control-sm"
                    placeholder="admin">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold">Storage Access</label>
                <select name="has_storage" class="form-select form-select-sm">
                    <option value="">Any</option>
                    <option value="1" @selected(request('has_storage') === '1')>Yes</option>
                    <option value="0" @selected(request('has_storage') === '0')>No</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    Filter
                </button>

                @if(request()->query())
                    <a href="{{ route('access.storage-access.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        Clear
                    </a>
                @endif
            </div>

        </div>
    </form>


        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="fw-semibold">Users</div>
                <div class="text-muted small">Total: {{ $users->total() }}</div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 220px;">User</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th class="text-center">Has Storage Access</th>
                                <th class="text-end" style="min-width: 160px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $u)
                            @php
        $hasAccess = $u->can($storagePermission); // effective permission (role OR direct)
        $direct = method_exists($u, 'getDirectPermissions')
            ? $u->getDirectPermissions()->pluck('name')->contains($storagePermission)
            : false;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $u->name ?? '—' }}</td>
                                <td>{{ $u->email ?? '—' }}</td>
                                <td>
                                    @forelse($u->roles as $r)
                                        <span class="badge bg-light text-dark border">{{ $r->name }}</span>
                                    @empty
                                        <span class="text-muted small">No role</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    @if($hasAccess)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif

                                    @if($direct)
                                        <span class="badge bg-info text-dark">Direct</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('access.storage-access.update', $u) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="grant" value="{{ $hasAccess ? 0 : 1 }}">
                                        <button type="submit"
                                                class="btn btn-sm {{ $hasAccess ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $hasAccess ? 'Revoke' : 'Grant' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
@endsection
