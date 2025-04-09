<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Department $department): View
    {
        // Проверяем, может ли пользователь видеть отдел (и, следовательно, его должности)
        $this->authorize('view', $department);
        // $this->authorize('viewAny', Position::class); // Менее полезно само по себе

        $positions = $department->positions()->paginate(15);
        return view('positions.index', compact('department', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Department $department): View
    {
        // Проверяем, может ли пользователь создавать должности *в этом отделе*
        // Это можно сделать, передав отдел в метод 'create' политики PositionPolicy
        // или добавив специальный метод в DepartmentPolicy.
        // Пока используем общую проверку на создание Position.
        $this->authorize('create', Position::class);
        // Дополнительно убедимся, что пользователь имеет право на этот отдел
        $this->authorize('update', $department); // Право на update отдела подразумевает право добавлять в него

        return view('positions.create', compact('department'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('create', Position::class);
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->positions()->create($validated);

        return redirect()->route('departments.positions.index', $department)
            ->with('success', 'Должность успешно создана.'); // Перевод
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position): View // Shallow binding
    {
        $this->authorize('view', $position);
        $department = $position->department;
        return view('positions.show', compact('department', 'position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position): View // Shallow binding
    {
        $this->authorize('update', $position);
        $department = $position->department;
        return view('positions.edit', compact('department', 'position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position): RedirectResponse // Shallow binding
    {
        $this->authorize('update', $position);
        $department = $position->department;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        unset($validated['department_id']);

        $position->update($validated);

        return redirect()->route('departments.positions.index', $department)
            ->with('success', 'Должность успешно обновлена.'); // Перевод
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position): RedirectResponse // Shallow binding
    {
        $this->authorize('delete', $position);
        $department = $position->department;

        // TODO: Consider implications for Workplaces linked to this Position?
        $position->delete();

        return redirect()->route('departments.positions.index', $department)
            ->with('success', 'Должность успешно удалена.'); // Перевод
    }
}
