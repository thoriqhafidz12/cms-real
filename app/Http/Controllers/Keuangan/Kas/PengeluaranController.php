<?php

namespace App\Http\Controllers\Keuangan\Kas;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Kas\PengeluaranModel;
use App\Models\Master\JenisPengeluaranModel;
use Illuminate\View\View;

class PengeluaranController extends BaseController
{
    public function __construct()
    {
        $this->model = PengeluaranModel::class;
        $this->route = 'pengeluaran';
        $this->titlePage = 'Daftar Pengeluaran';
        $this->primaryKey = 'trKelId';
        $this->table = 'tr_pengeluaran';
        $this->searchColumn = ['trKelNoTrans', 'trKelKeterangan'];
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
                'name' => 'trKelTanggal',
                'label' => 'Tanggal',
                'placeholder' => 'Masukkan tanggal',
                'type' => 'date',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'trKelJenisTrans',
                'label' => 'Jenis Transaksi',
                'placeholder' => '-- Cari dan pilih jenis --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.jenis-pengeluaran.search'),
                    'textField' => 'msJnsKelNama',
                    'valueField' => 'msJnsKelId',
                ],
                'exists' => 'ms_jns_keluar,msJnsKelId',
            ],
            [
                'name' => 'trKelNominal',
                'label' => 'Nominal',
                'placeholder' => 'Masukkan nominal',
                'type' => 'angka',
                'col' => 'col-md-12',
                'required' => true
            ],
            [
                'name' => 'trKelKeterangan',
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
                'field' => 'trKelTanggal',
                'type' => 'date'
            ],
            [
                'label' => 'Jenis Transaksi',
                'field' => 'msJnsKelNama',
                'type' => 'text'
            ],
            [
                'label' => 'Nominal',
                'field' => 'trKelNominal',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Keterangan',
                'field' => 'trKelKeterangan',
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
        
        $userId = auth()->user()->id; // Get the authenticated user's ID

        $query = $this->model::query();

        if ($search && $this->searchColumn) {
            $columns = (array) $this->searchColumn;
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->leftjoin('ms_jns_keluar', 'tr_pengeluaran.trKelJenisTrans', '=', 'ms_jns_keluar.msJnsKelId')->orderBy($this->primaryKey, 'desc')->where('trKelUserId', $userId)
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
                'trKelJenisTrans' => optional(
                    JenisPengeluaranModel::find($editData->trKelJenisTrans)
                )->msJnsKelNama,
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
        $data['trKelUserId'] = auth()->user()->id;
        $data['trKelCreatedBy'] = auth()->user()->name;
        $data['trKelUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $data['trKelUpdatedBy'] = auth()->user()->name;

        return $data;
    }
}

