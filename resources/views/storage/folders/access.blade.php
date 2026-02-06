@extends('layouts.erp')

@section('title', 'Storage - Manage Access')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Manage Access</h4>
        <div class="text-muted">Folder: {{ $folder->name }} (ID: {{ $folder->id }})</div>
    </div>
    <a href="{{ route('storage.folders.show', $folder) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

@include('partials.alerts')

<div class="card mb-3">
    <div class="card-header"><strong>Grant / Update User Access</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('storage.folders.access.store', $folder) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- Select user --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-3">
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_view" value="1">
                        <span class="form-check-label">View</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_upload" value="1">
                        <span class="form-check-label">Upload</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_download" value="1">
                        <span class="form-check-label">Download</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_edit" value="1">
                        <span class="form-check-label">Edit</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_delete" value="1">
                        <span class="form-check-label">Delete</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_manage_access" value="1">
                        <span class="form-check-label">Manage Access</span>
                    </label>

                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="apply_to_subfolders" value="1" checked>
                        <span class="form-check-label">Apply to subfolders</span>
                    </label>
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-save me-1"></i> Save Access
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Current Access List</strong></div>
    <div class="card-body">
        @if($accesses->isEmpty())
            <div class="text-muted">No users assigned yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Upload</th>
                            <th class="text-center">Download</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                            <th class="text-center">Manage</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accesses as $a)
                            <tr>
                                <td>{{ $a->user?->name ?? ('User#'.$a->user_id) }}</td>
                                <td class="text-center">{{ $a->can_view ? '✓' : '—' }}</td>
                                <td class="text-center">{{ $a->can_upload ? '✓' : '—' }}</td>
                                <td class="text-center">{{ $a->can_download ? '✓' : '—' }}</td>
                                <td class="text-center">{{ $a->can_edit ? '✓' : '—' }}</td>
                                <td class="text-center">{{ $a->can_delete ? '✓' : '—' }}</td>
                                <td class="text-center">{{ $a->can_manage_access ? '✓' : '—' }}</td>
                                <td class="text-end">
                                    <form method="POST"
                                          action="{{ route('storage.folders.access.destroy', [$folder, $a->user_id]) }}"
                                          onsubmit="return confirm('Remove access for this user (also removes from subfolders)?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
