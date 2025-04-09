<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Organization::class);

        // Администратор видит все, менеджер - свою организацию (если привязать)
        // Пока для простоты, если дошли сюда, показываем все доступные по viewAny
        // В будущем можно добавить фильтрацию по $user->organization_id для менеджера
        $organizations = Organization::paginate(15);
        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Organization::class);
        return view('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:20|unique:organizations,inn',
            'kpp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            // Add validation for logo if implemented
        ]);

        Organization::create($validated);

        // TODO: Add flash message for success
        // Example: session()->flash('message', 'Organization created successfully.');
        // Requires Bladewind Notification component in layout

        return redirect()->route('organizations.index')
            ->with('success', 'Организация успешно создана.'); // Переведем сообщение
    }

    /**
     * Display the specified resource.
     * Примечание: Метод show часто не нужен для CRUD, но если используется, добавим авторизацию
     */
    public function show(Organization $organization): View
    {
        $this->authorize('view', $organization);
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:20|unique:organizations,inn,' . $organization->id,
            'kpp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            // Add validation for logo if implemented
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Организация успешно обновлена.'); // Переведем сообщение
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', $organization);

        // TODO: Добавить проверку на наличие связанных сущностей (отделы, пользователи и т.д.)
        // перед удалением или использовать soft deletes.
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Организация успешно удалена.'); // Переведем сообщение
    }
}
