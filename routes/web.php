<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Models\User;
use App\Http\Controllers\ScriptRunnerController;
use App\Http\Controllers\OpenVpnClientController;
use App\Http\Controllers\DeleteUserController;


// Redirect root to login
Route::get('/', fn () => redirect('/login'));

Route::middleware(['auth', 'verified', 'isadmin'])->prefix('admin')->group(function () {
    // Admin landing/welcome page (options)
    Route::get('/options', function () {
        return view('admin.options'); // resources/views/admin/options.blade.php
    })->name('admin.options');

    // Admin dashboard
Route::get('/dashboard', [UserManagementController::class, 'index'])->name('dashboard');


    // User management actions (only admin can call)
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/make-admin', [UserManagementController::class, 'makeAdmin'])->name('users.makeAdmin');
});



// Profile (shared for all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Verquin UI (custom layout, not Breeze)
Route::prefix('verquin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/user-management', fn () => view('verquin.user-management'))->name('verquin.user');
    Route::get('/device-management', fn () => view('verquin.device-management'))->name('verquin.device');
    Route::get('/streaming', fn () => view('verquin.streaming'))->name('verquin.stream');
});
Route::post('/verquin/user-management/create', [ScriptRunnerController::class, 'createLinuxUser'])
    ->name('user.create');
 Route::post('/user-management/create-openvpn-client', [OpenVpnClientController::class, 'createClient'])
        ->name('openvpn.client.create');

// Fallback view to general Verquin app (e.g., landing, dashboard shell)
Route::middleware(['auth', 'verified'])->get('/verquin-app', function () {
    $users = User::all(); // Or filter by roles if needed
    return view('verquin.dashboard', compact('users'));

})->name('verquin');



Route::post('/verquin/user-management/delete', [DeleteUserController::class, 'delete'])->name('user.delete');

// Breeze auth scaffolding
require __DIR__ . '/auth.php';
