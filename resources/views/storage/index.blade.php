@extends('layouts.erp')

@section('title', 'Storage')


@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Storage</h4>
        <div class="text-muted">Folders and files (user-level access)</div>
    </div>
</div>
@can('createRoot', \App\Models\Storage\StorageFolder::class)
<div class="card mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('storage.folders.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-8">
                <label class="form-label">Create Root Folder</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Company Documents" required>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="bi bi-plus-lg me-1"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
@include('partials.alerts')

<div class="card">
    <div class="card-body">
        @if($folders->isEmpty())
            <div class="text-muted">No folders assigned to you yet.</div>
        @else
            <div class="list-group">
                @foreach($folders as $f)
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                       href="{{ route('storage.folders.show', $f) }}">
                        <div>
                            <div class="fw-semibold">{{ $f->name }}</div>
                            <div class="text-muted small">Folder ID: {{ $f->id }}</div>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
