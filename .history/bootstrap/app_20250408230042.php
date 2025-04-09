<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Импорты Моделей
use App\Models\Organization;
use App\Models\Department;
use App\Models\Workplace;
use App\Models\Hazard;
use App\Models\Measure;
use App\Models\RiskAssessment;
use App\Models\User;
use App\Models\Position;

// Импорты Политик
use App\Policies\OrganizationPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\WorkplacePolicy;
use App\Policies\HazardPolicy;
use App\Policies\MeasurePolicy;
use App\Policies\RiskAssessmentPolicy;
use App\Policies\UserPolicy;
use App\Policies\PositionPolicy;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withPolicies([
        Organization::class => OrganizationPolicy::class,
        Department::class => DepartmentPolicy::class,
        Workplace::class => WorkplacePolicy::class,
        Hazard::class => HazardPolicy::class,
        Measure::class => MeasurePolicy::class,
        RiskAssessment::class => RiskAssessmentPolicy::class,
        User::class => UserPolicy::class,
        Position::class => PositionPolicy::class,
    ])->create();
