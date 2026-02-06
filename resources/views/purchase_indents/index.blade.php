@extends('layouts.erp')

@section('title', 'Purchase Indents')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Purchase Indents</h1>
        @can('purchase.indent.create')
            <a href="{{ route('purchase-indents.create') }}" class="btn btn-sm btn-primary">New Indent</a>
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
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($indents as $indent)
                        <tr>
                            <td>{{ $indent->id }}</td>
                            <td>
                                <a href="{{ route('purchase-indents.show', $indent) }}">
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
                                <div class="d-flex gap-1">
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
                                                  method="POST" style="display: inline"
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
        <div class="card-footer py-2">
            {{ $indents->links() }}
        </div>
    </div>
@endsection