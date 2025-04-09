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
    // Список ролей для комиссии
    private function getCommissionRoles(): array
    {
        return [
            'chairman' => __('Председатель комиссии'),
            'deputy_chairman' => __('Заместитель председателя'),
            'member' => __('Член комиссии'),
            'expert' => __('Эксперт ОПР'),
        ];
        // Ключи могут быть использованы для хранения в JSON
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Workplace $workplace): View
    {
        $this->authorize('view', $workplace);

        $riskMaps = $workplace->riskMaps()->with('conductedBy')->latest('assessment_date')->paginate(15);
        return view('risk-maps.index', compact('workplace', 'riskMaps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Workplace $workplace): View
    {
        $this->authorize('update', $workplace);
        $commissionRoles = $this->getCommissionRoles();
        return view('risk-maps.create', compact('workplace', 'commissionRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Workplace $workplace): RedirectResponse
    {
        $this->authorize('update', $workplace);

        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'status' => 'required|string|in:draft,completed,archived',
            'commission_members' => 'nullable|json',
            'participants' => 'nullable|json',
        ]);

        $validated['workplace_id'] = $workplace->id;
        $validated['conducted_by_user_id'] = Auth::id();

        $commissionInput = $request->input('commission_members');
        $validated['commission_members'] = $commissionInput ? json_decode($commissionInput, true) : null;

        $participantsInput = $request->input('participants');
        $validated['participants'] = $participantsInput ? json_decode($participantsInput, true) : null;

        $riskMap = RiskMap::create($validated);

        return redirect()->route('risk-maps.show', $riskMap)
            ->with('success', 'Карта рисков успешно создана. Теперь можно добавлять оценки.');
    }

    /**
     * Display the specified resource.
     * This page will show map details and manage its assessments.
     */
    public function show(RiskMap $riskMap): View // Shallow binding
    {
        $this->authorize('view', $riskMap);

        $workplace = $riskMap->workplace->load('department.organization'); // Eager load related data

        // Load assessments for this map, including related hazard
        $assessments = $riskMap->riskAssessments()
            ->with('hazard') // Eager load hazard details
            // ->with('existingMeasures', 'plannedMeasures') // Load measures later if needed
            ->orderBy('id') // Or order by hazard code, risk level etc.
            ->get(); // Get all assessments for this map

        return view('risk-maps.show', compact('riskMap', 'workplace', 'assessments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskMap $riskMap): View // Shallow binding
    {
        $this->authorize('update', $riskMap);

        $workplace = $riskMap->workplace;
        $commissionRoles = $this->getCommissionRoles();

        // Передаем существующий riskMap (включая commission_members как массив)
        return view('risk-maps.edit', compact('riskMap', 'workplace', 'commissionRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskMap $riskMap): RedirectResponse // Shallow binding
    {
        $this->authorize('update', $riskMap);

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
            ->with('success', 'Детали карты рисков успешно обновлены.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskMap $riskMap): RedirectResponse // Shallow binding
    {
        $this->authorize('delete', $riskMap);

        $workplace = $riskMap->workplace;
        // Deleting a map should cascade delete assessments via DB constraints or model events
        $riskMap->delete(); // Soft delete if enabled

        return redirect()->route('workplaces.risk-maps.index', $workplace)
            ->with('success', 'Карта рисков успешно удалена.');
    }
}
