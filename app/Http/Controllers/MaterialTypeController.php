<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialTypeRequest;
use App\Http\Requests\UpdateMaterialTypeRequest;
use App\Models\MaterialType;

use Illuminate\Http\Request;
class MaterialTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:core.material_type.view')->only('index');
        $this->middleware('permission:core.material_type.create')->only(['create', 'store']);
        $this->middleware('permission:core.material_type.update')->only(['edit', 'update']);
        $this->middleware('permission:core.material_type.delete')->only('destroy');
    }
   public function index(Request $request)
{
    $query = MaterialType::withCount('categories');

    /* --------------------
     | Filters
     |--------------------*/

    // Code filter
    if ($request->filled('code')) {
        $query->where('code', 'like', '%' . trim($request->code) . '%');
    }

    // Name filter
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . trim($request->name) . '%');
    }

    // Description filter
    if ($request->filled('description')) {
        $query->where('description', 'like', '%' . trim($request->description) . '%');
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('is_active', $request->status === 'active');
    }

    /* --------------------
     | Sorting
     |--------------------*/

    $sortable = [
        'code',
        'name',
        'sort_order',
        'is_active',
        'categories_count',
        'created_at',
    ];

    $sort = $request->get('sort');
    $dir  = $request->get('dir') === 'desc' ? 'desc' : 'asc';

    if (in_array($sort, $sortable, true)) {
        $query->orderBy($sort, $dir);
    } else {
        // Default sorting
        $query->orderBy('sort_order')
              ->orderBy('code');
    }

    /* --------------------
     | Pagination
     |--------------------*/

    $types = $query->paginate(25)->withQueryString();

    return view('material_types.index', compact('types'));
}


    public function create()
    {
        $type = new MaterialType();

        return view('material_types.create', compact('type'));
    }

    public function store(StoreMaterialTypeRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        MaterialType::create($data);

        return redirect()
            ->route('material-types.index')
            ->with('success', 'Material type created successfully.');
    }

    public function edit(MaterialType $material_type)
    {
        $type = $material_type;

        return view('material_types.edit', compact('type'));
    }

    public function update(UpdateMaterialTypeRequest $request, MaterialType $material_type)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $material_type->update($data);

        return redirect()
            ->route('material-types.index')
            ->with('success', 'Material type updated successfully.');
    }

    public function destroy(MaterialType $material_type)
    {
        if ($material_type->categories()->exists()) {
            return redirect()
                ->route('material-types.index')
                ->with('error', 'Cannot delete material type while categories are assigned.');
        }

        $material_type->delete();

        return redirect()
            ->route('material-types.index')
            ->with('success', 'Material type deleted successfully.');
    }
}


