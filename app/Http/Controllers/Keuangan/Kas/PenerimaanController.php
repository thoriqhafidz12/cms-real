<?php

namespace App\Http\Controllers\Keuangan\Kas;

use App\Http\Controllers\BaseController;
use App\Models\Master\JenisPenerimaanModel;
use App\Models\Kas\PenerimaanModel;
use App\Models\Laporan\LaporanPerfaktualModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenerimaanController extends BaseController
{
    public function __construct()
    {
        $this->model = PenerimaanModel::class;
        $this->modelLaporan = LaporanPerfaktualModel::class;
        $this->route = 'penerimaan';
        $this->titlePage = 'Daftar Penerimaan';
        $this->primaryKey = 'trId';
        $this->table = 'tr_penerimaan';
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
                'exists' => 'ms_jns_terima,msJnsId',
            ],
            [
                'name' => 'trTerimaNominal',
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
                'field' => 'trTerimaNominal',
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

        $items = $query->leftjoin('ms_jns_terima', 'tr_penerimaan.trJenisTrans', '=', 'ms_jns_terima.msJnsId')
            ->where('trTerimaUserId', $userId)
            ->orderBy($this->primaryKey, 'desc')
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
        $data['trTerimaUserId'] = auth()->user()->id;
        $data['trCreatedBy'] = auth()->user()->name;
        $data['trUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    public function afterSave($data): void
    {
        // Simpan ke tabel laporan perfaktual
        $this->modelLaporan::create([
            'rpHeadId' => $data['trId'],
            'rpTanggal' => $data['trTanggal'],
            'rpTerimaNominal' => $data['trTerimaNominal'],
            'rpJenisTrans' => 1, // 1 = Penerimaan
            'rpKeterangan' => $data['trKeterangan'],
            'rpUserId' => $data['trTerimaUserId'],
            'rpCreatedBy' => $data['trCreatedBy'],
            'rpUpdatedBy' => $data['trUpdatedBy'],
        ]);
    }

    protected function beforeUpdate(array $data, $id): array
    {
        $data['trUpdatedBy'] = auth()->user()->name;
        // Update tabel laporan perfaktual
        $laporanRecord = $this->modelLaporan::where('rpJenisTrans', 1)->where('rpHeadId', $id)->first();
        if ($laporanRecord) {
            $laporanRecord->update([
                'rpTanggal' => $data['trTanggal'],
                'rpTerimaNominal' => $data['trTerimaNominal'],
                'rpKeterangan' => $data['trKeterangan'],
                'rpUpdatedBy' => $data['trUpdatedBy'],
            ]);
        }

        return $data;
    }

    public function beforeDelete($id)
    {
        // Hapus dari tabel laporan perfaktual
        $laporanRecord = $this->modelLaporan::where('rpJenisTrans', 1)->where('rpHeadId', $id)->first();
        if ($laporanRecord) {
            $laporanRecord->delete();
        }
    }
}
