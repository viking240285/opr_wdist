<?php

namespace App\Http\Controllers;

use App\Models\Hazard;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class HazardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Hazard::class);

        // Basic search functionality (can be expanded)
        $query = Hazard::query();
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                    ->orWhere('type', 'like', "%{$searchTerm}%")
                    ->orWhere('source', 'like', "%{$searchTerm}%")
                    ->orWhere('threat', 'like', "%{$searchTerm}%");
            });
        }
        $hazards = $query->paginate(20)->withQueryString(); // Keep search query in pagination links

        return view('hazards.index', compact('hazards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Hazard::class);
        return view('hazards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Hazard::class);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:hazards,code',
            'type' => 'nullable|string|max:255',
            'source' => 'required|string',
            'threat' => 'required|string',
        ]);

        Hazard::create($validated);

        return redirect()->route('hazards.index')
            ->with('success', 'Опасность успешно создана.'); // Перевод
    }

    /**
     * Display the specified resource.
     */
    public function show(Hazard $hazard): View
    {
        $this->authorize('view', $hazard);
        return view('hazards.show', compact('hazard')); // Placeholder view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hazard $hazard): View
    {
        $this->authorize('update', $hazard);
        return view('hazards.edit', compact('hazard'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hazard $hazard): RedirectResponse
    {
        $this->authorize('update', $hazard);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('hazards', 'code')->ignore($hazard->id)],
            'type' => 'nullable|string|max:255',
            'source' => 'required|string',
            'threat' => 'required|string',
        ]);

        $hazard->update($validated);

        return redirect()->route('hazards.index')
            ->with('success', 'Опасность успешно обновлена.'); // Перевод
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hazard $hazard): RedirectResponse
    {
        $this->authorize('delete', $hazard);

        // Check if the hazard is used in any RiskAssessments before deleting?
        // if ($hazard->riskAssessments()->exists()) {
        //     return redirect()->route('hazards.index')
        //                      ->with('error', 'Cannot delete hazard as it is used in risk assessments.');
        // }

        $hazard->delete();

        return redirect()->route('hazards.index')
            ->with('success', 'Опасность успешно удалена.'); // Перевод
    }
}
