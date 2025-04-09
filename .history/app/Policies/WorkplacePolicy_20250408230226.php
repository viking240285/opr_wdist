<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkplacePolicy
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
     * Определяет, может ли пользователь просматривать список рабочих мест.
     * Примечание: Обычно список РМ просматривается в контексте отдела или организации.
     */
    public function viewAny(User $user): bool
    {
        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ, Сотрудник могут видеть списки (в рамках своих прав)
        return $user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist() || $user->isEmployee();
    }

    /**
     * Определяет, может ли пользователь просматривать конкретное рабочее место.
     */
    public function view(User $user, Workplace $workplace): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $workplace->department->organization_id) {
            return false;
        }

        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть РМ своей организации
        if ($user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist()) {
            return true; // Организацию проверили выше
        }
        // Сотрудник может видеть РМ своего отдела
        if ($user->isEmployee()) {
            return $user->department_id === $workplace->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь создавать рабочие места.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер или Руководитель отдела могут создавать РМ
        return $user->isManager() || $user->isDepartmentHead();
    }

    /**
     * Определяет, может ли пользователь обновлять рабочее место.
     */
    public function update(User $user, Workplace $workplace): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $workplace->department->organization_id) {
            return false;
        }

        // Администратор (before)
        // Менеджер может обновлять РМ своей организации
        if ($user->isManager()) {
            return true; // Организацию проверили выше
        }
        // Руководитель может обновлять РМ своего отдела
        if ($user->isDepartmentHead()) {
            return $user->department_id === $workplace->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь удалять рабочее место.
     */
    public function delete(User $user, Workplace $workplace): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $workplace->department->organization_id) {
            return false;
        }

        // Администратор (before)
        // Менеджер может удалять РМ своей организации
        if ($user->isManager()) {
            return true; // Организацию проверили выше
        }
        // Руководитель может удалять РМ своего отдела
        if ($user->isDepartmentHead()) {
            return $user->department_id === $workplace->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленное рабочее место.
     */
    public function restore(User $user, Workplace $workplace): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять рабочее место.
     */
    public function forceDelete(User $user, Workplace $workplace): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
