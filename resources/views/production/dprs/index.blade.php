@extends('layouts.erp')

@section('title', 'Production DPRs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="bi bi-clipboard2-check"></i> Production DPRs</h1>
        @can('production.dpr.create')
            <a href="{{ url('/projects/'.$projectId.'/production-dprs/create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New DPR
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Plan</th>
                            <th>Activity</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th>Contractor</th>
                            <th>Worker</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr>
                                <td>{{ $r->dpr_date }}</td>
                                <td>{{ $r->plan_number }}</td>
                                <td>{{ $r->activity_name }} <span class="text-muted small">({{ $r->activity_code }})</span></td>
                                <td>{{ $r->shift ?? '—' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $r->status }}</span></td>
                                <td>{{ $r->contractor_name ?? '—' }}</td>
                                <td>{{ $r->worker_name ?? '—' }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/projects/'.$projectId.'/production-dprs/'.$r->id) }}">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No DPRs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rows->hasPages())
                <div class="mt-2">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
