<?php

namespace App\Http\Controllers\Keuangan\Kas;

use App\Http\Controllers\BaseController;
use App\Models\JenisPenerimaanModel;
use App\Models\PenerimaanModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenerimaanController extends BaseController
{
    public function __construct()
    {
        $this->model = PenerimaanModel::class;
        $this->route = 'penerimaan';
        $this->titlePage = 'Daftar Penerimaan';
        $this->primaryKey = 'trId';
        $this->table = 'trPenerimaan';
        $this->searchColumn = ['trNoTrans', 'trKeterangan'];
        $this->withRelation = 'roleRelation';

        $this->form = [
            // [
            //     'name' => 'trNoTrans',
            //     'label' => 'Nomor Transaksi',
            //     'placeholder' => 'Masukkan nomor transaksi',
            //     'type' => 'text',
            //     'col' => 'col-md-12',
            //     'required' => true,
            // ],
            [
                'name' => 'trTanggal',
                'label' => 'Tanggal',
                'placeholder' => 'Masukkan tanggal',
                'type' => 'date',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'trJenisTrans',
                'label' => 'Jenis Transaksi',
                'placeholder' => '-- Cari dan pilih jenis --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.jenis-penerimaan.search'),
                    'textField' => 'msJnsNama',
                    'valueField' => 'msJnsId',
                ],
                'exists' => 'msJnsTerima,msJnsId',
            ],
            [
                'name' => 'trNominal',
                'label' => 'Nominal',
                'placeholder' => 'Masukkan nominal',
                'type' => 'angka',
                'col' => 'col-md-12',
                'required' => true
            ],
            [
                'name' => 'trKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => false,
            ],
            // [
            //     'name' => 'role',
            //     'label' => 'Role',
            //     'placeholder' => '-- Pilih Role --',
            //     'type' => 'select',
            //     'col' => 'col-md-12',
            //     'required' => true,
            //     'exists' => 'role,rId',
            //     'options' => [], // filled via extraViewData
            // ],
        ];

        $this->grid = [
            // [
            //     'label' => 'Nomor Transaksi',
            //     'field' => 'trNoTrans',
            //     'type' => 'text'
            // ],
            [
                'label' => 'Tanggal',
                'field' => 'trTanggal',
                'type' => 'date'
            ],
            [
                'label' => 'Jenis Transaksi',
                'field' => 'msJnsNama',
                'type' => 'text'
            ],
            [
                'label' => 'Nominal',
                'field' => 'trNominal',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Keterangan',
                'field' => 'trKeterangan',
                'type' => 'text'
            ]
        ];

        // $this->extraViewData = [
        //     'roles' => fn() => Role::orderBy('rNama')->get(),
        // ];
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

        $items = $query->leftjoin('msJnsTerima', 'trPenerimaan.trJenisTrans', '=', 'msJnsTerima.msJnsId')->orderBy($this->primaryKey, 'desc')
            ->paginate(10)
            ->withQueryString();

        $editData = null;
        if ($editId) {
            $editData = $this->model::find($editId);
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        // Resolve display text untuk autocomplete saat edit
        if ($editData) {
            $extra['autocompleteSelected'] = [
                'trJenisTrans' => optional(
                    JenisPenerimaanModel::find($editData->trJenisTrans)
                )->msJnsNama,
            ];
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
        ], $extra));
    }

    protected function beforeSave(array $data, $record = null): array
    {
        $data['trCreatedBy'] = auth()->user()->name;
        $data['trUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $data['trUpdatedBy'] = auth()->user()->name;

        return $data;
    }
}
