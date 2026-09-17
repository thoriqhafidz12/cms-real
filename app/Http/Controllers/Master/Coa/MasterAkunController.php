<?php

namespace App\Http\Controllers\Master\Coa;

use App\Http\Controllers\BaseController;
use App\Models\Master\Coa\MasterAkun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterAkunController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterAkun::class;
        $this->route = 'ms-akun';
        $this->titlePage = 'Daftar Akun';
        $this->primaryKey = 'msaId';
        $this->table = 'ms_akun';
        $this->searchColumn = 'msaNama';

        $this->form = [
            [
                'name' => 'msaKode',
                'label' => 'Kode Akun',
                'placeholder' => 'Masukkan kode akun',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'msaNama',
                'label' => 'Nama Akun',
                'placeholder' => 'Masukkan nama akun',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Akun',
                    'field' => 'msaKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Akun',
                    'field' => 'msaNama',
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
        $data['msaCreateBy'] = auth()->user()->name ?? '';
        $data['msaUpdatedBy'] = auth()->user()->name ?? '';
        return $data;
    }

    /**
     * API search untuk autocomplete select2.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $data = $this->model::where('msaNama', 'like', "%{$search}%")
            ->orderBy('msaKode')
            ->limit(20)
            ->get(['msaId', 'msaKode', 'msaNama']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->msaKode, // Gunakan msaKode sebagai id
                'text' => $item->msaKode . ' - ' . $item->msaNama
            ];
        }));
    }
}
