<?php

namespace App\Http\Controllers;

use App\Models\RiskMap;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RiskMapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Workplace $workplace): View
    {
        $riskMaps = $workplace->riskMaps()->with('conductedBy')->latest('assessment_date')->paginate(15);
        return view('risk-maps.index', compact('workplace', 'riskMaps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Workplace $workplace): View
    {
        return view('risk-maps.create', compact('workplace'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Workplace $workplace): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'status' => 'required|string|in:draft,completed,archived',
            'commission_members' => 'nullable|json', // Basic validation, consider more specific rules
            'participants' => 'nullable|json',
        ]);

        $validated['workplace_id'] = $workplace->id;
        $validated['conducted_by_user_id'] = Auth::id(); // Set conductor to current user

        // Attempt to decode JSON fields, fallback to original string if invalid
        $validated['commission_members'] = json_decode($request->input('commission_members', '')) ?? $request->input('commission_members');
        $validated['participants'] = json_decode($request->input('participants', '')) ?? $request->input('participants');

        $riskMap = RiskMap::create($validated);

        // Redirect to the show page to manage assessments
        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Risk Map created successfully. You can now add assessments.');
    }

    /**
     * Display the specified resource.
     * This page will show map details and manage its assessments.
     */
    public function show(RiskMap $riskMap): View // Shallow binding
    {
        $workplace = $riskMap->workplace->load('department.organization'); // Eager load related data
        // TODO: Load assessments for this map
        // $assessments = $riskMap->riskAssessments()->with('hazard', 'existingMeasures', 'plannedMeasures')->get();
        $assessments = collect(); // Placeholder

        return view('risk-maps.show', compact('riskMap', 'workplace', 'assessments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskMap $riskMap): View // Shallow binding
    {
        $workplace = $riskMap->workplace;
        return view('risk-maps.edit', compact('riskMap', 'workplace'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskMap $riskMap): RedirectResponse // Shallow binding
    {
        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'status' => 'required|string|in:draft,completed,archived',
            'commission_members' => 'nullable|json',
            'participants' => 'nullable|json',
        ]);

        // Attempt to decode JSON fields
        $validated['commission_members'] = json_decode($request->input('commission_members', '')) ?? $request->input('commission_members');
        $validated['participants'] = json_decode($request->input('participants', '')) ?? $request->input('participants');

        // workplace_id and conducted_by_user_id should generally not be updated here
        unset($validated['workplace_id'], $validated['conducted_by_user_id']);

        $riskMap->update($validated);

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Risk Map details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskMap $riskMap): RedirectResponse // Shallow binding
    {
        $workplace = $riskMap->workplace;
        // Deleting a map should cascade delete assessments via DB constraints or model events
        $riskMap->delete(); // Soft delete if enabled

        return redirect()->route('workplaces.risk-maps.index', $workplace)
            ->with('success', 'Risk Map deleted successfully.');
    }
}
