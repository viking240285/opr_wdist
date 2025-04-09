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
        return User::orderBy('name')->select('id', 'name')->get()->toArray(); // Format for Bladewind select
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Measure::query()->with('responsibleUser'); // Eager load user

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
        $users = $this->getUsersForSelect();
        return view('measures.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'required|string|in:planned,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'responsible_user_id' => 'nullable|integer|exists:users,id',
        ]);

        Measure::create($validated);

        return redirect()->route('measures.index')
            ->with('success', 'Measure created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Measure $measure): View
    {
        return view('measures.show', compact('measure')); // Placeholder view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Measure $measure): View
    {
        $users = $this->getUsersForSelect();
        return view('measures.edit', compact('measure', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Measure $measure): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'required|string|in:planned,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'responsible_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $measure->update($validated);

        return redirect()->route('measures.index')
            ->with('success', 'Measure updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Measure $measure): RedirectResponse
    {
        // Check if the measure is linked to any assessments?
        // if ($measure->riskAssessments()->exists()) {
        //     return redirect()->route('measures.index')
        //                      ->with('error', 'Cannot delete measure as it is linked to risk assessments.');
        // }
        // Note: The pivot table `assessment_measure` has cascadeOnDelete, so links should be removed automatically.

        $measure->delete();

        return redirect()->route('measures.index')
            ->with('success', 'Measure deleted successfully.');
    }
}
