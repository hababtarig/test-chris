<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;

// Redirect root to login
Route::get('/', fn () => redirect('/login'));

// Admin-only routes
Route::middleware(['auth', 'verified', 'isadmin'])->group(function () {
    Route::get('/admin/dashboard', [UserManagementController::class, 'index'])->name('dashboard');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/make-admin', [UserManagementController::class, 'makeAdmin'])->name('users.makeAdmin');

    // Admin landing page (admin-options)
    Route::get('/admin/options', fn () => view('admin-options'))->name('admin.landing');
});

// Authenticated user profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// All authenticated users
Route::middleware(['auth', 'verified'])->get('/verquin-app', fn () => view('verquin'))->name('verquin');

require __DIR__ . '/auth.php';
