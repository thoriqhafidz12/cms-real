<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends BaseController
{
    public function __construct()
    {
        $this->model = Menu::class;
        $this->route = 'menus';
        $this->titlePage = 'Daftar Menu';
        $this->primaryKey = 'mId';
        $this->table = 'menu';
        $this->searchColumn = 'mNama';

        $this->form = [
            [
                'name' => 'mNama',
                'label' => 'Nama Menu',
                'placeholder' => 'Masukkan nama menu',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
                'unique' => 'menu,mNama',
            ],
            [
                'name' => 'mRoute',
                'label' => 'Route Prefix',
                'placeholder' => 'Masukkan prefix route (e.g. users)',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => false,
            ],
            [
                'name' => 'mIcon',
                'label' => 'Icon (Font Awesome)',
                'placeholder' => 'e.g. fa-users, fa-cog',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => false,
            ],
            [
                'name' => 'mOrder',
                'label' => 'Urutan',
                'placeholder' => 'Masukkan urutan',
                'type' => 'number',
                'col' => 'col-md-12',
                'required' => false,
            ],
            [
                'name' => 'mParentId',
                'label' => 'Parent Menu',
                'placeholder' => '-- Pilih Parent --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => false,
                'options' => [], // diisi dynamic via extraViewData
            ],
        ];

        $this->extraViewData = [
            'parentMenus' => fn() => Menu::whereNull('mParentId')
                ->where('mIsActive', 1)
                ->orderBy('mOrder')
                ->get(),
        ];
    }

    /**
     * Tampilkan daftar menu + form (two-column layout).
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $query = Menu::query();

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
            $editData = Menu::where($this->primaryKey, $editId)->first();
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        return view('menus.index', array_merge([
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
        $data['mCreatedBy'] = auth()->user()->name;
        $data['mUpdatedBy'] = auth()->user()->name;
        $data['mIsActive'] = 1;

        if (empty($data['mParentId'])) {
            $data['mParentId'] = null;
        }

        return $data;
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $data['mUpdatedBy'] = auth()->user()->name;

        if (empty($data['mParentId'])) {
            $data['mParentId'] = null;
        }

        return $data;
    }
}
