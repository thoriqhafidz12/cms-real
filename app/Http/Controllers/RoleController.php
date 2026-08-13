<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends BaseController
{
    public function __construct()
    {
        $this->model = Role::class;
        $this->route = 'roles';
        $this->titlePage = 'Daftar Role';
        $this->primaryKey = 'rId';
        $this->table = 'role';
        $this->searchColumn = 'rNama';

        $this->form = [
            [
                'name' => 'rNama',
                'label' => 'Nama Role',
                'placeholder' => 'Masukkan nama role',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
                'unique' => 'role,rNama',
            ],
        ];
    }

    /**
     * Tampilkan daftar role + form (two-column layout).
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $query = Role::query();

        if ($search && $this->searchColumn) {
            $columns = (array) $this->searchColumn;
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->orderBy($this->primaryKey, 'desc')
            ->paginate(10)
            ->withQueryString();

        $editData = null;
        if ($editId) {
            $editData = Role::where($this->primaryKey, $editId)->first();
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        return view('roles.index', array_merge([
            'items' => $items,
            'search' => $search,
            'editData' => $editData,
            'form' => $this->form,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'titlePage' => $this->titlePage,
        ], $extra));
    }

    protected function beforeSave(array $data, $record = null): array
    {
        $data['rCreatedBy'] = auth()->user()->name;
        $data['rUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $data['rUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    // ── Role Menu Management (tidak pakai dua kolom) ─────────────────────

    /**
     * Tampilkan form akses menu untuk role.
     */
    public function menu(string $id): View
    {
        $role = Role::where('rId', $id)->firstOrFail();
        $assignedMenuIds = $role->menus()->pluck('menu.mId')->toArray();

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

    /**
     * Hapus role — dicegah jika masih dipakai user, dan bersihkan role_menu
     * agar tidak ada data yatim (role_menu menunjuk role yang sudah dihapus).
     */
    public function destroy(string $id): RedirectResponse
    {
        $role = Role::where('rId', $id)->firstOrFail();

        if (User::where('role', $id)->exists()) {
            return redirect()
                ->route('roles.index')
                ->with('error', "Role \"{$role->rNama}\" tidak bisa dihapus karena masih dipakai oleh user.");
        }

        $role->menus()->detach();
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', "Role \"{$role->rNama}\" berhasil dihapus.");
    }
}
