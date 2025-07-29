<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Models\User;
use App\Http\Controllers\ScriptRunnerController;
use App\Http\Controllers\OpenVpnClientController;
use App\Http\Controllers\DeleteUserController;
use \App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\OpenVpnClientList;
use App\Http\Controllers\VpnFileController;
use App\Http\Controllers\VpnDeviceController;


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

Route::middleware(['auth', 'verified'])
    ->get('/verquin-app', fn () => view('verquin.dashboard'))
    ->name('verquin');
Route::prefix('verquin')->middleware(['auth', 'verified'])->group(function () {
 Route::get('/user-management', [OpenVpnClientList::class, 'listOpenVpnUsers'])->name('verquin.user');
    Route::get('/device-management', fn () => view('verquin.device-management'))->name('verquin.device');
    Route::get('/streaming', fn () => view('verquin.streaming'))->name('verquin.stream');
});
Route::post('/verquin/user-management/create', [ScriptRunnerController::class, 'createLinuxUser'])
    ->name('user.create');
 Route::post('/user-management/create-openvpn-client', [OpenVpnClientController::class, 'createClient'])
        ->name('openvpn.client.create');


Route::post('/verquin/user-management/delete', [DeleteUserController::class, 'delete'])->name('user.delete');

Route::get('/verquin/user-management/deletion-log-snippet', [TaskStatusController::class, 'latestDeleteLog'])->name('task.latest-delete-log');
Route::get('/verquin/user-management/latest-create-log', [TaskStatusController::class, 'latestCreateLog'])->name('task.latest-create-log');
Route::get('/verquin/user-management/openvpn-create', [TaskStatusController::class, 'latestOpenVpnCreateLog'])->name('task.latest-openvpn-create-log');
Route::get('/verquin/user-management/openvpn-delete', [TaskStatusController::class, 'latestOpenVpnDeleteLog'])->name('task.latest-openvpn-delete-log');

Route::post('/verquin/user-management/openvpn-delete', [OpenVpnClientController::class, 'deleteClient'])->name('openvpn.client.delete');


Route::post('/verquin/user-management/vpn-files/create', [VpnFileController::class, 'create'])
    ->name('vpn.file.create');

Route::post('/verquin/user-management/vpn-files/delete', [VpnFileController::class, 'delete'])
    ->name('vpn.file.delete');

Route::get('/verquin/user-management/vpn-files/create-log', [TaskStatusController::class, 'latestVpnFileCreateLog'])->name('task.latest-vpn-file-create-log');
Route::get('/verquin/user-management/vpn-files/delete-log', [TaskStatusController::class, 'latestVpnFileDeleteLog'])->name('task.latest-vpn-file-delete-log');


Route::post('/verquin/device-management/vpn-device/create', [VpnDeviceController::class, 'create'])
    ->name('vpn.device.create');
Route::post('/verquin/device-management/vpn-device/delete', [VpnDeviceController::class, 'delete'])
    ->name('vpn.device.delete');

Route::get('/verquin/device-management/vpn-device/create-log', [TaskStatusController::class, 'latestCreateVpnDeviceLog'])
    ->name('task.latest-vpn-device-create-log');
Route::get('/verquin/device-management/vpn-device/delete-log', [TaskStatusController::class, 'latestDeleteVpnDeviceLog'])
    ->name('task.latest-vpn-device-delete-log');

    
Route::prefix('verquin/streaming/sensor')->group(function () {
    Route::post('/create', [HaproxySensorController::class, 'create'])->name('haproxy.sensor.create');
    Route::post('/delete', [HaproxySensorController::class, 'delete'])->name('haproxy.sensor.delete');
});

Route::get('/task/log/haproxy-sensor/create', [TaskStatusController::class, 'latestHaproxySensorCreateLog'])->name('task.latest-haproxy-sensor-create-log');
Route::get('/task/log/haproxy-sensor/delete', [TaskStatusController::class, 'latestHaproxySensorDeleteLog'])->name('task.latest-haproxy-sensor-delete-log');


// Breeze auth scaffolding
require __DIR__ . '/auth.php';