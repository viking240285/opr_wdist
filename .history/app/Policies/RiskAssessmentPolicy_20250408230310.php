<?php

namespace App\Policies;

use App\Models\RiskAssessment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskAssessmentPolicy
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
     * Определяет, может ли пользователь просматривать список оценок рисков.
     * Примечание: Зависит от контекста (организация, отдел, рабочее место).
     */
    public function viewAny(User $user): bool
    {
        // Администратор (before)
        // Все остальные роли могут просматривать списки в рамках своей области видимости
        return $user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist() || $user->isEmployee();
    }

    /**
     * Определяет, может ли пользователь просматривать конкретную оценку риска.
     */
    public function view(User $user, RiskAssessment $riskAssessment): bool
    {
        // Получаем организацию, к которой относится оценка риска
        $assessmentOrganizationId = $riskAssessment->workplace->department->organization_id;

        // Проверяем принадлежность пользователя к той же организации
        if ($user->organization_id !== $assessmentOrganizationId) {
            return false;
        }

        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут видеть оценки своей организации
        if ($user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist()) {
            return true; // Организацию проверили выше
        }
        // Сотрудник может видеть оценки своего отдела
        if ($user->isEmployee()) {
            return $user->department_id === $riskAssessment->workplace->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь создавать оценки рисков.
     */
    public function create(User $user): bool
    {
        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут создавать
        return $user->isManager() || $user->isDepartmentHead() || $user->isSafetySpecialist();
    }

    /**
     * Определяет, может ли пользователь обновлять оценку риска.
     */
    public function update(User $user, RiskAssessment $riskAssessment): bool
    {
        // Получаем организацию, к которой относится оценка риска
        $assessmentOrganizationId = $riskAssessment->workplace->department->organization_id;

        // Проверяем принадлежность пользователя к той же организации
        if ($user->organization_id !== $assessmentOrganizationId) {
            return false;
        }

        // Администратор (before)
        // Менеджер, Руководитель, Специалист ОТ могут обновлять оценки в своей организации/отделе
        if ($user->isManager() || $user->isSafetySpecialist()) {
            return true; // Организацию проверили выше
        }
        if ($user->isDepartmentHead()) {
            return $user->department_id === $riskAssessment->workplace->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь удалять оценку риска.
     */
    public function delete(User $user, RiskAssessment $riskAssessment): bool
    {
        // Получаем организацию, к которой относится оценка риска
        $assessmentOrganizationId = $riskAssessment->workplace->department->organization_id;

        // Проверяем принадлежность пользователя к той же организации
        if ($user->organization_id !== $assessmentOrganizationId) {
            return false;
        }

        // Администратор (before)
        // Менеджер и Руководитель отдела могут удалять оценки в своей организации/отделе
        if ($user->isManager()) {
            return true; // Организацию проверили выше
        }
        if ($user->isDepartmentHead()) {
            return $user->department_id === $riskAssessment->workplace->department_id;
        }
        return false;
    }

    /**
     * Определяет, может ли пользователь восстанавливать удаленную оценку риска.
     */
    public function restore(User $user, RiskAssessment $riskAssessment): bool
    {
        // Только администратор (обработано в before)
        return false;
    }

    /**
     * Определяет, может ли пользователь навсегда удалять оценку риска.
     */
    public function forceDelete(User $user, RiskAssessment $riskAssessment): bool
    {
        // Только администратор (обработано в before)
        return false;
    }
}
