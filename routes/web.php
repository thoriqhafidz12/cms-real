<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Combo\PengajuanforcomboController;
use App\Http\Controllers\GantiPassController;
use App\Http\Controllers\Master\Coa\MasterAkunController;
use App\Http\Controllers\Master\Coa\MasterJenisController;
use App\Http\Controllers\Master\Coa\MasterKelompokController;
use App\Http\Controllers\Master\Coa\MasterObjekController;
use App\Http\Controllers\Master\JenisPinjamanController;
use App\Http\Controllers\Master\JenisSimpananController;
use App\Http\Controllers\Master\MasterAnggotaController;
use App\Http\Controllers\Master\MasterBankkasController;
use App\Http\Controllers\Master\MasterJaminanController;
use App\Http\Controllers\Master\MasterTujuanPinjamanController;
use App\Http\Controllers\Master\MetodePembayaranController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Pinjaman\PencairanPinjamanController;
use App\Http\Controllers\Pinjaman\PengajuanPinjamanController;
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
// checkRole: cek akses menu role user via role_menu — jika tidak ada akses, redirect ke 403
// dashboard ikut dicek
// karena terdaftar di tabel menu (mRoute = 'dashboard').
Route::middleware(['auth', 'checkRole'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalMenus' => Menu::count(),
        ]);
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // API FOR COMBO
    Route::get('api/akun/search', [MasterAkunController::class, 'search'])->name('api.akun.search');
    Route::get('api/kelompok/search', [MasterKelompokController::class, 'search'])->name('api.kelompok.search');
    Route::get('api/objek/search', [MasterObjekController::class, 'search'])->name('api.objek.search');
    Route::get('api/tujuan-pinjaman/search', [MasterTujuanPinjamanController::class, 'search'])->name('api.tujuan-pinjaman.search');
    Route::get('api/anggota/search', [MasterAnggotaController::class, 'search'])->name('api.anggota.search');
    Route::get('api/jaminan/search', [MasterJaminanController::class, 'search'])->name('api.jaminan.search');
    Route::get('api/metode-pembayaran/search', [MetodePembayaranController::class, 'search'])->name('api.metode-pembayaran.search');
    Route::get('api/pengajuan-pinjaman/search', [PengajuanforcomboController::class, 'index'])->name('api.pengajuan-pinjaman.search');

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

    // MASTER
    Route::resource('ms-anggota', MasterAnggotaController::class);
    Route::resource('ms-jns-pinjaman', JenisPinjamanController::class);
    Route::resource('ms-jns-simpanan', JenisSimpananController::class);

    Route::resource('ms-akun', MasterAkunController::class);
    Route::resource('ms-kelompok', MasterKelompokController::class);
    Route::resource('ms-jenis', MasterJenisController::class);
    Route::resource('ms-objek', MasterObjekController::class);
    Route::resource('ms-metode-bayar',  MetodePembayaranController::class);

    Route::resource('ms-bank-kas', MasterBankkasController::class);
    Route::resource('ms-jaminan', MasterJaminanController::class);
    Route::resource('ms-tujuan-pinjaman', MasterTujuanPinjamanController::class);

    // PINJAMAN
    Route::resource('pengajuan-pinjaman', PengajuanPinjamanController::class);
    Route::put('pengajuan-pinjaman/{id}/persetujuan', [PengajuanPinjamanController::class, 'persetujuan'])->name('pengajuan-pinjaman.persetujuan');
    // PENCAIRAN
    Route::resource('pencairan-pinjaman', PencairanPinjamanController::class);
});

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
