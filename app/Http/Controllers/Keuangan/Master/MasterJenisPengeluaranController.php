<?php

namespace App\Http\Controllers\Keuangan\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\JenisPengeluaranModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterJenisPengeluaranController extends BaseController
{
    public function __construct()
    {
        $this->model = JenisPengeluaranModel::class;
        $this->route = 'master-jenis-pengeluaran';
        $this->titlePage = 'Daftar Jenis Pengeluaran';
        $this->primaryKey = 'msJnsKelId';
        $this->table = 'msJnsKeluar';
        $this->searchColumn = ['msJnsKelNama'];

        $this->form = [
            [
                'name' => 'msJnsKelNama',
                'label' => 'Nama Jenis Pengeluaran',
                'placeholder' => 'Masukkan nama jenis pengeluaran',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid = [
            [
                'label' => 'Nama',
                'field' => 'msJnsKelNama',
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
        $data['msJnsKelCreatedBy'] = auth()->user()->name;
        $data['msJnsKelUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $data['msJnsKelUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    /**
     * API search untuk autocomplete select2.
     * GET /api/jenis-pengeluaran/search?search=keyword
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $data = JenisPengeluaranModel::where('msJnsKelNama', 'like', "%{$search}%")
            ->orderBy('msJnsKelNama')
            ->limit(20)
            ->get(['msJnsKelId', 'msJnsKelNama']);

        return response()->json($data);
    }
}
