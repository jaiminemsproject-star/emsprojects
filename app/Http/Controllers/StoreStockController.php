<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Project;
use App\Models\StoreStockItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StoreStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:store.stock.view');
    }

    public function index(Request $request): View
    {
        $items    = Item::orderBy('name')->get();
        $projects = Project::orderBy('code')->get();

        $query = StoreStockItem::with(['item.uom', 'project'])
            ->orderByDesc('id');

        // Filters
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->integer('item_id'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('material_category')) {
            $query->where('material_category', $request->input('material_category'));
        }

        // Status filter
        // IMPORTANT: Hide QC-hold stock by default. If user explicitly filters by status,
        // we respect it.
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', '!=', 'blocked_qc');
        }

        // Available filter: show only stock that can actually be issued
        // (qty_pcs_available OR weight_kg_available must be > 0) and status must be 'available'.
        if ($request->boolean('only_available')) {
            $query->where('status', 'available')
                ->where(function ($q) {
                    $q->where('qty_pcs_available', '>', 0)
                      ->orWhere('weight_kg_available', '>', 0);
                });
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($qi) use ($search) {
                    $qi->where('name', 'like', '%' . $search . '%')
                       ->orWhere('code', 'like', '%' . $search . '%');
                })
                ->orWhere('plate_number', 'like', '%' . $search . '%')
                ->orWhere('heat_number', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%')
                ->orWhere('grade', 'like', '%' . $search . '%');
            });
        }

        $stockItems = $query->paginate(50)->withQueryString();

        // For material_category filter options, get distinct values
        $categories = StoreStockItem::select('material_category')
            ->whereNotNull('material_category')
            ->distinct()
            ->orderBy('material_category')
            ->pluck('material_category')
            ->toArray();

        // Same for status
        $statuses = StoreStockItem::select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->toArray();

        return view('store_stock.index', [
            'stockItems' => $stockItems,
            'items'      => $items,
            'projects'   => $projects,
            'categories' => $categories,
            'statuses'   => $statuses,
            'filters'    => $request->all(),
        ]);
    }
}
