<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\MasterTujuanPinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterTujuanPinjamanController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterTujuanPinjaman::class;
        $this->route = 'ms-tujuan-pinjaman';
        $this->titlePage = 'Daftar Tujuan Pinjaman';
        $this->primaryKey = 'mstId';
        $this->table = 'ms_tujuanpinjaman';
        $this->searchColumn = 'mstNama';

        $this->rules = [
            'mstKode' => 'required|unique:ms_tujuanpinjaman,mstKode',
        ];
        
        $this->form = [
            [
                'name' => 'mstKode',
                'label' => 'Kode Tujuan',
                'placeholder' => 'Masukkan kode tujuan',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'mstNama',
                'label' => 'Nama Tujuan',
                'placeholder' => 'Masukkan nama tujuan',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Tujuan',
                    'field' => 'mstKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Tujuan',
                    'field' => 'mstNama',
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

    /**
     * API search untuk autocomplete select2.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $data = $this->model::where('mstNama', 'like', "%{$search}%")
            ->orderBy('mstKode')
            ->limit(20)
            ->get(['mstId', 'mstKode', 'mstNama']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->mstKode, // Gunakan mstKode sebagai id
                'text' => $item->mstKode . ' - ' . $item->mstNama,
                'hiddenValue' => $item->mstNama,
            ];
        }));
    }
}
