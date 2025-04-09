<?php

namespace App\Http\Controllers;

use App\Models\Workplace;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class WorkplaceController extends Controller
{
    // Helper to get departments for select dropdown
    private function getDepartmentsForSelect(Organization $organization)
    {
        return $organization->departments()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    // Helper to get positions for select dropdown (consider dynamic loading based on department)
    private function getPositionsForSelect(Organization $organization, ?int $departmentId = null)
    {
        // Currently fetches all positions in the org.
        // Ideally, filter by selected departmentId if provided (requires JS or separate requests)
        return Position::whereHas('department', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            }) // Basic filter if department ID is known
            ->orderBy('name')->pluck('name', 'id')->toArray();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): View
    {
        // Eager load relationships for efficiency
        $workplaces = $organization->workplaces()->with(['department', 'position'])->paginate(15);
        return view('workplaces.index', compact('organization', 'workplaces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization): View
    {
        $departments = $this->getDepartmentsForSelect($organization);
        $positions = $this->getPositionsForSelect($organization); // Get all positions initially
        return view('workplaces.create', compact('organization', 'departments', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:workplaces,code', // Unique code across all workplaces
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                }),
            ],
            'position_id' => [
                'required',
                'integer',
                Rule::exists('positions', 'id')->where(function ($query) use ($request, $organization) {
                    // Ensure position belongs to the selected department (and thus the org)
                    $query->where('department_id', $request->department_id);
                }),
            ],
        ]);

        $organization->workplaces()->create($validated); // Assumes Workplace model has organization_id fillable if not directly nested
        // If not using direct nesting relationship for creation:
        // $validated['organization_id'] = $organization->id;
        // Workplace::create($validated);

        return redirect()->route('organizations.workplaces.index', $organization)
            ->with('success', 'Workplace created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workplace $workplace): View // Shallow binding
    {
        $organization = $workplace->department->organization; // Get org via department
        return view('workplaces.show', compact('organization', 'workplace')); // Placeholder view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workplace $workplace): View // Shallow binding
    {
        $organization = $workplace->department->organization;
        $departments = $this->getDepartmentsForSelect($organization);
        // Load positions for the current department, or all if needed
        $positions = $this->getPositionsForSelect($organization, $workplace->department_id);
        // Consider loading *all* positions if you allow changing department and position simultaneously without JS
        // $all_positions = $this->getPositionsForSelect($organization);

        return view('workplaces.edit', compact('organization', 'workplace', 'departments', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workplace $workplace): RedirectResponse // Shallow binding
    {
        $organization = $workplace->department->organization;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:50', Rule::unique('workplaces', 'code')->ignore($workplace->id)],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                }),
            ],
            'position_id' => [
                'required',
                'integer',
                Rule::exists('positions', 'id')->where(function ($query) use ($request, $organization) {
                    // Ensure position belongs to the selected department (and thus the org)
                    // Use the submitted department_id for validation
                    $query->where('department_id', $request->department_id);
                }),
            ],
        ]);

        $workplace->update($validated);

        return redirect()->route('organizations.workplaces.index', $organization)
            ->with('success', 'Workplace updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workplace $workplace): RedirectResponse // Shallow binding
    {
        $organization = $workplace->department->organization;
        // Consider implications: deleting a workplace might require deleting associated Risk Maps first?
        // Add logic later if needed.
        $workplace->delete();

        return redirect()->route('organizations.workplaces.index', $organization)
            ->with('success', 'Workplace deleted successfully.');
    }
}
