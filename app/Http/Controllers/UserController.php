<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->model = User::class;
        $this->route = 'users';
        $this->titlePage = 'Daftar User';
        $this->primaryKey = 'id';
        $this->table = 'users';
        $this->searchColumn = ['name', 'email'];
        $this->withRelation = 'roleRelation';

        $this->form = [
            [
                'name' => 'name',
                'label' => 'Nama Lengkap',
                'placeholder' => 'Masukkan nama lengkap',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'placeholder' => 'Masukkan email',
                'type' => 'email',
                'col' => 'col-md-12',
                'required' => true,
                'unique' => 'users,email',
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'placeholder' => 'Minimal 4 karakter',
                'type' => 'password',
                'col' => 'col-md-12',
                'required' => true, // required on create, handled manually on update
            ],
            [
                'name' => 'role',
                'label' => 'Role',
                'placeholder' => '-- Pilih Role --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'exists' => 'role,rId',
                'options' => [], // filled via extraViewData
            ],
        ];

        $this->grid = [
            [
                'label' => 'Nama',
                'field' => 'name',
                'type' => 'text'
            ],
            [
                'label' => 'Email',
                'field' => 'email',
                'type' => 'text'
            ],
            [
                'label' => 'Role',
                'field' => 'rNama',
                'type' => 'text'
            ]
        ];

        $this->extraViewData = [
            'roles' => fn() => Role::orderBy('rNama')->get(),
        ];
    }

    /**
     * Tampilkan daftar user + form (two-column layout).
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $query = User::leftJoin('role', 'users.role', '=', 'role.rId')
            ->select('users.*', 'role.rNama');

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
            $editData = User::find($editId);
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        return view('master', array_merge([
            'items' => $items,
            'search' => $search,
            'editData' => $editData,
            'form' => $this->form,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'titlePage' => $this->titlePage,
            'grid' => $this->grid
        ], $extra));
    }

    /**
     * Override update: password optional pada edit.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $record = User::findOrFail($id);

        // Build rules tanpa required untuk password
        $rules = $this->buildValidationRules($id);

        // Password optional saat edit
        if (!$request->filled('password')) {
            unset($rules['password']);
        } else {
            $rules['password'] = ['string', 'min:4'];
        }

        $validated = $request->validate($rules);

        // Hash password jika diisi
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $record->update($this->beforeUpdate($validated, $record));

        return redirect()
            ->route($this->route . '.index')
            ->with('success', $this->titlePage . ' berhasil diupdate.');
    }

    protected function beforeSave(array $data, $record = null): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
