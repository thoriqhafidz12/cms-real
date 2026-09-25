<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\MasterJaminan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterJaminanController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterJaminan::class;
        $this->route = 'ms-jaminan';
        $this->titlePage = 'Daftar Jaminan';
        $this->primaryKey = 'msjId';
        $this->table = 'ms_jaminan';
        $this->searchColumn = 'msjNama';

        $this->rules = [
            'msjKode' => 'required|unique:ms_jaminan,msjKode',
        ];
        
        $this->form = [
            [
                'name' => 'msjKode',
                'label' => 'Kode Jaminan',
                'placeholder' => 'Masukkan kode jaminan',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'msjNama',
                'label' => 'Nama Jaminan',
                'placeholder' => 'Masukkan nama jaminan',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Jaminan',
                    'field' => 'msjKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Jaminan',
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
                'id' => $item->msjKode, // Gunakan msjKode sebagai id
                'text' => $item->msjKode . ' - ' . $item->msjNama,
                'hiddenValue' => $item->msjNama,
            ];
        }));
    }
}
