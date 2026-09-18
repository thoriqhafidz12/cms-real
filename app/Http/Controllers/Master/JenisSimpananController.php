<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\JenisSimpanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisSimpananController extends BaseController
{
    public function __construct()
    {
        $this->model = JenisSimpanan::class;
        $this->route = 'ms-jns-simpanan';
        $this->titlePage = 'Daftar Jenis Simpanan';
        $this->primaryKey = 'mjsJnsSimpananId';
        $this->table = 'ms_jnssimpanan';
        $this->searchColumn = ['mjsKodeSimpanan', 'mjsNamaSimpanan', 'mjsTipeSimpanan'];

        $this->bisaTarik = [
            ['value' => 'Y', 'name' => 'Ya'],
            ['value' => 'N', 'name' => 'Tidak'],
        ];
        
        $this->status = [
            ['value' => 'Active', 'name' => 'Active'],
            ['value' => 'Inactive', 'name' => 'Inactive'],
        ];

        $this->tipeSimpanan = [
            ['value' => 'Pokok', 'name' => 'Pokok'],
            ['value' => 'Wajib', 'name' => 'Wajib'],
            ['value' => 'Sukarela', 'name' => 'Sukarela'],
        ];

        $this->form = [
            [
                'name' => 'mjsKodeSimpanan',
                'label' => 'Kode Simpanan',
                'placeholder' => 'Masukkan kode simpanan',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjsNamaSimpanan',
                'label' => 'Nama Simpanan',
                'placeholder' => 'Masukkan nama simpanan',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjsTipeSimpanan',
                'label' => 'Tipe Simpanan',
                'placeholder' => '-- Pilih tipe simpanan --',
                'type' => 'select',
                'col' => 'col-md-6',
                'required' => true,
                'options' => $this->tipeSimpanan
            ],
            [
                'name' => 'mjsNominalMinimal',
                'label' => 'Nominal Minimal',
                'placeholder' => 'Masukkan nominal minimal',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjsBisaDitarik',
                'label' => 'Bisa Ditarik',
                'placeholder' => '-- Pilih tipe penarikan --',
                'type' => 'select',
                'col' => 'col-md-6',
                'required' => true,
                'options' => $this->bisaTarik
            ],
            [
                'name' => 'mjsAkunGl',
                'label' => 'Akun GL',
                'placeholder' => '-- Cari dan pilih akun GL --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'fill' => ['fObjek' => '3.01'],
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mjsKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'textarea',
                'col' => 'col-md-12',
                'required' => false,
            ],
            [
                'name' => 'mjsStatus',
                'label' => 'Status',
                'placeholder' => '-- Pilih status --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'options' => $this->status
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Simpanan',
                    'field' => 'mjsKodeSimpanan',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Simpanan',
                    'field' => 'mjsNamaSimpanan',
                    'type' => 'text'
                ],
                [
                    'label' => 'Tipe Simpanan',
                    'field' => 'mjsTipeSimpanan',
                    'type' => 'text'
                ],
                [
                    'label' => 'Status',
                    'field' => 'mjsStatus',
                    'type' => 'text'
                ]
            ];
    }
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $query = $this->model::query();

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
            $editData = $this->model::where($this->primaryKey, $editId)->first();
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
        $data['mjsCreateBy'] = auth()->user()->name ?? '';
        $data['mjsUpdateBy'] = auth()->user()->name ?? '';
        return $data;
    }
}
