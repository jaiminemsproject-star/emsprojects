@extends('layouts.erp')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">BOM {{ $bom->bom_number }}</h4>
        <small class="text-muted">
            Project: {{ $project->code }} - {{ $project->name }}
        </small>
    </div>
    <div class="text-end">
        <a href="{{ route('projects.boms.index', $project) }}" class="btn btn-outline-secondary">
            Back to BOM List
        </a>

        <a href="{{ route('projects.boms.export', [$project, $bom]) }}"
           class="btn btn-outline-success">
            Export CSV
        </a>
		<a href="{{ route('projects.boms.requirements', [$project, $bom]) }}"
  		 class="btn btn-outline-dark">
  		  Requirements
		</a>
		<a href="{{ route('projects.boms.material-planning.index', [$project, $bom]) }}"
 		  class="btn btn-outline-primary">
 		   Material Planning
		</a>
      
		<a href="{{ route('projects.boms.section-plans.index', [$project, $bom]) }}"
 		  class="btn btn-outline-primary">
 		   Section Planning
		</a>
      
    	  <a href="{{ route('projects.boms.purchase-plates.index', [$project, $bom]) }}"
  		 class="btn btn-outline-primary">
   		 Plate Purchase List
		</a>
        @can('project.bom.create')
            <form action="{{ route('projects.boms.clone-version', [$project, $bom]) }}"
                  method="POST"
                  class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    Clone as New Version
                </button>
            </form>

            <a href="{{ route('projects.boms.copy-form', [$project, $bom]) }}"
               class="btn btn-outline-info">
                Copy to Another Project
            </a>
        @endcan
		@can('project.bom_template.create')
    		<form action="{{ route('projects.boms.save-template', [$project, $bom]) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Save this BOM as a template in the library?');">
        @csrf
        <button type="submit" class="btn btn-outline-warning btn-sm">
            Save as Template
        </button>
   		 </form>
		@endcan
        @can('project.bom.update')
            @if($bom->isDraft())
                <a href="{{ route('projects.boms.edit', [$project, $bom]) }}"
                   class="btn btn-outline-primary">
                    Edit BOM Header
                </a>
            @endif
        @endcan

        @can('project.bom.finalize')
            @if($bom->isDraft())
                <form action="{{ route('projects.boms.finalize', [$project, $bom]) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Finalize this BOM? It will become read-only.');">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Finalize BOM
                    </button>
                </form>
            @endif
        @endcan
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        BOM Summary
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-3">
                <strong>BOM No:</strong> {{ $bom->bom_number }}
            </div>
            <div class="col-md-2">
                <strong>Version:</strong> {{ $bom->version }}
            </div>
            <div class="col-md-2">
                <strong>Status:</strong> {{ ucfirst($bom->status->value) }}
            </div>
            <div class="col-md-3">
                <strong>Total Weight:</strong> {{ $bom->total_weight }} kg
            </div>
            <div class="col-md-2">
                <strong>Finalized:</strong>
                @if($bom->finalized_date)
                    {{ $bom->finalized_date->format('d-m-Y') }} by {{ $bom->finalizedBy?->name }}
                @else
                    <span class="text-muted">Not finalized</span>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-12">
                <strong>Remarks:</strong>
                {{ $bom->metadata['remarks'] ?? '-' }}
            </div>
        </div>

        @if(!empty($categorySummary))
            <hr>
            <h6>Category-wise Summary (leaf materials)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Material Category</th>
                            <th>Lines</th>
                            <th>Total Weight (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorySummary as $cat => $row)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $cat)) }}</td>
                                <td>{{ $row['lines'] }}</td>
                                <td>{{ number_format($row['total_weight'], 3) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@include('projects.boms.partials._items_table', [
    'project' => $project,
    'bom' => $bom,
    'assemblyWeights' => $assemblyWeights,
])
@endsection
