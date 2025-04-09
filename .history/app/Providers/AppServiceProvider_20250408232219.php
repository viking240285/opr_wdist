<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

// Импорты Моделей
use App\Models\Organization;
use App\Models\Department;
use App\Models\Workplace;
use App\Models\Hazard;
use App\Models\Measure;
use App\Models\RiskAssessment;
use App\Models\User;
use App\Models\Position;
use App\Models\RiskMap;

// Импорты Политик
use App\Policies\OrganizationPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\WorkplacePolicy;
use App\Policies\HazardPolicy;
use App\Policies\MeasurePolicy;
use App\Policies\RiskAssessmentPolicy;
use App\Policies\UserPolicy;
use App\Policies\PositionPolicy;
use App\Policies\RiskMapPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрация политик
        Auth::policy(Organization::class, OrganizationPolicy::class);
        Auth::policy(Department::class, DepartmentPolicy::class);
        Auth::policy(Workplace::class, WorkplacePolicy::class);
        Auth::policy(Hazard::class, HazardPolicy::class);
        Auth::policy(Measure::class, MeasurePolicy::class);
        Auth::policy(RiskAssessment::class, RiskAssessmentPolicy::class);
        Auth::policy(User::class, UserPolicy::class);
        Auth::policy(Position::class, PositionPolicy::class);
        Auth::policy(RiskMap::class, RiskMapPolicy::class);
    }
}
