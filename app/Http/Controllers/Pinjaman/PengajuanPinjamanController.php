<?php

namespace App\Http\Controllers\Pinjaman;

use App\Http\Controllers\BaseController;
use App\Models\Pinjaman\PengajuanPinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanPinjamanController extends BaseController
{
    public function __construct()
    {
        $this->model = PengajuanPinjaman::class;
        $this->route = 'pengajuan-pinjaman';
        $this->titlePage = 'Daftar Pengajuan Pinjaman';
        $this->primaryKey = 'tpId';
        $this->table = 'tr_pengajuan';
        $this->searchColumn = ['tpKode', 'tpAnggotaNama', 'tpJJaminanNama', 'tpStatus'];

        $this->status = [
            ['value' => 'Active', 'name' => 'Active'],
            ['value' => 'Inactive', 'name' => 'Inactive'],
        ];

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
                'col' => 'col-md-6',
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
            ],
            [
                'name' => 'tpStatus',
                'label' => 'Status',
                'placeholder' => '-- Pilih status --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'options' => $this->status
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Tanggal',
                    'field' => 'tpTanggalPinjam',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Peminjam',
                    'field' => 'tpAnggotaNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Jumlah Pinjam',
                    'field' => 'tpJumlahPinjam',
                    'type' => 'text'
                ],
                [
                    'label' => 'Bunga (%)',
                    'field' => 'tpBunga',
                    'type' => 'text'
                ],
                [
                    'label' => 'Status',
                    'field' => 'tpStatus',
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
}
