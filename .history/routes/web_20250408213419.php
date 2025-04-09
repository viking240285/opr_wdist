<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\HazardController;
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

    // Add routes for Positions later, possibly nested under Departments or Organizations
});

require __DIR__ . '/auth.php';
