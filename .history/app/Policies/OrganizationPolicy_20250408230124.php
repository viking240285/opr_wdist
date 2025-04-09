<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Выполнить предварительные проверки авторизации.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) { // Администратор может все
            return true;
        }
    }

    /**
     * Определяет, может ли пользователь просматривать список организаций.
     */
    public function viewAny(User $user): bool
    {
        // Администратор уже обработан в before()
        // Менеджер может видеть список
        return $user->isManager();
    }

    /**
     * Определяет, может ли пользователь просматривать конкретную организацию.
     */
    public function view(User $user, Organization $organization): bool
    {
        // Администратор может (before)
        // Менеджер, Руководитель, Специалист ОТ, Сотрудник могут видеть свою организацию
        return $user->organization_id === $organization->id;
    }

    /**
     * Определяет, может ли пользователь создавать организации.
     */
    public function create(User $user): bool
    {
        // Только администратор (обработано в before)
        return false; // По умолчанию запрещено для остальных
    }

    /**
     * Определяет, может ли пользователь обновлять организацию.
     */
    public function update(User $user, Organization $organization): bool
    {
        // Администратор может (before)
        // Менеджер может обновлять свою организацию
        return $user->isManager() && $user->organization_id === $organization->id;
    }

    /**
     * Определяет, может ли пользователь удалять организацию.
     */
    public function delete(User $user, Organization $organization): bool
    {
        // Только администратор (обработано в before)
        return false; // По умолчанию запрещено для остальных
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленную организацию.
     */
    public function restore(User $user, Organization $organization): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять организацию.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
