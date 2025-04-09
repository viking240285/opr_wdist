<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
     * Определяет, может ли пользователь просматривать список пользователей.
     */
    public function viewAny(User $user): bool
    {
        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть списки (с ограничениями в контроллере)
        return $user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь просматривать профиль другого пользователя.
     */
    public function view(User $user, User $model): bool
    {
        // Администратор (before)
        // Пользователь может видеть свой профиль (обычно не через эту политику, а напрямую)
        if ($user->id === $model->id) {
            return true;
        }

        // Менеджер может видеть пользователей своей организации
        if ($user->isManager() && $user->organization_id === $model->organization_id) {
            return true;
        }

        // Руководитель может видеть пользователей своего отдела
        if ($user->isDepartmentHead() && $user->department_id === $model->department_id && $user->organization_id === $model->organization_id) {
            return true;
        }

        // Специалист ОТ может видеть пользователей своей организации
        if ($user->isSafetySpecialist() && $user->organization_id === $model->organization_id) {
            return true;
        }

        // Сотрудник может видеть пользователей своего отдела (для списка контактов, например)
        if ($user->isEmployee() && $user->department_id === $model->department_id && $user->organization_id === $model->organization_id) {
            return true;
        }

        return false;
    }

    /**
     * Определяет, может ли пользователь создавать пользователей.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер и Руководитель могут создавать пользователей (в своей области)
        return $user->isManager() || $user->isDepartmentHead();
    }

    /**
     * Определяет, может ли пользователь обновлять профиль другого пользователя.
     */
    public function update(User $user, User $model): bool
    {
        // Администратор (before) - может обновлять всех

        // Нельзя обновлять администратора, если ты не администратор
        if ($model->isAdmin()) {
            return false;
        }

        // Менеджер может обновлять пользователей своей организации (кроме админов)
        if ($user->isManager() && $user->organization_id === $model->organization_id) {
            // Менеджер не может повысить роль выше своей
            // (Эту логику лучше реализовать при обработке запроса, а не в политике)
            return true;
        }

        // Руководитель может обновлять пользователей своего отдела (кроме админов и менеджеров)
        if ($user->isDepartmentHead() && $user->department_id === $model->department_id && $user->organization_id === $model->organization_id) {
            if ($model->isManager()) { // Нельзя обновлять менеджера
                return false;
            }
            return true;
        }

        // Пользователь может обновить свой профиль (обрабатывается отдельно)
        // if ($user->id === $model->id) { return true; }

        return false;
    }

    /**
     * Определяет, может ли пользователь удалять другого пользователя.
     */
    public function delete(User $user, User $model): bool
    {
        // Нельзя удалить самого себя через этот метод
        if ($user->id === $model->id) {
            return false;
        }

        // Администратор (before) - может удалять всех (кроме себя, см выше)

        // Нельзя удалять администратора, если ты не администратор
        if ($model->isAdmin()) {
            return false;
        }

        // Менеджер может удалять пользователей своей организации (кроме админов)
        if ($user->isManager() && $user->organization_id === $model->organization_id) {
            return true;
        }

        // Руководитель может удалять пользователей своего отдела (кроме админов и менеджеров)
        if ($user->isDepartmentHead() && $user->department_id === $model->department_id && $user->organization_id === $model->organization_id) {
            if ($model->isManager()) { // Нельзя удалять менеджера
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленного пользователя.
     */
    public function restore(User $user, User $model): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять пользователя.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Нельзя удалить самого себя через этот метод
        if ($user->id === $model->id) {
            return false;
        }

        // Администратор (before) - может удалять всех (кроме себя)

        // Нельзя удалять администратора, если ты не администратор
        if ($model->isAdmin()) {
            return false;
        }

        // Менеджер может навсегда удалять пользователей своей организации (кроме админов)
        if ($user->isManager() && $user->organization_id === $model->organization_id) {
            return true;
        }

        // Руководитель может навсегда удалять пользователей своего отдела (кроме админов и менеджеров)
        if ($user->isDepartmentHead() && $user->department_id === $model->department_id && $user->organization_id === $model->organization_id) {
            if ($model->isManager()) {
                return false;
            }
            return true;
        }

        return false;
    }
}
