<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
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
     * Определяет, может ли пользователь просматривать список отделов.
     * Примечание: Обычно список отделов просматривается в контексте организации.
     * Этот метод может быть не так часто использован напрямую, но полезен для общих проверок.
     */
    public function viewAny(User $user): bool
    {
        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть список (в рамках своей организации)
        return $user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь просматривать конкретный отдел.
     */
    public function view(User $user, Department $department): bool
    {
        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть отделы своей организации
        if ($user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist()) {
            return $user->organization_id === $department->organization_id;
        }
        // Сотрудник может видеть свой отдел
        if ($user->isEmployee()) {
            return $user->department_id === $department->id && $user->organization_id === $department->organization_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь создавать отделы.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер может создавать отделы в своей организации
        return $user->isManager();
    }

    /**
     * Определяет, может ли пользователь обновлять отдел.
     */
    public function update(User $user, Department $department): bool
    {
        // Администратор (before)
        // Менеджер может обновлять отделы своей организации
        if ($user->isManager()) {
            return $user->organization_id === $department->organization_id;
        }
        // Руководитель может обновлять свой отдел
        if ($user->isDepartmentHead()) {
            return $user->department_id === $department->id && $user->organization_id === $department->organization_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь удалять отдел.
     */
    public function delete(User $user, Department $department): bool
    {
        // Администратор (before)
        // Менеджер может удалять отделы своей организации
        return $user->isManager() && $user->organization_id === $department->organization_id;
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленный отдел.
     */
    public function restore(User $user, Department $department): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять отдел.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
