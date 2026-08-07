<?php

namespace App\Http\Controllers\Keuangan\Master;

use App\Http\Controllers\BaseController;
use App\Models\JenisPenerimaanModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterJenisPenerimaanController extends BaseController
{
    public function __construct()
    {
        $this->model = JenisPenerimaanModel::class;
        $this->route = 'master-jenis-penerimaan';
        $this->titlePage = 'Daftar Jenis Penerimaan';
        $this->primaryKey = 'msJnsId';
        $this->table = 'msJnsTerima';
        $this->searchColumn = ['msJnsNama'];

        $this->form = [
            [
                'name' => 'msJnsNama',
                'label' => 'Nama Jenis Penerimaan',
                'placeholder' => 'Masukkan nama jenis penerimaan',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid = [
            [
                'label' => 'Nama',
                'field' => 'msJnsNama',
                'type' => 'text'
            ],
        ];
    }

    /**
     * Tampilkan daftar user + form (two-column layout).
     */
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
            $editData = $this->model::find($editId);
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
        ]));
    }

    protected function beforeSave(array $data, $record = null): array
    {
        $data['msJnsCreatedBy'] = auth()->user()->name;
        $data['msJnsUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $data['msJnsUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    /**
     * API search untuk autocomplete select2.
     * GET /api/jenis-penerimaan/search?search=keyword
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $data = JenisPenerimaanModel::where('msJnsNama', 'like', "%{$search}%")
            ->orderBy('msJnsNama')
            ->limit(20)
            ->get(['msJnsId', 'msJnsNama']);

        return response()->json($data);
    }
}
