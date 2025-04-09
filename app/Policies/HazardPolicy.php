<?php

namespace App\Policies;

use App\Models\Hazard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HazardPolicy
{
    use HandlesAuthorization;

    /**
     * Выполнить предварительные проверки авторизации.
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Определяет, может ли пользователь просматривать список опасностей.
     */
    public function viewAny(User $user): bool
    {
        // Все аутентифицированные пользователи могут просматривать справочник
        return true;
    }

    /**
     * Определяет, может ли пользователь просматривать конкретную опасность.
     */
    public function view(User $user, Hazard $hazard): bool
    {
        // Все аутентифицированные пользователи могут просматривать
        return true;
    }

    /**
     * Определяет, может ли пользователь создавать опасности.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер и Специалист ОТ могут создавать
        return $user->isManager() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь обновлять опасность.
     */
    public function update(User $user, Hazard $hazard): bool
    {
        // Администратор (before)
        // Менеджер и Специалист ОТ могут обновлять
        return $user->isManager() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь удалять опасность.
     */
    public function delete(User $user, Hazard $hazard): bool
    {
        // Администратор (before)
        // Менеджер и Специалист ОТ могут удалять
        return $user->isManager() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленную опасность.
     */
    public function restore(User $user, Hazard $hazard): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять опасность.
     */
    public function forceDelete(User $user, Hazard $hazard): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
