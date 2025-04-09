<?php

namespace App\Http\Controllers;

use App\Models\RiskAssessment;
use App\Models\RiskMap;
use App\Models\Hazard;
use App\Models\Measure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RiskAssessmentController extends Controller
{
    // Helper to get Hazards for select dropdown
    private function getHazardsForSelect(?RiskMap $riskMap = null, ?RiskAssessment $excludeAssessment = null)
    {
        return Hazard::orderBy('code')
            // Optionally exclude hazards already added to this map, except the one being edited
            ->when($riskMap, function ($query) use ($riskMap, $excludeAssessment) {
                $query->whereDoesntHave('riskAssessments', function ($q) use ($riskMap, $excludeAssessment) {
                    $q->where('risk_map_id', $riskMap->id)
                        ->when($excludeAssessment, function ($q2) use ($excludeAssessment) {
                            $q2->where('id', '!=', $excludeAssessment->id);
                        });
                });
            })
            ->get()
            // Format for Bladewind select: array of ['id' => ..., 'name' => 'CODE - Source Snippet']
            ->map(function ($hazard) {
                return ['id' => $hazard->id, 'name' => $hazard->code . ' - ' . Str::limit($hazard->source, 50)];
            })
            ->toArray();
    }

    // Simple risk calculation logic (move to a dedicated service later)
    private function calculateRisk(float $v, float $t, float $e): float
    {
        return round($v * $t * $e, 2);
    }

    // Simple category determination (move to service and make configurable later)
    private function getRiskCategory(float $riskValue): string
    {
        // Example thresholds - make these configurable in settings
        if ($riskValue < 50) return 'Low';
        if ($riskValue < 200) return 'Medium';
        return 'High';
    }

    // Helper to get Measures for select dropdown
    private function getMeasuresForSelect()
    {
        // Format for Bladewind select: array of ['id' => ..., 'name' => 'Description Snippet']
        return Measure::orderBy('description')
            ->get()
            ->map(function ($measure) {
                return ['id' => $measure->id, 'name' => Str::limit($measure->description, 80)];
            })
            ->toArray();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RiskMap $riskMap): View
    {
        $hazards = $this->getHazardsForSelect($riskMap);
        $measures = $this->getMeasuresForSelect();

        return view('risk-assessments.create', compact('riskMap', 'hazards', 'measures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, RiskMap $riskMap): RedirectResponse
    {
        $validated = $request->validate([
            'hazard_id' => [
                'required',
                'integer',
                'exists:hazards,id',
                // Ensure this hazard isn't already added to this specific risk map
                Rule::unique('risk_assessments')->where(function ($query) use ($riskMap) {
                    return $query->where('risk_map_id', $riskMap->id);
                }),
            ],
            'probability' => 'required|numeric|min:0',
            'severity' => 'required|numeric|min:0',
            'exposure' => 'required|numeric|min:0',
            // Add validation for measures if implemented
            // 'existing_measures' => 'nullable|array',
            // 'existing_measures.*' => 'integer|exists:measures,id',
            // 'planned_measures' => 'nullable|array',
            // 'planned_measures.*' => 'integer|exists:measures,id',
        ], [
            'hazard_id.unique' => 'This hazard has already been added to this risk map.'
        ]);

        $validated['risk_map_id'] = $riskMap->id;

        // Calculate Risk and Category
        $validated['calculated_risk'] = $this->calculateRisk($validated['probability'], $validated['severity'], $validated['exposure']);
        $validated['risk_category'] = $this->getRiskCategory($validated['calculated_risk']);

        $assessment = RiskAssessment::create($validated);

        // TODO: Sync measures using the pivot table (assessment_measure)
        // $assessment->measures()->syncWithPivotValues($request->input('existing_measures', []), ['measure_type' => 'existing']);
        // $assessment->measures()->syncWithoutDetaching($request->input('planned_measures', []), ['measure_type' => 'planned']); // Use syncWithoutDetaching if existing measures should also be selectable as planned?

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Risk assessment added successfully.');
    }

    /**
     * Display the specified resource.
     * Not typically needed as edit is used directly.
     */
    public function show(RiskAssessment $assessment): void
    {
        abort(404); // Or redirect to edit
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskAssessment $assessment): View // Shallow binding
    {
        $riskMap = $assessment->riskMap;
        $hazards = $this->getHazardsForSelect($riskMap, $assessment);
        if (!isset($hazards[$assessment->hazard_id])) {
            $currentHazard = Hazard::find($assessment->hazard_id);
            if ($currentHazard) {
                $hazards[$currentHazard->id] = $currentHazard->code . ' - ' . Str::limit($currentHazard->source, 50);
            }
        }
        $measures = $this->getMeasuresForSelect();

        // Eager load measures for the form
        $assessment->load('existingMeasures', 'plannedMeasures');

        return view('risk-assessments.edit', compact('riskMap', 'assessment', 'hazards', 'measures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskAssessment $assessment): RedirectResponse // Shallow binding
    {
        $riskMap = $assessment->riskMap;

        $validated = $request->validate([
            'hazard_id' => [
                'required',
                'integer',
                'exists:hazards,id',
                Rule::unique('risk_assessments')->where(function ($query) use ($riskMap) {
                    return $query->where('risk_map_id', $riskMap->id);
                })->ignore($assessment->id),
            ],
            'probability' => 'required|numeric|min:0',
            'severity' => 'required|numeric|min:0',
            'exposure' => 'required|numeric|min:0',
            // Add validation for measures if implemented
        ], [
            'hazard_id.unique' => 'This hazard has already been added to this risk map.'
        ]);

        // Calculate Risk and Category
        $validated['calculated_risk'] = $this->calculateRisk($validated['probability'], $validated['severity'], $validated['exposure']);
        $validated['risk_category'] = $this->getRiskCategory($validated['calculated_risk']);

        // Prevent changing the risk_map_id
        unset($validated['risk_map_id']);

        $assessment->update($validated);

        // TODO: Sync measures

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Risk assessment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskAssessment $assessment): RedirectResponse // Shallow binding
    {
        $riskMap = $assessment->riskMap;
        // Detach measures if using pivot table? Or let DB cascade handle it?
        $assessment->delete();

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Risk assessment deleted successfully.');
    }
}
