<?php

namespace App\Http\Controllers\Pinjaman;

use App\Http\Controllers\BaseController;
use App\Models\Pinjaman\PengajuanPinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PengajuanPinjamanController extends BaseController
{
    protected array $formPersetujuan = [];
    protected array $gridPersetujuan = [];

    public function __construct()
    {
        $this->model = PengajuanPinjaman::class;
        $this->route = 'pengajuan-pinjaman';
        $this->titlePage = 'Daftar Pengajuan Pinjaman';
        $this->primaryKey = 'tpId';
        $this->table = 'tr_pengajuan';
        $this->searchColumn = ['tpKode', 'tpAnggotaNama', 'tpJJaminanNama', 'tpStatus'];

        // ========================== PENGAJUAN ==========================
        $this->form = [
            [
                'name' => 'tpKode',
                'label' => 'Kode Pengajuan',
                'placeholder' => 'Masukkan kode pengajuan',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tpTanggalPinjam',
                'label' => 'Tanggal Pinjam',
                'placeholder' => 'Pilih tanggal pinjam',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tpAnggotaId',
                'nameValue' => 'tpAnggotaNama',
                'label' => 'Nama Anggota',
                'placeholder' => '-- Cari dan pilih anggota --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.anggota.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'tpJaminanId',
                'nameValue' => 'tpJaminanNama',
                'label' => 'Jaminan',
                'placeholder' => '-- Cari dan pilih jaminan --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.jaminan.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'tpTujuanId',
                'nameValue' => 'tpTujuanNama',
                'label' => 'Tujuan',
                'placeholder' => '-- Cari dan pilih tujuan --',
                'type' => 'autocomplete',
                'col' => 'col-md-12',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.tujuan-pinjaman.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'tpJumlahPinjam',
                'label' => 'Jumlah Pinjam',
                'placeholder' => 'Masukkan jumlah pinjam',
                'type' => 'angka',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'tpJumlahAngsuranBulan',
                'label' => 'Jumlah Angsuran (Bulan)',
                'placeholder' => 'Masukkan jumlah angsuran dalam bulan',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tpBunga',
                'label' => 'Bunga (%)',
                'placeholder' => 'Masukkan persentase bunga pinjaman',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tpKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan tambahan (opsional)',
                'type' => 'textarea',
                'col' => 'col-md-12',
                'required' => false,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Tanggal',
                    'field' => 'tpTanggalPinjam',
                    'type' => 'date'
                ],
                [
                    'label' => 'Nama Peminjam',
                    'field' => 'tpAnggotaNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Jumlah Pinjam',
                    'field' => 'tpJumlahPinjam',
                    'type' => 'angka'
                ],
                [
                    'label' => 'Bunga (%)',
                    'field' => 'tpBunga',
                    'type' => 'angka'
                ],
                [
                    'label' => 'Status',
                    'field' => 'tpStatus',
                    'type' => 'text'
                ]
            ];

        // ========================== PERSETUJUAN ==========================
        $this->formPersetujuan = [
            [
                'name' => 'tpTenorDisetuji',
                'label' => 'Tenor Disetujui',
                'placeholder' => 'Masukkan tenor pinjaman yang disetujui',
                'type' => 'number',
                'col' => 'col-md-12',
                'required' => true,
                'readonly' => false,
            ],
            [
                'name' => 'tpNilaiDisetuji',
                'label' => 'Nilai Disetujui',
                'placeholder' => 'Masukkan nilai pinjaman yang disetujui',
                'type' => 'angka',
                'col' => 'col-md-12',
                'required' => true,
                'readonly' => false,
            ],
            [
                'name' => 'tpStatus',
                'label' => 'Status Persetujuan',
                'placeholder' => '-- Pilih status persetujuan --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'options' => [
                    ['value' => '0', 'label' => 'Pending'],
                    ['value' => '1', 'label' => 'Disetujui'],
                    ['value' => '2', 'label' => 'Ditolak']
                ],
            ],
            [
                'name' => 'tpKeterangan',
                'label' => 'Keterangan Persetujuan',
                'placeholder' => 'Masukkan catatan persetujuan (opsional)',
                'type' => 'textarea',
                'col' => 'col-md-12',
                'required' => false,
            ]
        ];

        $this->gridPersetujuan =
            [
                [
                    'label' => 'Tanggal',
                    'field' => 'tpTanggalPinjam',
                    'type' => 'date'
                ],
                [
                    'label' => 'Kode Pengajuan',
                    'field' => 'tpKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Peminjam',
                    'field' => 'tpAnggotaNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Jaminan',
                    'field' => 'tpJaminanNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Pengajuan Pinjam',
                    'field' => 'tpJumlahPinjam',
                    'type' => 'rupiah'
                ],
                [
                    'label' => 'Bunga (%)',
                    'field' => 'tpBunga',
                    'type' => 'angka'
                ]
            ];
    }
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        // ========================== PENGAJUAN ==========================
        $query = $this->model::query()->select(
            'tpTanggalPinjam',
            'tpAnggotaNama',
            'tpJumlahPinjam',
            'tpBunga',
            $this->primaryKey,
            DB::raw(" CASE 
                WHEN tpStatus = '0' THEN 'Pending'
                WHEN tpStatus = '1' THEN 'Disetujui'
                WHEN tpStatus = '2' THEN 'Ditolak'
                WHEN tpStatus = '3' THEN 'Dibatalkan'
                ELSE 'Unknown'
            END 
            AS tpStatus")
        );

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

        // ========================== PERSETUJUAN ==========================
        $countPersetujuan = 0;
        $searchPersetujuan = $request->get('search_persetujuan');
        $setujuiId = $request->get('setujui');

        // $queryPersetujuan = $this->model::query()->where('tpStatus', '0');
        $queryPersetujuan = $this->model::query();
        $countPersetujuan = (clone $queryPersetujuan)
            ->where('tpStatus', 0)
            ->count();

        if ($searchPersetujuan && $this->searchColumn) {
            $columns = (array) $this->searchColumn;
            $queryPersetujuan->where(function ($q) use ($columns, $searchPersetujuan) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$searchPersetujuan}%");
                }
            });
        }

        $itemsPersetujuan = $queryPersetujuan->orderBy($this->primaryKey, 'desc')
            ->paginate(10)
            ->withQueryString();

        $dataPersetujuan = null;
        if ($setujuiId) {
            $dataPersetujuan = $this->model::where($this->primaryKey, $setujuiId)->first();
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        return view('pinjaman.index', array_merge([
            'items' => $items,
            'search' => $search,
            'editData' => $editData,
            'form' => $this->form,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'titlePage' => $this->titlePage,
            'grid' => $this->grid,
            // ========================== PERSETUJUAN ==========================
            'itemsPersetujuan' => $itemsPersetujuan,
            'searchPersetujuan' => $searchPersetujuan,
            'dataPersetujuan' => $dataPersetujuan,
            'formPersetujuan' => $this->formPersetujuan,
            'gridPersetujuan' => $this->gridPersetujuan,
            'countPersetujuan' => $countPersetujuan,
        ], $extra));
    }

    public function persetujuan(Request $request, string $id): RedirectResponse
    {
        $record = $this->model::where($this->primaryKey, $id)->firstOrFail();

        $validated = $request->validate([
            'tpTenorDisetuji' => 'required|integer|min:1',
            'tpNilaiDisetuji' => 'required|numeric|min:0',
            'tpStatus' => 'required|in:0,1,2',
            'tpKeterangan' => 'nullable|string|max:225',
        ]);

        $record->update([
            'tpTenorDisetuji' => $validated['tpTenorDisetuji'],
            'tpNilaiDisetuji' => $validated['tpNilaiDisetuji'],
            'tpStatus' => $validated['tpStatus'],
            'tpKeterangan' => $validated['tpKeterangan'] ?? $record->tpKeterangan,
            $this->model::UPDATED_BY => auth()->user()->name,
            $this->model::UPDATED_AT => now(),
        ]);

        $label = $validated['tpStatus'] == '1' ? 'disetujui' : 'ditolak';

        return redirect()
            ->route($this->route . '.index')
            ->with('success', 'Pengajuan ' . $record->tpKode . ' berhasil ' . $label . '.');
    }
}
