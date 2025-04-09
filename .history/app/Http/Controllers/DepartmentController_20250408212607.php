<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    private function getParentDepartmentsForSelect(Organization $organization, ?Department $excludeDepartment = null)
    {
        return $organization->departments()
            ->when($excludeDepartment, function ($query) use ($excludeDepartment) {
                // Exclude the current department and its children to prevent loops
                $query->where('id', '!=', $excludeDepartment->id)
                    ->where(function ($q) use ($excludeDepartment) {
                        // This needs a more robust way to exclude all descendants
                        // For now, just exclude immediate children if editing
                        $q->where('parent_id', '!=', $excludeDepartment->id)->orWhereNull('parent_id');
                    });
            })
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id') // Format for Bladewind select
            ->toArray();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): View
    {
        $departments = $organization->departments()->with('parent')->paginate(15);
        return view('departments.index', compact('organization', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization): View
    {
        $parentDepartments = $this->getParentDepartmentsForSelect($organization);
        return view('departments.create', compact('organization', 'parentDepartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id', // Ensure parent exists within the same org implicitly via route binding? No, need explicit check.
            // 'organization_id' is implicitly set below, no need to validate in form?
        ]);

        // Validate that parent_id belongs to the same organization
        if ($request->filled('parent_id') && !$organization->departments()->where('id', $request->parent_id)->exists()) {
            return back()->withErrors(['parent_id' => 'Selected parent department does not belong to this organization.'])->withInput();
        }

        $organization->departments()->create($validated);

        return redirect()->route('organizations.departments.index', $organization)
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department): View // Using shallow binding
    {
        $organization = $department->organization; // Get parent organization
        return view('departments.show', compact('organization', 'department')); // Placeholder view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department): View // Using shallow binding
    {
        $organization = $department->organization;
        $parentDepartments = $this->getParentDepartmentsForSelect($organization, $department);
        return view('departments.edit', compact('organization', 'department', 'parentDepartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department): RedirectResponse // Using shallow binding
    {
        $organization = $department->organization;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id|not_in:' . $department->id, // Prevent self-parenting & check existence
        ]);

        // Validate that parent_id belongs to the same organization and is not a descendant
        if ($request->filled('parent_id')) {
            if (!$organization->departments()->where('id', $request->parent_id)->exists()) {
                return back()->withErrors(['parent_id' => 'Selected parent department does not belong to this organization.'])->withInput();
            }
            // TODO: Add check to prevent setting a descendant as a parent (more complex query)
        }

        // Ensure organization_id isn't mass assigned if present in request
        unset($validated['organization_id']);

        $department->update($validated);

        return redirect()->route('organizations.departments.index', $organization)
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department): RedirectResponse // Using shallow binding
    {
        $organization = $department->organization;
        // Consider implications: what happens to child departments or positions?
        // For now, just delete. Add logic later if needed (e.g., prevent delete if children exist, reassign children, etc.)
        // Note: Foreign key constraint on parent_id is set to nullOnDelete in migration.
        $department->delete();

        return redirect()->route('organizations.departments.index', $organization)
            ->with('success', 'Department deleted successfully.');
    }
}
