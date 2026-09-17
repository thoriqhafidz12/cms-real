<?php

namespace App\Http\Controllers\Master\Coa;

use App\Http\Controllers\BaseController;
use App\Models\Master\Coa\MasterJenis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterJenisController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterJenis::class;
        $this->route = 'ms-jenis';
        $this->titlePage = 'Daftar Jenis';
        $this->primaryKey = 'msjId';
        $this->table = 'ms_jenis';
        $this->searchColumn = 'msjNama';

        $this->rules = [
            'msjKode' => 'required|unique:ms_jenis,msjKode',
        ];

        $this->form = [
            [
                'name' => 'msjAkunKode',
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
                'name' => 'msjKelompokKode',
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
                'name' => 'msjKode',
                'label' => 'Kode Jenis',
                'placeholder' => 'Masukkan kode jenis',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'msjNama',
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
                    'field' => 'msjKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Jenis',
                    'field' => 'msjNama',
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
        $data['msjCreateBy'] = auth()->user()->name ?? '';
        $data['msjUpdatedBy'] = auth()->user()->name ?? '';
        return $data;
    }

    /**
     * API search untuk autocomplete select2.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $data = $this->model::where('msjNama', 'like', "%{$search}%")
            ->orderBy('msjKode')
            ->limit(20)
            ->get(['msjId', 'msjKode', 'msjNama']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->msjKode,
                'text' => $item->msjKode . ' - ' . $item->msjNama
            ];
        }));
    }
}
