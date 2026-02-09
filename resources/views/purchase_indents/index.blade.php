@extends('layouts.erp')

@section('title', 'Purchase Indents')

@section('content')
    @php
        $rows = $indents->getCollection();
        $draftCount = $rows->where('status', 'draft')->count();
        $approvedCount = $rows->where('status', 'approved')->count();
        $rejectedCount = $rows->where('status', 'rejected')->count();
        $orderedCount = $rows->where('procurement_status', 'ordered')->count();
    @endphp
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0"><i class="bi bi-card-checklist me-1"></i> Purchase Indents</h1>
                <div class="small text-muted">Track request, approval, and procurement progress in one place.</div>
            </div>
            @can('purchase.indent.create')
                <a href="{{ route('purchase-indents.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> New Indent
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-2 mb-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <div class="small text-muted">Draft</div>
                        <div class="h5 mb-0">{{ $draftCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <div class="small text-muted">Approved</div>
                        <div class="h5 mb-0">{{ $approvedCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <div class="small text-muted">Rejected</div>
                        <div class="h5 mb-0">{{ $rejectedCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <div class="small text-muted">Fully Ordered</div>
                        <div class="h5 mb-0">{{ $orderedCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Indent number">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(($statusOptions ?? []) as $k => $v)
                                <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Procurement</label>
                        <select name="procurement_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(($procurementOptions ?? []) as $k => $v)
                                <option value="{{ $k }}" @selected(request('procurement_status') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(($projects ?? collect()) as $p)
                                <option value="{{ $p->id }}" @selected((string) request('project_id') === (string) $p->id)>
                                    {{ $p->code }} - {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" type="submit">Go</button>
                        <a href="{{ route('purchase-indents.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px">#</th>
                                <th>Indent No</th>
                                <th>Project</th>
                                <th>Required By</th>
                                <th>Status</th>
                                <th>Procurement</th>
                                <th>Created At</th>
                                <th style="width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($indents as $indent)
                            <tr>
                                <td>{{ $indent->id }}</td>
                                <td>
                                    <a href="{{ route('purchase-indents.show', $indent) }}" class="fw-semibold text-decoration-none">
                                        {{ $indent->code }}
                                    </a>
                                </td>
                                <td>
                                    @if($indent->project)
                                        {{ $indent->project->code }} - {{ $indent->project->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($indent->required_by_date)?->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $indent->status === 'approved' ? 'success' : ($indent->status === 'rejected' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($indent->status) }}
                                    </span>
                                </td>
                                <td>
                                    @php($p = $indent->procurement_status ?? 'open')
                                    @php($procClass = match($p) {'ordered'=>'success','partially_ordered'=>'warning','rfq_created'=>'info','cancelled'=>'danger','closed'=>'dark',default=>'secondary'})
                                    <span class="badge bg-{{ $procClass }}">{{ ucwords(str_replace('_',' ', $p)) }}</span>
                                </td>
                                <td>{{ $indent->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        @can('purchase.indent.view')
                                            <a href="{{ route('purchase-indents.show', $indent) }}"
                                               class="btn btn-sm btn-outline-secondary">View</a>
                                        @endcan

                                        @can('purchase.indent.update')
                                            @if(!in_array($indent->status, ['approved', 'rejected']))
                                                <a href="{{ route('purchase-indents.edit', $indent) }}"
                                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                            @endif
                                        @endcan

                                        @can('purchase.indent.approve')
                                            @if($indent->status === 'draft')
                                                <form action="{{ route('purchase-indents.approve', $indent) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Approve this indent?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No indents found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $indents->count() }} of {{ $indents->total() }} indents</small>
                {{ $indents->links() }}
            </div>
        </div>
    </div>
@endsection
