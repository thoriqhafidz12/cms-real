<?php

namespace App\Http\Controllers\Pinjaman;

use App\Http\Controllers\BaseController;
use App\Models\Jurnal\Pengeluaran;
use App\Models\Pinjaman\JadwalAngsuran;
use App\Models\Pinjaman\PembayaranPinjaman;
use App\Models\Pinjaman\PengajuanPinjaman;
use App\Models\Pinjaman\Pinjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // [
            //     'name' => 'trpBiayaAdmin',
            //     'label' => 'Biaya Admin',
            //     'placeholder' => '0 jika tidak ada biaya admin',
            //     'type' => 'angka',
            //     'col' => 'col-md-4',
            //     'required' => true,
            // ],
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

        $data['trpSudahDibayar'] = 0;
        $data['trpSisaCicilan'] = $data['trpTotalCicilan'] * $data['trpTenor'];
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
        return $this->hitungCicilan($data);
    }

    protected function beforeUpdate(array $data, $record): array
    {
        // Hidden field nominal/tenor/bunga ikut dikirim saat edit, tapi
        // tetap pakai nilai lama sebagai fallback kalau kosong.
        $data['trpNominalPinjaman'] = $data['trpNominalPinjaman'] ?? $record->trpNominalPinjaman;
        $data['trpTenor'] = $data['trpTenor'] ?? $record->trpTenor;
        $data['trpBunga'] = $data['trpBunga'] ?? $record->trpBunga;
        $data['trpSudahDibayar'] = 0;
        $data['trpSisaCicilan'] = $data['trpSisaCicilan'] ?? $record->trpSisaCicilan;

        return $this->hitungCicilan($data);
    }

    /** Hitung cicilan pokok/bunga/total dari nominal, tenor, dan bunga. */
    protected function hitungCicilan(array $data): array
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

        $this->generateJadwalAngsuran((object) $data);

        $this->createPengeluaranRecord($data);
    }

    /**
     * Saat pencairan diupdate: perbarui kas keluar + jurnal, lalu regenerate
     * jadwal angsuran (hanya jika belum ada angsuran yang dibayar).
     */
    protected function afterUpdate(array $data, $id): void
    {
        $detail = $this->getPengeluaranBySumber('PENCAIRAN PINJAMAN', $id);

        if ($detail) {
            // Nomor dokumen (kNo) tidak diubah karena sudah terbit.
            $detail->update([
                'kNilai' => $data['trpNominalPinjaman'],
                'kTgl' => $data['trpTanggalCair'],
                'kKeterangan' => 'Pencairan Pinjaman untuk ' . $data['trpAnggotaNama'],
                'kPenagihan' => $data['trpAnggotaNama'],
            ]);

            // createJurnal otomatis delete + insert ulang, jadi jurnal selalu sinkron.
            $this->createJurnal('PENGELUARAN', $detail->kCoa, $detail, 'PENCAIRAN PINJAMAN');
        } else {
            // Data lama belum punya record kas → buat baru.
            $this->createPengeluaranRecord($data);
        }

        $this->regenerateJadwalAngsuran($data);
    }

    public function destroy(string $id): RedirectResponse
    {
        $modelClass = $this->model;

        $record = $modelClass::where($this->primaryKey, $id)
            ->firstOrFail();

        // Nilai default respons.
        $redirectRoute = $this->route . '.index';
        $messageType = 'success';
        $message = $this->titlePage . ' berhasil dihapus.';

        $adaPembayaran = PembayaranPinjaman::where('tppPinjamanId', $id)
            ->exists();

        if ($adaPembayaran) {
            $messageType = 'error';
            $message = 'Pinjaman sudah dibayar dan tidak dapat dihapus.';
        } else {
            DB::transaction(function () use ($record, $id) {
                PengajuanPinjaman::where('tpId', $record->trpPengajuanId)
                    ->update(['tpStatus' => 1]);

                $record->delete();

                if (method_exists($this, 'afterDelete')) {
                    $this->afterDelete($id);
                }
            });
        }

        return redirect()
            ->route($redirectRoute)
            ->with($messageType, $message);
    }

    /** Bersihkan data anak setelah pinjaman terhapus. */
    protected function afterDelete($id): void
    {
        JadwalAngsuran::where('tjaPinjamanId', $id)->delete();

        $this->hapusPengeluaranBySumber('PENCAIRAN PINJAMAN', $id);
    }

    /**
     * Regenerate jadwal angsuran setelah update. Jika sudah ada angsuran
     * yang dibayar, jadwal tidak diubah agar riwayat pembayaran tidak rusak.
     */
    private function regenerateJadwalAngsuran(array $data): void
    {
        $sudahDibayar = JadwalAngsuran::where('tjaPinjamanId', $data['trpjId'])
            ->where('tjaStatus', '!=', '0')
            ->exists();

        if ($sudahDibayar) {
            return;
        }

        JadwalAngsuran::where('tjaPinjamanId', $data['trpjId'])->delete();
        $this->generateJadwalAngsuran((object) $data);
    }

    public function generateJadwalAngsuran($pinjaman): void
    {
        DB::transaction(function () use ($pinjaman) {
            $tenor = (int) $pinjaman->trpTenor;

            if ($tenor <= 0) {
                throw new \InvalidArgumentException(
                    'Tenor pinjaman harus lebih dari 0.'
                );
            }

            $tanggalDasar = now();

            for ($i = 1; $i <= $tenor; $i++) {
                $tanggalAngsuran = $tanggalDasar->copy()
                    ->addMonthsNoOverflow($i)
                    ->toDateString();

                $jadwal = JadwalAngsuran::create([
                    'tjaPinjamanId' => $pinjaman->trpjId,
                    'tjaTanggalAngsuran' => $tanggalAngsuran,
                    'tjaCicilanKe' => $i,
                    'tjaNominalAngsuran' => $pinjaman->trpCicilanPokok,
                    'tjaNominalBunga' => $pinjaman->trpCicilanBunga,
                    'tjaTotalTagihan' => $pinjaman->trpTotalCicilan,
                    'tjaSisaTagihan' => $pinjaman->trpTotalCicilan,
                    'tjaStatus' => '0'
                ]);

                if (!$jadwal->exists) {
                    throw new \RuntimeException(
                        "Gagal menyimpan jadwal angsuran ke-{$i}."
                    );
                }
            }
        });
    }

    public function createPengeluaranRecord(array $data): void
    {
        $counterKeluar = $this->getKeluarCounter($data['trpTanggalCair']);

        $detail = Pengeluaran::create([
            'kNo' => str_pad($counterKeluar, 5, '0', STR_PAD_LEFT) . '/PENGELUARAN/' . $data['trpTanggalCair'],
            'kNilai' => $data['trpNominalPinjaman'],
            'kTgl' => $data['trpTanggalCair'],
            'kKeterangan' => 'Pencairan Pinjaman untuk ' . $data['trpAnggotaNama'],
            'kCoa' => '1.01.03.01', // PIUTANG ANGGOTA
            'kPenagihan' => $data['trpAnggotaNama'],
            'kStatus' => 0, // Belum Closing
            'kTerpakai' => 0,
            'kIdPengajuanBelanja' => null,
            'kSumber' => 'PENCAIRAN PINJAMAN',
            'kSumberId' => $data['trpjId'],
        ]);

        $this->createJurnal('PENGELUARAN', $detail->kCoa, $detail, 'PENCAIRAN PINJAMAN');
    }
}
