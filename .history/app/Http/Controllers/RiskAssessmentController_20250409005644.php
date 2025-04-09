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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RiskAssessmentController extends Controller
{
    // Helper to get Hazards for select dropdown
    private function getHazardsForSelect(?RiskMap $riskMap = null, ?RiskAssessment $excludeAssessment = null)
    {
        // Авторизация не нужна

        // Получаем ID опасностей, уже добавленных в эту карту (кроме текущей при редактировании)
        $existingHazardIds = collect(); // Пустая коллекция по умолчанию
        if ($riskMap) {
            $existingHazardIds = $riskMap->riskAssessments()
                                     ->when($excludeAssessment, fn($q) => $q->where('id', '!=', $excludeAssessment->id))
                                     ->pluck('hazard_id')
                                     ->filter()
                                     ->unique();
        }

        // Запрашиваем опасности, исключая уже добавленные
        return Hazard::orderBy('code')
            ->whereNotIn('id', $existingHazardIds)
            ->get()
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
        if ($riskValue < 50) return 'Низкий'; // Перевод
        if ($riskValue < 200) return 'Средний'; // Перевод
        return 'Высокий'; // Перевод
    }

    // Helper to get Measures for select dropdown
    private function getMeasuresForSelect()
    {
        // Авторизация не нужна
        // Возвращаем коллекцию объектов с полями id и description
        return Measure::orderBy('description')
            ->select('id', 'description') // Выбираем нужные поля
            ->get(); // Возвращаем коллекцию
    }

    /**
     * Sync measures for the given assessment.
     */
    private function syncMeasures(RiskAssessment $assessment, Request $request): void
    {
        // Авторизация не нужна
        $measuresToSync = [];
        foreach ($request->input('existing_measures', []) as $measureId) {
            $measuresToSync[$measureId] = ['measure_type' => 'existing'];
        }
        foreach ($request->input('planned_measures', []) as $measureId) {
            $measuresToSync[$measureId] = ['measure_type' => 'planned'];
        }
        $assessment->measures()->sync($measuresToSync);
    }

    /**
     * Display a listing of the resource.
     * Обычно оценки рисков отображаются в контексте RiskMap или Workplace
     */
    public function index()
    {
        // $this->authorize('viewAny', RiskAssessment::class);
        abort(404); // Не используется
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RiskMap $riskMap): View
    {
        // Проверяем право на создание оценки риска в контексте рабочего места (через riskMap)
        $this->authorize('create', [RiskAssessment::class, $riskMap->workplace]);

        $hazards = $this->getHazardsForSelect($riskMap);
        $measures = $this->getMeasuresForSelect();

        return view('risk-assessments.create', compact('riskMap', 'hazards', 'measures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, RiskMap $riskMap): RedirectResponse
    {
        $this->authorize('create', [RiskAssessment::class, $riskMap->workplace]);

        $validated = $request->validate([
            'hazard_id' => [
                'required',
                'integer',
                'exists:hazards,id',
                Rule::unique('risk_assessments')->where(function ($query) use ($riskMap) {
                    return $query->where('risk_map_id', $riskMap->id);
                }),
            ],
            'probability' => 'required|numeric|min:0',
            'severity' => 'required|numeric|min:0',
            'exposure' => 'required|numeric|min:0',
            'existing_measures' => 'nullable|array',
            'existing_measures.*' => 'integer|exists:measures,id',
            'planned_measures' => 'nullable|array',
            'planned_measures.*' => 'integer|exists:measures,id',
        ], [
            'hazard_id.unique' => 'Эта опасность уже добавлена в данную карту рисков.' // Перевод
        ]);

        $validated['risk_map_id'] = $riskMap->id;
        $validated['calculated_risk'] = $this->calculateRisk($validated['probability'], $validated['severity'], $validated['exposure']);
        $validated['risk_category'] = $this->getRiskCategory($validated['calculated_risk']);

        $assessment = RiskAssessment::create($validated);
        $this->syncMeasures($assessment, $request);

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Оценка риска успешно добавлена.'); // Перевод
    }

    /**
     * Display the specified resource.
     */
    public function show(RiskAssessment $assessment): void
    {
        $this->authorize('view', $assessment);
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskAssessment $assessment): View // Shallow binding
    {
        $this->authorize('update', $assessment);

        $riskMap = $assessment->riskMap;
        $hazards = $this->getHazardsForSelect($riskMap, $assessment);
        if (!isset($hazards[$assessment->hazard_id])) {
            $currentHazard = Hazard::find($assessment->hazard_id);
            if ($currentHazard) {
                $hazards[$currentHazard->id] = $currentHazard->code . ' - ' . Str::limit($currentHazard->source, 50);
            }
        }
        $measures = $this->getMeasuresForSelect();
        $assessment->load('existingMeasures', 'plannedMeasures');

        return view('risk-assessments.edit', compact('riskMap', 'assessment', 'hazards', 'measures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskAssessment $assessment): RedirectResponse // Shallow binding
    {
        $this->authorize('update', $assessment);

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
            'existing_measures' => 'nullable|array',
            'existing_measures.*' => 'integer|exists:measures,id',
            'planned_measures' => 'nullable|array',
            'planned_measures.*' => 'integer|exists:measures,id',
        ], [
            'hazard_id.unique' => 'Эта опасность уже добавлена в данную карту рисков.' // Перевод
        ]);

        $validated['calculated_risk'] = $this->calculateRisk($validated['probability'], $validated['severity'], $validated['exposure']);
        $validated['risk_category'] = $this->getRiskCategory($validated['calculated_risk']);
        unset($validated['risk_map_id']);

        $assessmentData = Arr::except($validated, ['existing_measures', 'planned_measures']);
        $assessment->update($assessmentData);
        $this->syncMeasures($assessment, $request);

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Оценка риска успешно обновлена.'); // Перевод
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskAssessment $assessment): RedirectResponse // Shallow binding
    {
        $this->authorize('delete', $assessment);

        $riskMap = $assessment->riskMap;
        $assessment->delete();

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Оценка риска успешно удалена.'); // Перевод
    }
}
