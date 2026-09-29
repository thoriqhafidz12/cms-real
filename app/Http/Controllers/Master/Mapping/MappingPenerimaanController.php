<?php

namespace App\Http\Controllers\Master\Mapping;

use App\Http\Controllers\BaseController;
use App\Models\Master\Mapping\MappingPenerimaan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MappingPenerimaanController extends BaseController
{
    public function __construct()
    {
        $this->model = MappingPenerimaan::class;
        $this->route = 'ms-mapping-penerimaan';
        $this->titlePage = 'Mapping Penerimaan';
        $this->primaryKey = 'mapId';
        $this->table = 'mapping_penerimaan';
        $this->searchColumn = ['mapKodeAsal', 'mapNamaAsal'];

        $this->rules = [
            'mapKodeAsal' => 'required|unique:mapping_penerimaan,mapKodeAsal',
        ];

        $this->statusPenagihan = [
            [
                'value' => 1,
                'label' => 'Menggunakan Piutang',
            ],
            [
                'value' => 2,
                'label' => 'Tanpa Menggunakan Piutang',
            ]
        ];

        $this->form = [
            [
                'name' => 'mapKodeAsal',
                'nameValue' => 'mapNamaAsal',
                'label' => 'Kode Asal',
                'placeholder' => '-- Cari dan pilih asal --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ],
            ],
            [
                'name' => 'mapKodeDebet',
                'nameValue' => 'mapNamaDebet',
                'label' => 'Kode Debet',
                'placeholder' => '-- Cari dan pilih debet --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mapKodeKredit',
                'nameValue' => 'mapNamaKredit',
                'label' => 'Kode Kredit',
                'placeholder' => '-- Cari dan pilih kredit --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mapStatusPenagihan',
                'label' => 'Status Penagihan',
                'placeholder' => '-- Pilih Status Penagihan --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'options' => $this->statusPenagihan,
            ],
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Asal',
                    'field' => 'mapKodeAsal',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Asal',
                    'field' => 'mapNamaAsal',
                    'type' => 'text'
                ],
                [
                    'label' => 'Kode Debet',
                    'field' => 'mapKodeDebet',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Debet',
                    'field' => 'mapNamaDebet',
                    'type' => 'text'
                ],
                [
                    'label' => 'Kode Kredit',
                    'field' => 'mapKodeKredit',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Kredit',
                    'field' => 'mapNamaKredit',
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
    // public function search(Request $request): JsonResponse
    // {
    //     $search = $request->get('search');
    //     $fObjek = $request->get('fObjek', []);

    //     $data = $this->model::where('msoNama', 'like', "%{$search}%")
    //         ->orderBy('msoKode')
    //         ->when(!empty($fObjek), function ($query) use ($fObjek) {
    //             $query->where('msoJenisKode', 'LIKE', "%$fObjek%");
    //         })
    //         ->limit(20)
    //         ->get(['msoId', 'msoKode', 'msoNama']);

    //     return response()->json($data->map(function ($item) {
    //         return [
    //             'id' => $item->msoKode,
    //             'text' => $item->msoKode . ' - ' . $item->msoNama,
    //             'hiddenValue' => $item->msoNama,
    //         ];
    //     }));
    // }
}
