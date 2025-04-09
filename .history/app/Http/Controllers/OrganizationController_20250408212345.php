<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $organizations = Organization::paginate(15); // Paginate results
        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:20|unique:organizations,inn',
            'kpp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            // Add validation for logo if implemented
        ]);

        Organization::create($validated);

        // TODO: Add flash message for success
        // Example: session()->flash('message', 'Organization created successfully.');
        // Requires Bladewind Notification component in layout

        return redirect()->route('organizations.index')
            ->with('success', 'Organization created successfully.'); // Simple success message for now
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization): View // Keep or remove based on need
    {
        // Usually not needed for basic CRUD, index/edit is sufficient
        // If needed, create organizations/show.blade.php
        return view('organizations.show', compact('organization')); // Placeholder
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): View
    {
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:20|unique:organizations,inn,' . $organization->id,
            'kpp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            // Add validation for logo if implemented
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }
}
