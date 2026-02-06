@extends('layouts.erp')

@section('title', 'Storage - Folder')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">{{ $folder->name }}</h4>
        <div class="text-muted">Folder ID: {{ $folder->id }}</div>
    </div>

    <div class="d-flex gap-2">
        @can('manageAccess', $folder)
            <a href="{{ route('storage.folders.access.index', $folder) }}" class="btn btn-outline-secondary">
                <i class="bi bi-shield-lock me-1"></i> Manage Access
            </a>
        @endcan
    </div>
</div>

@include('partials.alerts')

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Subfolders</strong>
            </div>
            <div class="card-body">
                @can('createSubfolder', $folder)
                <form method="POST" action="{{ route('storage.folders.store') }}" class="row g-2 mb-3">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $folder->id }}">
                    <div class="col-8">
                        <input type="text" name="name" class="form-control" placeholder="New folder name" required>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-plus-lg me-1"></i> Create
                        </button>
                    </div>
                </form>
                @endcan

                @if($subfolders->isEmpty())
                    <div class="text-muted">No subfolders.</div>
                @else
                    <div class="list-group">
                        @foreach($subfolders as $sf)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <a class="text-decoration-none" href="{{ route('storage.folders.show', $sf) }}">
                                    <i class="bi bi-folder me-1"></i> {{ $sf->name }}
                                </a>
                                @can('delete', $sf)
                                <form method="POST"
                                      action="{{ route('storage.folders.destroy', $sf) }}"
                                      onsubmit="return confirm('Delete this folder and everything inside it?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><strong>Files</strong></div>
            <div class="card-body">

                @can('upload', $folder)
                <form method="POST" action="{{ route('storage.files.store', $folder) }}" enctype="multipart/form-data" class="mb-3">
                    @csrf
                    <label class="form-label">Upload files</label>
                    <input type="file" name="files[]" class="form-control" multiple required>
                    <button class="btn btn-primary mt-2" type="submit">
                        <i class="bi bi-upload me-1"></i> Upload
                    </button>
                </form>
                @endcan

                @if($files->isEmpty())
                    <div class="text-muted">No files uploaded.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($files as $f)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="me-2">
                                    <div class="fw-semibold">{{ $f->original_name }}</div>
                                    <div class="text-muted small">{{ number_format((int)($f->size ?? 0)/1024, 1) }} KB</div>

                                    @can('update', $f)
                                    <form method="POST" action="{{ route('storage.files.update', $f) }}" class="mt-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="original_name" class="form-control" value="{{ $f->original_name }}" required>
                                            <button class="btn btn-outline-primary" type="submit">Rename</button>
                                        </div>
                                    </form>
                                    @endcan
                                </div>
                                <div class="d-flex gap-1">
                                    @can('download', $f)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('storage.files.download', $f) }}" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @endcan

                                    @can('delete', $f)
                                    <form method="POST" action="{{ route('storage.files.destroy', $f) }}"
                                          onsubmit="return confirm('Delete this file?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
