<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUomRequest;
use App\Http\Requests\UpdateUomRequest;
use App\Models\Uom;

use Illuminate\Http\Request;
class UomController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:core.uom.view')->only(['index']);
        $this->middleware('permission:core.uom.create')->only(['create', 'store']);
        $this->middleware('permission:core.uom.update')->only(['edit', 'update']);
        $this->middleware('permission:core.uom.delete')->only(['destroy']);
    }
    public function index(Request $request)
{
    $query = Uom::query();

    // 🔍 Filters
    if ($request->filled('code')) {
        $query->where('code', 'like', '%' . trim($request->code) . '%');
    }

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . trim($request->name) . '%');
    }

    // Category from SELECT (exact match)
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('is_active', $request->status === 'active');
    }

    // 🔃 Sorting (managed here)
    $sortable = ['code', 'name', 'category', 'decimal_places', 'is_active', 'created_at'];

    $sort = $request->get('sort', 'created_at'); // default column
    $dir  = $request->get('dir', 'desc');        // default DESC

    if (! in_array($sort, $sortable, true)) {
        $sort = 'created_at';
    }

    if (! in_array($dir, ['asc', 'desc'], true)) {
        $dir = 'desc';
    }

    $query->orderBy($sort, $dir);

    // 📄 Pagination
    $uoms = $query->paginate(10)->withQueryString();

    return view('uoms.index', compact('uoms'));
}



    public function create()
    {
        $uom = new Uom();

        return view('uoms.create', compact('uom'));
    }

    public function store(StoreUomRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        Uom::create($data);

        return redirect()
            ->route('uoms.index')
            ->with('success', 'UOM created successfully.');
    }

    public function edit(Uom $uom)
    {
        return view('uoms.edit', compact('uom'));
    }

    public function update(UpdateUomRequest $request, Uom $uom)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $uom->update($data);

        return redirect()
            ->route('uoms.index')
            ->with('success', 'UOM updated successfully.');
    }

    public function destroy(Uom $uom)
    {
        // later we can check if UOM is used in transactions
        $uom->delete();

        return redirect()
            ->route('uoms.index')
            ->with('success', 'UOM deleted successfully.');
    }
}


