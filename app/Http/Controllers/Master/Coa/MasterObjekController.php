<?php

namespace App\Http\Controllers\Master\Coa;

use App\Http\Controllers\BaseController;
use App\Models\Master\Coa\MasterObjek;
use Illuminate\Http\Request;
use Illuminate\View\View;


class MasterObjekController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterObjek::class;
        $this->route = 'ms-objek';
        $this->titlePage = 'Daftar Objek';
        $this->primaryKey = 'msoId';
        $this->table = 'ms_objek';
        $this->searchColumn = 'msoNama';

        $this->rules = [
            'msoKode' => 'required|unique:ms_objek,msoKode',
        ];

        $this->form = [
            [
                'name' => 'msoAkunKode',
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
                'rules' => ['string', 'max:20'],
                'exists' => 'ms_akun,msaKode',
            ],
            [
                'name' => 'msoKelompokKode',
                'label' => 'Kode Kelompok',
                'placeholder' => '-- Cari dan pilih kelompok --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.kelompok.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ],
                'rules' => ['string', 'max:20'],
                'exists' => 'ms_kelompok,mskKode',
            ],
            [
                'name' => 'msoJenisKode',
                'label' => 'Kode Jenis',
                'placeholder' => '-- Cari dan pilih jenis --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.jenis.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ],
                'rules' => ['string', 'max:20'],
                'exists' => 'ms_jenis,msjKode',
            ],
            [
                'name' => 'msoJenisKode',
                'label' => 'Kode Jenis',
                'placeholder' => 'Masukkan kode jenis',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'msoNama',
                'label' => 'Nama Jenis',
                'placeholder' => 'Masukkan nama jenis',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Jenis',
                    'field' => 'msoJenisKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Jenis',
                    'field' => 'msoNama',
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
        $data['msoCreateBy'] = auth()->user()->name ?? '';
        $data['msoUpdatedBy'] = auth()->user()->name ?? '';
        return $data;
    }
}
