@extends('layouts.erp')

@section('title', 'Project Details')

@php
    $has = fn(string $name) => \Illuminate\Support\Facades\Route::has($name);

    $pickRoute = function (array $candidates) {
        foreach ($candidates as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return $name;
            }
        }
        return null;
    };

    // BOM route name can differ across builds
    $bomIndexRoute = $pickRoute([
        'projects.boms.index', // project-scoped
        'boms.index',          // global (fallback)
    ]);

    // Production routes (project-scoped preferred)
    $prodDashRoute = $pickRoute(['projects.production-dashboard.index', 'production-dashboard.index']);
    $prodPlansRoute = $pickRoute(['projects.production-plans.index', 'production.production-plans.index', 'production-plans.index']);
    $prodDprRoute = $pickRoute(['projects.production-dprs.index', 'production.production-dprs.index', 'production-dprs.index']);
    $prodQcRoute = $pickRoute(['projects.production-qc.index', 'production.production-qc.index', 'production-qc.index']);
    $prodBillingRoute = $pickRoute(['projects.production-billing.index', 'production.production-billing.index', 'production-billing.index']);
@endphp

@section('page_header')
    <div>
        <h1 class="h5 mb-0">
            Project {{ $project->code }}
        </h1>
        <small class="text-muted">
            {{ $project->name }}
        </small>
    </div>

    <div class="d-flex flex-wrap gap-2">

        @if($has('projects.index'))
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Projects
            </a>
        @endif

        @can('project.project.update')
            @if($has('projects.edit'))
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit Project
                </a>
            @endif
        @endcan

        {{-- Project BOMs button (route-safe, supports different route names) --}}
        @if($bomIndexRoute)
            @php
                // If route is global boms.index, it likely doesn't accept $project param.
                $bomParams = ($bomIndexRoute === 'boms.index') ? [] : $project;
            @endphp
            <a href="{{ route($bomIndexRoute, $bomParams) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-diagram-3 me-1"></i> Project BOMs
            </a>
        @endif

        {{-- Production shortcuts (Phase B–G) --}}
        @canany(['production.report.view','production.plan.view','production.dpr.view','production.qc.perform','production.billing.view'])
            @if($prodDashRoute && auth()->user()->can('production.report.view'))
                <a href="{{ route($prodDashRoute, $project) }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-speedometer2 me-1"></i> Prod Dashboard
                </a>
            @endif

            @if($prodPlansRoute && auth()->user()->can('production.plan.view'))
                <a href="{{ route($prodPlansRoute, $project) }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-clipboard2-check me-1"></i> Prod Plans
                </a>
            @endif

            @if($prodDprRoute && auth()->user()->can('production.dpr.view'))
                <a href="{{ route($prodDprRoute, $project) }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-journal-check me-1"></i> DPR
                </a>
            @endif

            @if($prodQcRoute && auth()->user()->can('production.qc.perform'))
                <a href="{{ route($prodQcRoute, $project) }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-shield-check me-1"></i> QC Pending
                </a>
            @endif

            @if($prodBillingRoute && auth()->user()->can('production.billing.view'))
                <a href="{{ route($prodBillingRoute, $project) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-receipt me-1"></i> Billing
                </a>
            @endif
        @endcanany

    </div>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="card-title text-uppercase small text-muted mb-0">Core Info</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-4 text-muted">Project Code</dt>
                        <dd class="col-sm-8">{{ $project->code }}</dd>

                        <dt class="col-sm-4 text-muted">Project Name</dt>
                        <dd class="col-sm-8">{{ $project->name }}</dd>

                        <dt class="col-sm-4 text-muted">Client</dt>
                        <dd class="col-sm-8">
                            @if($project->client)
                                <div>{{ $project->client->name }}</div>
                                <div class="text-muted small">{{ $project->client->code }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted">Project ID</dt>
                        <dd class="col-sm-8">#{{ $project->id }}</dd>

                        <dt class="col-sm-4 text-muted">Created At</dt>
                        <dd class="col-sm-8">
                            {{ optional($project->created_at)->format('d M Y, H:i') }}
                        </dd>

                        <dt class="col-sm-4 text-muted">Updated At</dt>
                        <dd class="col-sm-8">
                            {{ optional($project->updated_at)->format('d M Y, H:i') }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Placeholder for related data like BOM, tasks, etc. --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0 d-flex align-items-center justify-content-between">
                    <h6 class="card-title text-uppercase small text-muted mb-0">Related Data</h6>
                    {{-- You can add actions here later (e.g., "View Cutting Plans", "Purchase Summary") --}}
                </div>
                <div class="card-body small text-muted">
                    <p class="mb-0">
                        This section can be used to show related information such as BOMs,
                        cutting plans, purchase summaries or project notes.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
