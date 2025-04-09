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
use Illuminate\Support\Facades\Auth;

class WorkplaceController extends Controller
{
    // Helper to get departments for select dropdown
    private function getDepartmentsForSelect(Organization $organization)
    {
        // Авторизация не нужна, вызывается из авторизованных методов
        // Возвращаем коллекцию объектов с полями id и name
        return $organization->departments()->orderBy('name')->select('id', 'name')->get();
    }

    // Helper to get positions for select dropdown
    private function getPositionsForSelect(Organization $organization, ?int $departmentId = null)
    {
        // Авторизация не нужна
        // Возвращаем коллекцию объектов с полями id и name
        return Position::whereHas('department', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderBy('name')->select('id', 'name')->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): View
    {
        $this->authorize('view', $organization);
        // $this->authorize('viewAny', Workplace::class); // Менее полезно

        $workplaces = $organization->workplaces()->with(['department', 'position'])->paginate(15);
        return view('workplaces.index', compact('organization', 'workplaces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization): View
    {
        $this->authorize('view', $organization); // Нужно видеть организацию, чтобы добавлять в нее РМ
        $this->authorize('create', Workplace::class);

        $departments = $this->getDepartmentsForSelect($organization);
        $positions = $this->getPositionsForSelect($organization);
        return view('workplaces.create', compact('organization', 'departments', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('view', $organization);
        $this->authorize('create', Workplace::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:workplaces,code',
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
                    // Доп. проверка: Пользователь должен иметь право на управление этим отделом?
                    // Например, Руководитель отдела может добавлять РМ только в свой отдел.
                    // Пока оставляем базовую проверку, что позиция принадлежит выбранному отделу.
                    $query->where('department_id', $request->department_id);
                }),
            ],
        ]);

        // Проверим права на department_id более детально для Руководителя отдела
        if ($request->user()->isDepartmentHead() && $request->user()->department_id != $request->department_id) {
            return back()->withErrors(['department_id' => 'Вы можете добавлять рабочие места только в свой отдел.'])->withInput();
        }

        // Создаем рабочее место. Явно укажем organization_id для связи.
        $workplace = new Workplace($validated);
        $workplace->organization_id = $organization->id; // Привязываем к организации
        $workplace->save();

        return redirect()->route('organizations.workplaces.index', $organization)
            ->with('success', 'Рабочее место успешно создано.'); // Перевод
    }

    /**
     * Display the specified resource.
     */
    public function show(Workplace $workplace): View // Shallow binding
    {
        $this->authorize('view', $workplace);
        $organization = $workplace->department->organization;
        return view('workplaces.show', compact('organization', 'workplace'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workplace $workplace): View // Shallow binding
    {
        $this->authorize('update', $workplace);
        $organization = $workplace->department->organization;
        $departments = $this->getDepartmentsForSelect($organization);
        $positions = $this->getPositionsForSelect($organization, $workplace->department_id);

        return view('workplaces.edit', compact('organization', 'workplace', 'departments', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workplace $workplace): RedirectResponse // Shallow binding
    {
        $this->authorize('update', $workplace);
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
                    $query->where('department_id', $request->department_id);
                }),
            ],
        ]);

        // Проверим права на department_id более детально для Руководителя отдела
        if ($request->user()->isDepartmentHead() && $request->user()->department_id != $request->department_id) {
            return back()->withErrors(['department_id' => 'Вы можете изменять рабочие места только в своем отделе.'])->withInput();
        }

        $workplace->update($validated);

        return redirect()->route('organizations.workplaces.index', $organization)
            ->with('success', 'Рабочее место успешно обновлено.'); // Перевод
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workplace $workplace): RedirectResponse // Shallow binding
    {
        $this->authorize('delete', $workplace);
        $organization = $workplace->department->organization;

        // TODO: Consider implications: deleting a workplace might require deleting associated Risk Maps first?
        $workplace->delete();

        return redirect()->route('organizations.workplaces.index', $organization)
            ->with('success', 'Рабочее место успешно удалено.'); // Перевод
    }
}
