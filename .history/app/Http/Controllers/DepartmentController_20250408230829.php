<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    private function getParentDepartmentsForSelect(Organization $organization, ?Department $excludeDepartment = null)
    {
        // Авторизация здесь не требуется, т.к. используется внутри авторизованных методов
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
        // Проверяем, может ли пользователь видеть *эту* организацию (и, следовательно, ее отделы)
        $this->authorize('view', $organization);
        // Политика viewAny для Department сама по себе менее полезна, т.к. отделы всегда в контексте организации
        // $this->authorize('viewAny', Department::class);

        // Фильтрация по правам не нужна, т.к. доступ к организации уже проверен
        $departments = $organization->departments()->with('parent')->paginate(15);
        return view('departments.index', compact('organization', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization): View
    {
        // Проверяем, может ли пользователь создавать отделы *в этой* организации
        $this->authorize('createDepartments', $organization); // Может потребоваться кастомный метод в OrganizationPolicy или проверка через DepartmentPolicy::create
        // Или, если create в DepartmentPolicy не требует организации:
        $this->authorize('create', Department::class);

        $parentDepartments = $this->getParentDepartmentsForSelect($organization);
        return view('departments.create', compact('organization', 'parentDepartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        // Авторизация аналогична create
        $this->authorize('create', Department::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        // Validate that parent_id belongs to the same organization
        if ($request->filled('parent_id') && !$organization->departments()->where('id', $request->parent_id)->exists()) {
            return back()->withErrors(['parent_id' => 'Выбранный родительский отдел не принадлежит этой организации.'])->withInput();
        }

        $organization->departments()->create($validated);

        return redirect()->route('organizations.departments.index', $organization)
            ->with('success', 'Отдел успешно создан.'); // Перевод
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department): View // Используется неявная привязка
    {
        $this->authorize('view', $department);
        $organization = $department->organization; // Получаем родительскую организацию
        return view('departments.show', compact('organization', 'department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department): View // Используется неявная привязка
    {
        $this->authorize('update', $department);
        $organization = $department->organization;
        $parentDepartments = $this->getParentDepartmentsForSelect($organization, $department);
        return view('departments.edit', compact('organization', 'department', 'parentDepartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department): RedirectResponse // Используется неявная привязка
    {
        $this->authorize('update', $department);
        $organization = $department->organization;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id|not_in:' . $department->id,
        ]);

        // Validate that parent_id belongs to the same organization and is not a descendant
        if ($request->filled('parent_id')) {
            if (!$organization->departments()->where('id', $request->parent_id)->exists()) {
                return back()->withErrors(['parent_id' => 'Выбранный родительский отдел не принадлежит этой организации.'])->withInput();
            }
            // TODO: Add check to prevent setting a descendant as a parent
        }

        unset($validated['organization_id']);

        $department->update($validated);

        return redirect()->route('organizations.departments.index', $organization)
            ->with('success', 'Отдел успешно обновлен.'); // Перевод
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department): RedirectResponse // Используется неявная привязка
    {
        $this->authorize('delete', $department);
        $organization = $department->organization;

        // TODO: Consider implications: what happens to child departments or positions?
        $department->delete();

        return redirect()->route('organizations.departments.index', $organization)
            ->with('success', 'Отдел успешно удален.'); // Перевод
    }
}
