<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
//dd('web.php is loading'); // Sanity check

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Admin-only routes
Route::middleware(['auth', 'verified', 'isadmin'])->group(function () {
    Route::get('/admin/dashboard', [UserManagementController::class, 'index'])->name('dashboard');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

// Authenticated user profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
