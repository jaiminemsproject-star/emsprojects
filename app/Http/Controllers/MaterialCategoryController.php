<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialCategoryRequest;
use App\Http\Requests\UpdateMaterialCategoryRequest;
use App\Models\MaterialCategory;
use App\Models\MaterialType;

use Illuminate\Http\Request;
class MaterialCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:core.material_category.view')->only('index');
        $this->middleware('permission:core.material_category.create')->only(['create', 'store']);
        $this->middleware('permission:core.material_category.update')->only(['edit', 'update']);
        $this->middleware('permission:core.material_category.delete')->only('destroy');
    }
   public function index(Request $request)
{
    $types = MaterialType::orderBy('sort_order')
        ->orderBy('code')
        ->get();

    $query = MaterialCategory::with('type')
        ->withCount('subcategories');

    /* ------------------------------
     | Code filter
     |------------------------------*/
    if ($request->filled('code')) {
        $query->where('code', 'like', '%' . trim($request->code) . '%');
    }

    /* ------------------------------
     | Name filter
     |------------------------------*/
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . trim($request->name) . '%');
    }

    /* ------------------------------
     | Description filter
     |------------------------------*/
    if ($request->filled('description')) {
        $query->where('description', 'like', '%' . trim($request->description) . '%');
    }

    /* ------------------------------
     | Material Type filter
     |------------------------------*/
    if ($request->filled('material_type_id')) {
        $query->where('material_type_id', (int) $request->material_type_id);
    }

    /* ------------------------------
     | Status filter
     |------------------------------*/
    if ($request->status === 'active') {
        $query->where('is_active', true);
    } elseif ($request->status === 'inactive') {
        $query->where('is_active', false);
    }

    /* ------------------------------
     | Sorting
     |------------------------------*/
    $sort = (string) $request->get('sort', '');
    $dir  = strtolower((string) $request->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

    $sortable = [
        'material_type_id',
        'code',
        'name',
        'sort_order',
        'is_active',
        'subcategories_count',
        'created_at',
    ];

    if ($sort && in_array($sort, $sortable, true)) {
        $query->orderBy($sort, $dir);
    } else {
        // default order (same as before)
        $query->orderBy('material_type_id')
              ->orderBy('sort_order')
              ->orderBy('code');
    }

    $categories = $query->paginate(10)->withQueryString();

    return view('material_categories.index', compact('categories', 'types'));
}


    public function create()
    {
        $category = new MaterialCategory();
        $types = MaterialType::orderBy('sort_order')->orderBy('code')->get();

        return view('material_categories.create', compact('category', 'types'));
    }

    public function store(StoreMaterialCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        MaterialCategory::create($data);

        return redirect()
            ->route('material-categories.index')
            ->with('success', 'Material category created successfully.');
    }

    public function edit(MaterialCategory $material_category)
    {
        $category = $material_category;
        $types = MaterialType::orderBy('sort_order')->orderBy('code')->get();

        return view('material_categories.edit', compact('category', 'types'));
    }

    public function update(UpdateMaterialCategoryRequest $request, MaterialCategory $material_category)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $material_category->update($data);

        return redirect()
            ->route('material-categories.index')
            ->with('success', 'Material category updated successfully.');
    }

    public function destroy(MaterialCategory $material_category)
    {
        if ($material_category->subcategories()->exists()) {
            return redirect()
                ->route('material-categories.index')
                ->with('error', 'Cannot delete material category while subcategories are assigned.');
        }

        if ($material_category->items()->exists()) {
            return redirect()
                ->route('material-categories.index')
                ->with('error', 'Cannot delete material category while items are assigned.');
        }

        $material_category->delete();

        return redirect()
            ->route('material-categories.index')
            ->with('success', 'Material category deleted successfully.');
    }
}



