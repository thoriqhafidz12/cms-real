<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\MasterAnggota;
use Illuminate\Http\Request;
use Illuminate\View\View;


class MasterAnggotaController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterAnggota::class;
        $this->route = 'ms-anggota';
        $this->titlePage = 'Daftar Anggota';
        $this->primaryKey = 'maId';
        $this->table = 'ms_anggota';
        $this->searchColumn = 'maNama';

        $this->rules = [
            'maNoIdentitas' => 'required|string|max:16|unique:ms_anggota,maNoIdentitas'
        ];

        $this->jenisKelamin = [
            ['value' => 'Laki-laki', 'label' => 'Laki-laki'],
            ['value' => 'Perempuan', 'label' => 'Perempuan']
        ];

        $this->form = [
            [
                'name' => 'maNama',
                'label' => 'Nama Anggota',
                'placeholder' => 'Masukkan nama anggota',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'maAlamat',
                'label' => 'Alamat',
                'placeholder' => 'Masukkan alamat anggota',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'maNoTelp',
                'label' => 'No. Telepon',
                'placeholder' => 'Masukkan nomor telepon anggota',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'maNoIdentitas',
                'label' => 'No. Identitas',
                'placeholder' => 'Masukkan nomor identitas anggota',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'maTempatLahir',
                'label' => 'Tempat Lahir',
                'placeholder' => 'Masukkan tempat lahir anggota',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'maTglLahir',
                'label' => 'Tanggal Lahir',
                'placeholder' => '',
                'type' => 'date',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'maJnsKelamin',
                'label' => 'Jenis Kelamin',
                'placeholder' => '-- Pilih Jenis Kelamin --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'options' => $this->jenisKelamin
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Nama Anggota',
                    'field' => 'maNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Alamat',
                    'field' => 'maAlamat',
                    'type' => 'text'
                ],
                [
                    'label' => 'No. Identitas',
                    'field' => 'maNoIdentitas',
                    'type' => 'text'
                ]
            ];
    }

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $query = MasterAnggota::query();

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
            $editData = MasterAnggota::where($this->primaryKey, $editId)->first();
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
            'grid' => $this->grid,
        ], $extra));
    }

    protected function beforeUpdate(array $data, $id): array
    {
        $data['maCreateBy'] = auth()->user()->name ?? '';
        $data['maUpdatedBy'] = auth()->user()->name ?? '';
        return $data;
    }
}
