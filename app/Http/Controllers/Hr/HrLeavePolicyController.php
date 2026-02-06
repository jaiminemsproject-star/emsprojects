<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Hr\HrLeavePolicy;
use App\Models\Hr\HrLeaveType;
use Illuminate\Http\Request;

class HrLeavePolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = HrLeavePolicy::with('leaveType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $policies = $query->withCount('employees')
                          ->orderBy('name')
                          ->paginate(20)
                          ->withQueryString();

        return view('hr.leave-policies.index', compact('policies'));
    }

    public function create()
    {
        $leaveTypes = HrLeaveType::where('is_active', true)->orderBy('name')->get();
        return view('hr.leave-policies.form', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:hr_leave_policies,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'hr_leave_type_id' => 'required|exists:hr_leave_types,id',
            'annual_quota' => 'required|numeric|min:0|max:365',
            'accrual_type' => 'required|in:yearly,monthly,quarterly',
            'accrual_timing' => 'nullable|in:start,end',
            'prorate_for_new_joiners' => 'boolean',
            'effective_after_months' => 'nullable|integer|min:0',
            'applicable_to' => 'nullable|in:all,male,female',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['prorate_for_new_joiners'] = $request->boolean('prorate_for_new_joiners', true);

        HrLeavePolicy::create($validated);

        return redirect()->route('hr.leave-policies.index')
                         ->with('success', 'Leave policy created successfully.');
    }

    public function show(HrLeavePolicy $leavePolicy)
    {
        $leavePolicy->load('leaveType');
        $leavePolicy->loadCount('employees');
        return view('hr.leave-policies.show', ['policy' => $leavePolicy]);
    }

    public function edit(HrLeavePolicy $leavePolicy)
    {
        $leaveTypes = HrLeaveType::where('is_active', true)->orderBy('name')->get();
        return view('hr.leave-policies.form', ['policy' => $leavePolicy, 'leaveTypes' => $leaveTypes]);
    }

    public function update(Request $request, HrLeavePolicy $leavePolicy)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:hr_leave_policies,code,' . $leavePolicy->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'hr_leave_type_id' => 'required|exists:hr_leave_types,id',
            'annual_quota' => 'required|numeric|min:0|max:365',
            'accrual_type' => 'required|in:yearly,monthly,quarterly',
            'accrual_timing' => 'nullable|in:start,end',
            'prorate_for_new_joiners' => 'boolean',
            'effective_after_months' => 'nullable|integer|min:0',
            'applicable_to' => 'nullable|in:all,male,female',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['prorate_for_new_joiners'] = $request->boolean('prorate_for_new_joiners', true);

        $leavePolicy->update($validated);

        return redirect()->route('hr.leave-policies.index')
                         ->with('success', 'Leave policy updated successfully.');
    }

    public function destroy(HrLeavePolicy $leavePolicy)
    {
        if ($leavePolicy->employees()->exists()) {
            return back()->with('error', 'Cannot delete policy. It is assigned to employees.');
        }

        $leavePolicy->delete();

        return redirect()->route('hr.leave-policies.index')
                         ->with('success', 'Leave policy deleted successfully.');
    }
}
