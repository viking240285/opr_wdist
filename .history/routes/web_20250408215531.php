<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\HazardController;
use App\Http\Controllers\RiskMapController;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource route for Organizations
    Route::resource('organizations', OrganizationController::class);

    // Nested resource route for Departments within Organizations
    Route::resource('organizations.departments', DepartmentController::class)->scoped([
        'department' => 'id', // Optional: Customize scoping if needed
    ])->shallow(); // Makes routes like /departments/{department}/edit simpler

    // Nested resource route for Positions within Departments
    Route::resource('departments.positions', PositionController::class)->shallow();

    // Resource route for Workplaces (nested under Organizations for context)
    Route::resource('organizations.workplaces', WorkplaceController::class)->shallow();

    // Resource route for Hazards (global reference)
    Route::resource('hazards', HazardController::class);

    // Nested resource route for Risk Maps within Workplaces
    Route::resource('workplaces.risk-maps', RiskMapController::class)->shallow();

    // Nested resource route for Risk Assessments within Risk Maps
    Route::resource('risk-maps.assessments', RiskAssessmentController::class)->shallow()->except(['index']); // Index is handled by RiskMapController@show

    // Resource route for Measures (global reference)
    Route::resource('measures', MeasureController::class);

    // Settings Routes (Admin only)
    Route::middleware('admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('risk', [SettingsController::class, 'editRiskSettings'])->name('risk.edit');
        Route::patch('risk', [SettingsController::class, 'updateRiskSettings'])->name('risk.update');
        // Add other settings pages here (e.g., general, appearance)
    });
});
require __DIR__ . '/auth.php';
