<?php

namespace App\Http\Controllers\Keuangan\Kas;

use App\Http\Controllers\BaseController;
use App\Models\Kas\CicilanDetailModel;
use App\Models\Kas\CicilanModel;
use App\Models\Kas\PengeluaranModel;
use App\Models\Laporan\LaporanPerfaktualModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CicilanController extends BaseController
{
    protected array $bayarForm = [];

    public function __construct()
    {
        $this->model = CicilanModel::class;
        $this->modelLaporan = LaporanPerfaktualModel::class;
        $this->modelPengeluaran = PengeluaranModel::class;
        $this->route = 'cicilan';
        $this->titlePage = 'Daftar Cicilan';
        $this->primaryKey = 'trcId';
        $this->table = 'tr_cicilan';
        $this->searchColumn = ['trcNoTrans', 'trcKeterangan'];
        $this->withRelation = 'roleRelation';

        $this->form = [
            [
                'name' => 'trcTanggal',
                'label' => 'Tanggal Pinjam',
                'placeholder' => 'Masukkan tanggal',
                'type' => 'date',
                'col' => 'col-md-4',
                'required' => true,
            ],
            // [
            //     'name' => 'trJenisTrans',
            //     'label' => 'Jenis Transaksi',
            //     'placeholder' => '-- Cari dan pilih jenis --',
            //     'type' => 'autocomplete',
            //     'col' => 'col-md-4',
            //     'required' => true,
            //     'autocomplete' => [
            //         'url' => route('api.jenis-penerimaan.search'),
            //         'textField' => 'msJnsNama',
            //         'valueField' => 'msJnsId',
            //     ],
            //     'exists' => 'ms_jns_terima,msJnsId',
            // ],
            [
                'name' => 'trcNominalPokok',
                'label' => 'Jumlah Pinjaman',
                'placeholder' => 'Masukkan nominal',
                'type' => 'angka',
                'col' => 'col-md-4',
                'required' => true
            ],
            [
                'name' => 'trcPokokBayar',
                'label' => 'Jumlah Bayar Perbulan',
                'placeholder' => 'Masukkan nominal',
                'type' => 'angka',
                'col' => 'col-md-4',
                'required' => true
            ],
            [
                'name' => 'trcTenor',
                'label' => 'Tenor',
                'placeholder' => 'Dalam Hitungan Bulan',
                'type' => 'number',
                'col' => 'col-md-4',
                'required' => true,
                ''
            ],
            [
                'name' => 'trcJatuhTempo',
                'label' => 'Jatuh Tempo',
                'placeholder' => 'Masukkan jatuh tempo',
                'type' => 'date',
                'col' => 'col-md-4',
                'required' => true
            ],
            [
                'name' => 'trcKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'textarea',
                'col' => 'col-md-4',
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
            [
                'label' => 'Tanggal Pinjam',
                'field' => 'trcTanggal',
                'type' => 'date'
            ],
            [
                'label' => 'Jatuh Tempo',
                'field' => 'trcJatuhTempo',
                'type' => 'date'
            ],
            [
                'label' => 'Pinjaman',
                'field' => 'trcNominalPokok',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Bayar Perbulan',
                'field' => 'trcPokokBayar',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Terbayar',
                'field' => 'trcTerbayar',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Keterangan',
                'field' => 'trcKeterangan',
                'type' => 'text'
            ]
        ];

        // $this->extraViewData = [
        //     'roles' => fn() => Role::orderBy('rNama')->get(),
        // ];

        $this->bayarForm = [
            [
                'name' => 'trcdHeadId',
                'label' => 'ID Cicilan',
                'type' => 'hidden',
            ],
            [
                'name' => 'trcdTanggal',
                'label' => 'Tanggal Bayar',
                'placeholder' => 'Masukkan tanggal',
                'type' => 'date',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'trcdNominal',
                'label' => 'Nominal Bayar',
                'placeholder' => 'Masukkan nominal pembayaran',
                'type' => 'angka',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'trcdKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan (opsional)',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => false,
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

        $items = $query->where('trcUserId', $userId)
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

        // // Resolve display text untuk autocomplete saat edit
        // if ($editData) {
        //     $extra['autocompleteSelected'] = [
        //         'trJenisTrans' => optional(
        //             JenisPenerimaanModel::find($editData->trJenisTrans)
        //         )->msJnsNama,
        //     ];
        // }

        return view('kas.cicilan', array_merge([
            'items' => $items,
            'search' => $search,
            'editData' => $editData,
            'form' => $this->form,
            'bayarForm' => $this->bayarForm,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'titlePage' => $this->titlePage,
            'grid' => $this->grid
        ], $extra));
    }

    protected function beforeSave(array $data, $record = null): array
    {
        $data['trcNoTrans'] = $this->getCicilanTrans();
        $data['trcUserId'] = auth()->user()->id;
        $data['trcCreatedBy'] = auth()->user()->name;
        $data['trcUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    // public function afterSave($data): void
    // {
    //     // Simpan ke tabel laporan perfaktual
    //     $this->modelLaporan::create([
    //         'rpHeadId' => $data['trId'],
    //         'rpTanggal' => $data['trcTanggal'],
    //         'rpTerimaNominal' => $data['trcNominalPokok'],
    //         'rpJenisTrans' => 1, // 1 = Penerimaan
    //         'rpKeterangan' => $data['trcKeterangan'],
    //         'rpUserId' => $data['trcUserId'],
    //         'rpCreatedBy' => $data['trCreatedBy'],
    //         'rpUpdatedBy' => $data['trUpdatedBy'],
    //     ]);
    // }

    protected function beforeUpdate(array $data, $id): array
    {
        $data['trUpdatedBy'] = auth()->user()->name;
        // Update tabel laporan perfaktual
        // $laporanRecord = $this->modelLaporan::where('rpJenisTrans', 1)->where('rpHeadId', $id)->first();
        // if ($laporanRecord) {
        //     $laporanRecord->update([
        //         'rpTanggal' => $data['trcTanggal'],
        //         'rpTerimaNominal' => $data['trcNominal'],
        //         'rpKeterangan' => $data['trKeterangan'],
        //         'rpUpdatedBy' => $data['trUpdatedBy'],
        //     ]);
        // }

        return $data;
    }

    // public function beforeDelete($id)
    // {
    //     // Hapus dari tabel laporan perfaktual
    //     $laporanRecord = $this->modelLaporan::where('rpJenisTrans', 1)->where('rpHeadId', $id)->first();
    //     if ($laporanRecord) {
    //         $laporanRecord->delete();
    //     }
    // }

    public function getCicilanTrans()
    {
        $getLast = $this->model::count();
        $newNumber = $getLast + 1;
        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        $transNumber = 'CIC-' . date('Y') . '-' . $formattedNumber;
        return $transNumber;
    }

    /**
     * Ambil data cicilan by ID (untuk modal bayar).
     */
    public function getCicilan($id): JsonResponse
    {
        $cicilan = $this->model::find($id);

        if (!$cicilan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Hitung sisa yang harus dibayar
        $totalDibayar = CicilanDetailModel::where('trcdHeadId', $id)->sum('trcdNominal');
        $sisa = $cicilan->trcNominalPokok - $totalDibayar;

        return response()->json([
            'success' => true,
            'data' => [
                'trcId' => $cicilan->trcId,
                'trcNoTrans' => $cicilan->trcNoTrans,
                'trcPokokBayar' => $cicilan->trcPokokBayar,
                'trcNominalPokok' => $cicilan->trcNominalPokok,
                'trcKeterangan' => $cicilan->trcKeterangan,
                'totalDibayar' => $totalDibayar,
                'sisa' => $sisa,
            ],
        ]);
    }

    /**
     * Ambil data detail cicilan by head ID (untuk collapse table).
     */
    public function getDetail($id): JsonResponse
    {
        $details = CicilanDetailModel::where('trcdHeadId', $id)
            ->orderBy('trcdTanggal', 'desc')
            ->get();

        foreach ($details as $detail) {
            $detail->trcdTanggal = date('d/m/Y', strtotime($detail->trcdTanggal));
        }

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    /**
     * Proses pembayaran cicilan (simpan ke tr_cicilan_detail).
     */
    public function bayar(Request $request): RedirectResponse
    {
        $request->validate([
            'trcdHeadId' => 'required|integer|exists:tr_cicilan,trcId',
            'trcdTanggal' => 'required|date',
            'trcdNominal' => 'required|numeric|min:1',
            'trcdKeterangan' => 'nullable|string|max:225',
        ]);

        $det = CicilanDetailModel::create([
            'trcdHeadId' => $request->trcdHeadId,
            'trcdTanggal' => $request->trcdTanggal,
            'trcdNominal' => $request->trcdNominal,
            'trcdKeterangan' => $request->trcdKeterangan,
            'trcdCreatedAt' => now(),
            'trcdCreatedBy' => auth()->user()->name,
            'trcdUpdatedAt' => now(),
            'trcdUpdatedBy' => auth()->user()->name,
        ]);

        CicilanModel::where('trcId', $request->trcdHeadId)->update([
            'trcTerbayar' => $request->trcdNominal + CicilanModel::where('trcId', $request->trcdHeadId)->value('trcTerbayar'),
            'trcUpdatedAt' => now(),
            'trcUpdatedBy' => auth()->user()->name,
        ]);

        $this->afterBayar($det->toArray());

        return redirect()
            ->route($this->route . '.index')
            ->with('success', 'Pembayaran cicilan berhasil disimpan.');
    }

    public function destroyBayar(string $id): JsonResponse
    {
        $detail = CicilanDetailModel::find($id);

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan.',
            ], 404);
        }

        $headId = $detail->trcdHeadId;
        $nominal = $detail->trcdNominal;

        // Kurangi jumlah terbayar di tabel tr_cicilan
        $cicilan = CicilanModel::find($headId);
        $nTotal = $cicilan->trcTerbayar - $nominal;

        if ($cicilan) {
            $cicilan->update([
                'trcTerbayar' => $nTotal,
                'trcUpdatedAt' => now(),
                'trcUpdatedBy' => auth()->user()->name,
            ]);
        }

        $this->modelLaporan::where('rpJenisTrans', 2)
            ->where('rpKeterangan', 'BYR-CIC-' . $detail->trcdHeadId)
            ->delete();
            
        $this->modelPengeluaran::where('trKelJenisTrans', 7)
            ->where('trKelKeterangan', 'BYR-CIC-' . $detail->trcdHeadId)
            ->delete();

        $detail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran cicilan berhasil dihapus.',
        ]);
    }

    public function afterBayar($data): void
    {
        $keluar = $this->modelPengeluaran::create([
            'trKelTanggal' => $data['trcdTanggal'],
            'trKelNominal' => $data['trcdNominal'],
            'trKelJenisTrans' => 7, // 7 = Pembayaran Cicilan
            'trKelKeterangan' => 'BYR-CIC-' . ($data['trcdHeadId'] ?? ''),
            'trKelUserId' => auth()->user()->id,
            'trKelCreatedBy' => auth()->user()->name,
            'trKelUpdatedBy' => auth()->user()->name,
        ]);
        // Simpan ke tabel laporan perfaktual
        $this->modelLaporan::create([
            'rpHeadId' => $keluar->trKelId,
            'rpTanggal' => $data['trcdTanggal'],
            'rpKeluarNominal' => $data['trcdNominal'],
            'rpJenisTrans' => 2, // 2 = Pengeluaran
            'rpKeterangan' => 'BYR-CIC-' . ($data['trcdHeadId'] ?? ''),
            'rpUserId' => auth()->user()->id,
            'rpCreatedBy' => $data['trcdCreatedBy'],
            'rpUpdatedBy' => $data['trcdUpdatedBy'],
        ]);
    }

}
