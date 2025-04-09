<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionPolicy
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
     * Определяет, может ли пользователь просматривать список должностей.
     * Примечание: Обычно список должностей просматривается в контексте отдела.
     */
    public function viewAny(User $user): bool
    {
        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть список (в рамках своей организации/отдела)
        return $user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь просматривать конкретную должность.
     */
    public function view(User $user, Position $position): bool
    {
        // Проверяем, что пользователь и должность принадлежат одной организации
        $userOrganizationId = $user->department->organization_id ?? $user->organization_id;
        $positionOrganizationId = $position->department->organization_id;
        if ($userOrganizationId !== $positionOrganizationId) {
            return false;
        }

        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть должности своей организации
        if ($user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist()) {
            return true; // Уже проверили организацию выше
        }
        // Сотрудник может видеть свою должность
        if ($user->isEmployee()) {
            return $user->position_id === $position->id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь создавать должности.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер или Руководитель отдела могут создавать должности
        return $user->isManager() || $user->isDepartmentHead();
    }

    /**
     * Определяет, может ли пользователь обновлять должность.
     */
    public function update(User $user, Position $position): bool
    {
        // Проверяем, что пользователь и должность принадлежат одной организации
        $userOrganizationId = $user->department->organization_id ?? $user->organization_id;
        $positionOrganizationId = $position->department->organization_id;
        if ($userOrganizationId !== $positionOrganizationId) {
            return false;
        }

        // Администратор (before)
        // Менеджер может обновлять должности своей организации
        if ($user->isManager()) {
            return true; // Уже проверили организацию выше
        }
        // Руководитель может обновлять должности своего отдела
        if ($user->isDepartmentHead()) {
            return $user->department_id === $position->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь удалять должность.
     */
    public function delete(User $user, Position $position): bool
    {
        // Проверяем, что пользователь и должность принадлежат одной организации
        $userOrganizationId = $user->department->organization_id ?? $user->organization_id;
        $positionOrganizationId = $position->department->organization_id;
        if ($userOrganizationId !== $positionOrganizationId) {
            return false;
        }

        // Администратор (before)
        // Менеджер может удалять должности своей организации
        if ($user->isManager()) {
            return true; // Уже проверили организацию выше
        }
        // Руководитель может удалять должности своего отдела
        if ($user->isDepartmentHead()) {
            return $user->department_id === $position->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленную должность.
     */
    public function restore(User $user, Position $position): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять должность.
     */
    public function forceDelete(User $user, Position $position): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
