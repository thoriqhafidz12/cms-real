<?php

namespace App\Http\Controllers\Master\Coa;

use App\Http\Controllers\BaseController;
use App\Models\Master\Coa\MasterKelompok;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterKelompokController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterKelompok::class;
        $this->route = 'ms-kelompok';
        $this->titlePage = 'Daftar Kelompok';
        $this->primaryKey = 'mskId';
        $this->table = 'ms_kelompok';
        $this->searchColumn = 'mskNama';

        $this->form = [
            [
                'name' => 'mskAkunKode',
                'label' => 'Kode Akun',
                'placeholder' => '-- Cari dan pilih akun --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.akun.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ],
                // Value disimpan = msaKode (string), bukan msaId (integer)
                'rules' => ['string', 'max:20'],
                'exists' => 'ms_akun,msaKode',
            ],
            [
                'name' => 'mskNama',
                'label' => 'Nama Kelompok',
                'placeholder' => 'Masukkan nama kelompok',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'mskKode',
                'label' => 'Kode Kelompok',
                'placeholder' => 'Masukkan kode kelompok',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Kelompok',
                    'field' => 'mskKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Kelompok',
                    'field' => 'mskNama',
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
        $data['mskCreateBy'] = auth()->user()->name ?? '';
        $data['mskUpdatedBy'] = auth()->user()->name ?? '';
        return $data;
    }
}
