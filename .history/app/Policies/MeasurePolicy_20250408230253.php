<?php

namespace App\Policies;

use App\Models\Measure;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeasurePolicy
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
     * Определяет, может ли пользователь просматривать список мер контроля.
     */
    public function viewAny(User $user): bool
    {
        // Все аутентифицированные пользователи могут просматривать справочник
        return true;
    }

    /**
     * Определяет, может ли пользователь просматривать конкретную меру контроля.
     */
    public function view(User $user, Measure $measure): bool
    {
        // Все аутентифицированные пользователи могут просматривать
        return true;
    }

    /**
     * Определяет, может ли пользователь создавать меры контроля.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер и Специалист ОТ могут создавать
        return $user->isManager() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь обновлять меру контроля.
     */
    public function update(User $user, Measure $measure): bool
    {
        // Администратор (before)
        // Менеджер и Специалист ОТ могут обновлять
        return $user->isManager() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь удалять меру контроля.
     */
    public function delete(User $user, Measure $measure): bool
    {
        // Администратор (before)
        // Менеджер и Специалист ОТ могут удалять
        return $user->isManager() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленную меру контроля.
     */
    public function restore(User $user, Measure $measure): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять меру контроля.
     */
    public function forceDelete(User $user, Measure $measure): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
