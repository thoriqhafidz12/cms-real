<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Cek apakah role user yang login punya akses ke route saat ini
     * berdasarkan tabel role_menu (lihat RoleController::updateMenu).
     *
     * Aturan pengecekan:
     * 1. Route tanpa nama / tidak terdaftar di tabel menu → lolos (mis. logout).
     * 2. Role tanpa assignment menu sama sekali → lolos (konsisten dengan
     *    filter sidebar di layouts/app.blade.php).
     * 3. Role dengan assignment → harus punya menu yang cocok dengan route saat ini.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()?->getName();

        // Route tanpa nama (mis. closure '/') → tidak dicek
        if (!$routeName) {
            return $next($request);
        }

        // Cari menu aktif yang mRoute-nya cocok dengan route saat ini.
        // mRoute 'users' cocok dengan route resource 'users.index', 'users.store', dst.
        $matchedMenuIds = Menu::where('mIsActive', 1)
            ->whereNotNull('mRoute')
            ->get(['mId', 'mRoute'])
            ->filter(fn (Menu $menu) => $routeName === $menu->mRoute
                || str_starts_with($routeName, $menu->mRoute . '.'))
            ->pluck('mId');

        // Route tidak terdaftar di tabel menu → tidak diatur oleh permission menu
        if ($matchedMenuIds->isEmpty()) {
            return $next($request);
        }

        // ID menu yang di-assign ke role user (sama seperti filter sidebar)
        $assignedMenuIds = $user->roleRelation?->menus()->pluck('menu.mId') ?? collect();

        // Role tanpa assignment → semua menu dianggap boleh (konsisten dengan sidebar)
        if ($assignedMenuIds->isEmpty()) {
            return $next($request);
        }

        // Role punya assignment → harus ada irisan dengan menu route saat ini
        if ($matchedMenuIds->intersect($assignedMenuIds)->isNotEmpty()) {
            return $next($request);
        }

        abort(403);
    }
}
