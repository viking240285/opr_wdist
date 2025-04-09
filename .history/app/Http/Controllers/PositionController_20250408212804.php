<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Department $department): View
    {
        $positions = $department->positions()->paginate(15);
        return view('positions.index', compact('department', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Department $department): View
    {
        return view('positions.create', compact('department'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'department_id' is implicitly set
        ]);

        $department->positions()->create($validated);

        return redirect()->route('departments.positions.index', $department)
            ->with('success', 'Position created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position): View // Shallow binding
    {
        $department = $position->department; // Get parent department
        return view('positions.show', compact('department', 'position')); // Placeholder view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position): View // Shallow binding
    {
        $department = $position->department;
        return view('positions.edit', compact('department', 'position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position): RedirectResponse // Shallow binding
    {
        $department = $position->department;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Ensure department_id isn't mass assigned
        unset($validated['department_id']);

        $position->update($validated);

        return redirect()->route('departments.positions.index', $department)
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position): RedirectResponse // Shallow binding
    {
        $department = $position->department;
        // Consider implications for Workplaces linked to this Position?
        // Add logic later if needed.
        $position->delete();

        return redirect()->route('departments.positions.index', $department)
            ->with('success', 'Position deleted successfully.');
    }
}
