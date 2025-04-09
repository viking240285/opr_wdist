<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeasureController extends Controller
{
    // Helper to get users for select dropdown
    private function getUsersForSelect()
    {
        // TODO: Возможно, стоит ограничить выбор пользователей текущей организацией,
        // если справочник мер станет привязан к организации.
        // Пока возвращаем всех.
        // Убираем ->toArray(), чтобы вернуть коллекцию объектов User
        return User::orderBy('name')->select('id', 'name')->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Measure::class);

        $query = Measure::query()->with('responsibleUser');

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhereHas('responsibleUser', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }
        $measures = $query->latest()->paginate(20)->withQueryString();

        return view('measures.index', compact('measures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Measure::class);
        $users = $this->getUsersForSelect();
        return view('measures.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Measure::class);

        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'required|string|in:planned,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'responsible_user_id' => 'nullable|integer|exists:users,id',
        ]);

        Measure::create($validated);

        return redirect()->route('measures.index')
            ->with('success', 'Мера контроля успешно создана.'); // Перевод
    }

    /**
     * Display the specified resource.
     */
    public function show(Measure $measure): View
    {
        $this->authorize('view', $measure);
        return view('measures.show', compact('measure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Measure $measure): View
    {
        $this->authorize('update', $measure);
        $users = $this->getUsersForSelect();
        return view('measures.edit', compact('measure', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Measure $measure): RedirectResponse
    {
        $this->authorize('update', $measure);

        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'required|string|in:planned,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'responsible_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $measure->update($validated);

        return redirect()->route('measures.index')
            ->with('success', 'Мера контроля успешно обновлена.'); // Перевод
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Measure $measure): RedirectResponse
    {
        $this->authorize('delete', $measure);

        // TODO: Check if the measure is linked to any assessments?
        // Note: The pivot table `assessment_measure` has cascadeOnDelete

        $measure->delete();

        return redirect()->route('measures.index')
            ->with('success', 'Мера контроля успешно удалена.'); // Перевод
    }
}
