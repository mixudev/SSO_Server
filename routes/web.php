<?php

use App\Http\Controllers\AdminActivityLogController;
use App\Http\Controllers\AdminAccessAreaController;
use App\Http\Controllers\AdminClientController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProfileController;
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
});


Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [AdminRoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/access-areas', [AdminAccessAreaController::class, 'index'])->name('access-areas.index');
        Route::get('/access-areas/create', [AdminAccessAreaController::class, 'create'])->name('access-areas.create');
        Route::post('/access-areas', [AdminAccessAreaController::class, 'store'])->name('access-areas.store');
        Route::get('/access-areas/{accessArea}/edit', [AdminAccessAreaController::class, 'edit'])->name('access-areas.edit');
        Route::put('/access-areas/{accessArea}', [AdminAccessAreaController::class, 'update'])->name('access-areas.update');
        Route::delete('/access-areas/{accessArea}', [AdminAccessAreaController::class, 'destroy'])->name('access-areas.destroy');

        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');
        Route::post('/clients/{client}/generate-passport', [AdminClientController::class, 'generatePassportClient'])->name('clients.generate-passport');
        Route::get('/clients/{client}/info', [AdminClientController::class, 'infoPassportClient'])->name('clients.info');
        Route::delete('/clients/{client}/delete-passport', [AdminClientController::class, 'deletePassportClient'])->name('clients.delete-passport');
        Route::post('/clients/clear-secret-session', [AdminClientController::class, 'clearSecretSession'])->name('clients.clear-secret-session');

        Route::get('/logs', [AdminActivityLogController::class, 'index'])->name('logs.index');
    });

require __DIR__.'/auth.php';
