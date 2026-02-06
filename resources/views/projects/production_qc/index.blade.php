@extends('layouts.erp')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0"><i class="bi bi-shield-check"></i> QC Pending</h2>
            <div class="text-muted small">Project: <span class="fw-semibold">{{ $project->code }}</span> — {{ $project->name }}</div>
        </div>
        <a href="{{ route('projects.production-dprs.index', $project) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plan</th>
                        <th>Activity</th>
                        <th>Item</th>
                        <th style="width:380px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $qc)
                        <tr>
                            <td class="fw-semibold">#{{ $qc->id }}</td>
                            <td>{{ $qc->plan?->plan_number }}</td>
                            <td>{{ $qc->activity?->name }}</td>
                            <td>
                                {{ $qc->planItemActivity?->planItem?->item_code ?? '—' }}
                                <div class="text-muted small">{{ $qc->planItemActivity?->planItem?->assembly_mark ?? '' }}</div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('projects.production-qc.update', [$project, $qc]) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="result" class="form-select form-select-sm" required>
                                        <option value="">—</option>
                                        <option value="passed">Pass</option>
                                        <option value="failed">Fail</option>
                                    </select>
                                    <input name="remarks" class="form-control form-control-sm" placeholder="remarks (optional)">
                                    <button class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No QC pending items.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $pending->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
