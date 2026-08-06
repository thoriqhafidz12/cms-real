<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Tampilkan daftar role.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $roles = Role::when($search, function ($query, $search) {
                return $query->where('rNama', 'like', "%{$search}%");
            })
            ->orderBy('rId', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('roles.index', compact('roles', 'search'));
    }

    /**
     * Form tambah role.
     */
    public function create(): View
    {
        return view('roles.create');
    }

    /**
     * Simpan role baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rNama' => ['required', 'string', 'max:225', 'unique:role,rNama'],
        ]);

        Role::create([
            'rNama'      => $validated['rNama'],
            'rCreatedBy' => auth()->user()->name,
            'rUpdatedBy' => auth()->user()->name,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Form edit role.
     */
    public function edit(string $id): View
    {
        $role = Role::where('rId', $id)->firstOrFail();

        return view('roles.edit', compact('role'));
    }

    /**
     * Update role.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'rNama' => ['required', 'string', 'max:225', "unique:role,rNama,{$id},rId"],
        ]);

        $role = Role::where('rId', $id)->firstOrFail();
        $role->update([
            'rNama'      => $validated['rNama'],
            'rUpdatedBy' => auth()->user()->name,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diupdate.');
    }

    /**
     * Hapus role.
     */
    public function destroy(string $id): RedirectResponse
    {
        $role = Role::where('rId', $id)->firstOrFail();
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Tampilkan form akses menu untuk role.
     */
    public function menu(string $id): View
    {
        $role = Role::where('rId', $id)->firstOrFail();
        $assignedMenuIds = $role->menus()->pluck('menu.mId')->toArray();

        // Ambil semua menu, grouping parent & child
        $allMenus = Menu::where('mIsActive', 1)->orderBy('mOrder')->get();
        $parentMenus = $allMenus->whereNull('mParentId');
        $childMenus = $allMenus->whereNotNull('mParentId')->groupBy('mParentId');

        return view('roles.menu', compact('role', 'parentMenus', 'childMenus', 'assignedMenuIds'));
    }

    /**
     * Simpan akses menu untuk role.
     */
    public function updateMenu(Request $request, string $id): RedirectResponse
    {
        $role = Role::where('rId', $id)->firstOrFail();

        $menuIds = $request->input('menu_ids', []);

        $role->menus()->sync($menuIds);

        return redirect()
            ->route('roles.index')
            ->with('success', "Akses menu untuk role \"{$role->rNama}\" berhasil disimpan.");
    }
}
