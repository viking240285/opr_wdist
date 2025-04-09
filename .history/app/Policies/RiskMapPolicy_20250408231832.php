<?php

namespace App\Policies;

use App\Models\RiskMap;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskMapPolicy
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
     * Определяет, может ли пользователь просматривать список карт рисков.
     * Обычно просмотр идет в контексте рабочего места.
     */
    public function viewAny(User $user): bool
    {
        // Разрешаем всем, кто может видеть рабочие места
        return $user->can('viewAny', \App\Models\Workplace::class);
    }

    /**
     * Определяет, может ли пользователь просматривать конкретную карту рисков.
     */
    public function view(User $user, RiskMap $riskMap): bool
    {
        // Проверяем через политику связанного рабочего места
        return $user->can('view', $riskMap->workplace);
    }

    /**
     * Определяет, может ли пользователь создавать карты рисков.
     * Карта рисков обычно создается автоматически или через WorkplaceController.
     * Прямое создание может быть ограничено.
     */
    public function create(User $user): bool
    {
        // Разрешим только администраторам, менеджерам, руководителям отделов?
        // Или привяжем к праву на создание Workplace?
        // Пока оставим как у Workplace
        return $user->can('create', \App\Models\Workplace::class);
    }

    /**
     * Определяет, может ли пользователь обновлять карту рисков.
     * Обновление карты - это добавление/удаление оценок, делается через RiskAssessmentController.
     * Прямое обновление полей самой RiskMap может быть не нужно.
     */
    public function update(User $user, RiskMap $riskMap): bool
    {
        // Проверяем через политику связанного рабочего места
        return $user->can('update', $riskMap->workplace);
    }

    /**
     * Определяет, может ли пользователь удалять карту рисков.
     */
    public function delete(User $user, RiskMap $riskMap): bool
    {
        // Проверяем через политику связанного рабочего места
        return $user->can('delete', $riskMap->workplace);
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленную карту рисков.
     */
    public function restore(User $user, RiskMap $riskMap): bool
    {
        return false; // Администратор обработан в before
    }

    /**
     * Определяет, может ли пользователь навсегда удалять карту рисков.
     */
    public function forceDelete(User $user, RiskMap $riskMap): bool
    {
        return false; // Администратор обработан в before
    }
}
