<?php

namespace App\Http\Controllers\Pinjaman;

use App\Http\Controllers\BaseController;
use App\Models\Pinjaman\PengajuanPinjaman;
use App\Models\Pinjaman\Pinjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PencairanPinjamanController extends BaseController
{

    public function __construct()
    {
        $this->model = Pinjaman::class;
        $this->primaryKey = 'trpjId';
        $this->searchColumn = ['trpNoPinjaman', 'trpAnggotaNama', 'trpStatusPinjaman'];
        $this->route = 'pencairan-pinjaman';
        $this->titlePage = 'Daftar Pencairan Pinjaman';
        $this->table = 'tr_pinjaman';

        $this->form = [
            [
                'name' => 'trpNoPinjaman',
                'label' => 'Nomor Pengajuan',
                'placeholder' => 'Masukkan nomor pengajuan',
                'type' => 'text',
                'col' => 'col-md-4',
                'required' => true,
            ],
            [
                'name' => 'trpTanggalCair',
                'label' => 'Tanggal Pencairan',
                'placeholder' => 'Pilih tanggal pencairan',
                'type' => 'date',
                'col' => 'col-md-4',
                'required' => true,
            ],
            [
                'name' => 'trpPengajuanId',
                'label' => 'Nomor Pengajuan',
                'placeholder' => '-- Cari dan pilih pengajuan --',
                'type' => 'autocomplete',
                'col' => 'col-md-4',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.pengajuan-pinjaman.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'trpAnggotaId',
                'label' => 'ID Anggota',
                'type' => 'hidden',
            ],
            [
                'name' => 'trpAnggotaNama',
                'label' => 'Nama Anggota',
                'type' => 'hidden',
            ],
            [
                'name' => 'trpNominalPinjaman',
                'label' => 'Nominal Pinjaman',
                'type' => 'hidden',
            ],
            [
                'name' => 'trpTenor',
                'label' => 'Tenor (bulan)',
                'type' => 'hidden',
            ],
            [
                'name' => 'trpBunga',
                'label' => 'Bunga (%)',
                'type' => 'hidden',
            ],
            // [
            //     'name' => 'trpAnggotaNama',
            //     'label' => 'Nama Anggota',
            //     'placeholder' => 'Otomatis',
            //     'type' => 'text',
            //     'col' => 'col-md-3',
            //     'required' => true,
            //     'readonly' => true,
            // ],
            // [
            //     'name' => 'trpNominalPinjaman',
            //     'label' => 'Nominal Pinjaman',
            //     'placeholder' => 'Otomatis',
            //     'type' => 'angka',
            //     'col' => 'col-md-3',
            //     'required' => true,
            //     'readonly' => true,
            // ],
            // [
            //     'name' => 'trpTenor',
            //     'label' => 'Tenor (bulan)',
            //     'placeholder' => 'Otomatis',
            //     'type' => 'text',
            //     'col' => 'col-md-3',
            //     'required' => true,
            //     'readonly' => true,
            // ],
            // [
            //     'name' => 'trpBunga',
            //     'label' => 'Bunga (%)',
            //     'placeholder' => 'Otomatis',
            //     'type' => 'text',
            //     'col' => 'col-md-3',
            //     'required' => true,
            //     'readonly' => true,
            // ],
            [
                'name' => 'trpMetodBayarId',
                'nameValue' => 'trpMetodBayarNama',
                'label' => 'Metode Pembayaran',
                'placeholder' => '-- Cari dan pilih metode pembayaran --',
                'type' => 'autocomplete',
                'col' => 'col-md-4',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.metode-pembayaran.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'trpBiayaAdmin',
                'label' => 'Biaya Admin',
                'placeholder' => '0 jika tidak ada biaya admin',
                'type' => 'angka',
                'col' => 'col-md-4',
                'required' => true,
            ],
            [
                'name' => 'trpKeterangan',
                'label' => 'Keterangan Pencairan',
                'placeholder' => 'Masukkan keterangan pencairan (opsional)',
                'type' => 'textarea',
                'col' => 'col-md-4',
                'required' => false,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Tanggal',
                    'field' => 'trpTanggalCair',
                    'type' => 'date'
                ],
                [
                    'label' => 'Kode Pengajuan',
                    'field' => 'trpNoPinjaman',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Peminjam',
                    'field' => 'trpAnggotaNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Pengajuan Pinjam',
                    'field' => 'trpNominalPinjaman',
                    'type' => 'rupiah'
                ],
                [
                    'label' => 'Bunga (%)',
                    'field' => 'trpBunga',
                    'type' => 'angka'
                ]
            ];
    }
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $search = $request->get('search');
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

        return view('pinjaman.pencairan', array_merge([
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
    public function store(Request $request): RedirectResponse
    {
        $modelClass = $this->model;

        $rules = array_merge($this->buildValidationRules(), [
            'trpNominalPinjaman' => ['required', 'numeric', 'gt:0'],
            'trpTenor' => ['required', 'integer', 'min:1'],
            'trpBunga' => ['required', 'numeric', 'min:0'],
        ]);

        $data = $request->validate($rules);

        if (method_exists($this, 'beforeSave')) {
            $data = $this->beforeSave($data, null);
        }

        $data[$modelClass::CREATED_BY] = auth()->user()->name;
        $data[$modelClass::CREATED_AT] = now();

        $res = $modelClass::create($data);

        if (method_exists($this, 'afterSave')) {
            $this->afterSave($res->toArray());
        }

        return redirect()
            ->route($this->route . '.index')
            ->with('success', $this->titlePage . ' berhasil ditambahkan.');
    }
    protected function beforeSave(array $data, $id = null): array
    {
        $nominal = (float) $data['trpNominalPinjaman'];
        $tenor = (int) $data['trpTenor'];
        $bunga = (float) $data['trpBunga'];

        $data['trpCicilanPokok'] = round($nominal / $tenor, 2);
        $data['trpCicilanBunga'] = round($nominal * $bunga / 100, 2);
        $data['trpTotalCicilan'] = round(
            $data['trpCicilanPokok'] + $data['trpCicilanBunga'],
            2
        );

        return $data;
    }
    protected function afterSave(array $data): void
    {
        $pengajuanId = $data['trpPengajuanId'];
        $pengajuan = PengajuanPinjaman::find($pengajuanId);
        if ($pengajuan) {
            $pengajuan->tpStatus = 4; // Assuming 4 represents "cair" status
            $pengajuan->save();
        }
    }
    protected function beforeDelete($id): void
    {
        $pinjamanId = $id;
        $pengajuan = Pinjaman::find($pinjamanId);
        if ($pengajuan) {
            PengajuanPinjaman::where('tpId', $pengajuan->trpPengajuanId)->update(['tpStatus' => 1]);
            $pengajuan->save();
        }
    }
}