<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Keuangan\Kas\PenerimaanController;
use App\Http\Controllers\Keuangan\Master\MasterJenisPenerimaanController;
use App\Http\Controllers\Keuangan\Master\MasterJenisPengeluaranController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/getSession', [LoginController::class, 'checkSession'])->name('session.check');

// Guest routes (only accessible when NOT logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes (only accessible when logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalMenus' => Menu::count(),
        ]);
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // MENU MANAGEMENT
    // Custom role menu routes (harus diatas resource agar tidak ditangkap {role} wildcard)
    Route::get('roles/{role}/menu', [RoleController::class, 'menu'])->name('roles.menu');
    Route::put('roles/{role}/menu', [RoleController::class, 'updateMenu'])->name('roles.updateMenu');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('menus', MenuController::class);

    // MENU MASTER
    Route::resource('master-jenis-penerimaan', MasterJenisPenerimaanController::class);
    Route::resource('master-jenis-pengeluaran', MasterJenisPengeluaranController::class);

    // API autocomplete
    Route::get('api/jenis-penerimaan/search', [MasterJenisPenerimaanController::class, 'search'])
        ->name('api.jenis-penerimaan.search');
    Route::get('api/jenis-pengeluaran/search', [MasterJenisPengeluaranController::class, 'search'])
        ->name('api.jenis-pengeluaran.search');

    // MENU KAS
    Route::resource('penerimaan', PenerimaanController::class);
});

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
