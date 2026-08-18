<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Keuangan\Kas\CicilanController;
use App\Http\Controllers\Keuangan\Kas\PenerimaanController;
use App\Http\Controllers\Keuangan\Kas\PengeluaranController;
use App\Http\Controllers\Keuangan\Master\MasterJenisPenerimaanController;
use App\Http\Controllers\Keuangan\Master\MasterJenisPengeluaranController;
use App\Http\Controllers\GantiPassController;
use App\Http\Controllers\Aset\TerimaasetController;
use App\Http\Controllers\Laporan\ArusKasController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/getSession', [LoginController::class, 'checkSession'])->name('session.check');

// Guest routes (only accessible when NOT logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::get('/debug-url', function () {
    return [
        'url' => request()->url(),
        'full_url' => request()->fullUrl(),
        'scheme' => request()->getScheme(),
        'secure' => request()->isSecure(),
        'host' => request()->getHost(),
    ];
});

// Authenticated routes (only accessible when logged in)
Route::middleware(['auth', 'checkRole'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // MENU MANAGEMENT
    // Custom role menu routes (harus diatas resource agar tidak ditangkap {role} wildcard)
    Route::get('roles/{role}/menu', [RoleController::class, 'menu'])->name('roles.menu');
    Route::put('roles/{role}/menu', [RoleController::class, 'updateMenu'])->name('roles.updateMenu');

    // Route::resource('ganti-password', GantiPassController::class);
    Route::get('ganti-password', [GantiPassController::class, 'index'])->name('ganti-password.index');
    Route::put('ganti-password', [GantiPassController::class, 'update'])->name('ganti-password.update');

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
    Route::resource('pengeluaran', PengeluaranController::class);

    // MENU ASET
    Route::resource('terima-aset', TerimaasetController::class);
    Route::get('api/aset/detail/{id}', [TerimaasetController::class, 'getDetail'])->name('api.aset.detail');

    // API cicilan
    Route::get('api/cicilan/{id}/detail', [CicilanController::class, 'getDetail'])->name('api.cicilan.detail');
    Route::get('api/cicilan/{id}', [CicilanController::class, 'getCicilan'])->name('api.cicilan.get');
    Route::get('api/cicilan/delete/{id}', [CicilanController::class, 'destroyBayar'])->name('cicilan.destroyBayar');
    Route::post('cicilan/bayar', [CicilanController::class, 'bayar'])->name('cicilan.bayar');

    Route::resource('cicilan', CicilanController::class);

    // MENU LAPORAN
    Route::get('lap-arus-kas', [ArusKasController::class, 'index'])->name('lap-arus-kas.index');
    Route::get('lap-arus-kas/load', [ArusKasController::class, 'loadData'])->name('lap-arus-kas.load');
});

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
