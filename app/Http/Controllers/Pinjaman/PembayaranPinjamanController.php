<?php

namespace App\Http\Controllers\Pinjaman;

use App\Http\Controllers\BaseController;
use App\Models\Jurnal\Penerimaan;
use App\Models\Pinjaman\JadwalAngsuran;
use App\Models\Pinjaman\PembayaranPinjaman;
use App\Models\Pinjaman\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;



class PembayaranPinjamanController extends BaseController
{
    public function __construct()
    {
        $this->model = PembayaranPinjaman::class;
        $this->route = 'pembayaran-pinjaman';
        $this->titlePage = 'Daftar Pembayaran Pinjaman';
        $this->primaryKey = 'tppId';
        $this->table = 'tr_pembayaranpinjaman';
        $this->searchColumn = ['tppNoBukti', 'tppTanggalBayar'];

        $this->rules = [
            // 'tppNoBukti' => 'required|unique:tr_pembayaranpinjaman,tppNoBukti',
            'tppNominalBayar' => 'required|numeric|min:1',
        ];

        $this->form = [
            [
                'name' => 'tppPinjamanId',
                'label' => 'Kode Pinjaman',
                'placeholder' => '-- Cari dan pilih pinjaman --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.pencairan-pinjaman.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            // [
            //     'name' => 'tppAngsuranId',
            //     'label' => 'Kode Angsuran',
            //     'placeholder' => '-- Cari dan pilih angsuran --',
            //     'type' => 'autocomplete_filter',
            //     'col' => 'col-md-6',
            //     'required' => true,
            //     'autocomplete' => [
            //         'url' => route('api.jadwal-angsuran.search'),
            //         'textField' => 'text',
            //         'valueField' => 'id',
            //         'filters' => [
            //             'tppPinjamanId' => 'tppPinjamanId', // param dikirim => nama field form yang dibaca
            //         ],
            //     ]
            // ],
            [
                'name' => 'tppAnggotaId',
                'label' => 'Id Anggota',
                'placeholder' => 'Id Anggota',
                'type' => 'hidden',
                'col' => 'col-md-6',
                'required' => true,
                'readonly' => true,
            ],
            [
                'name' => 'trpTotalCicilan',
                'label' => 'Total Cicilan + Bunga',
                'placeholder' => 'Masukkan total cicilan',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
                'readonly' => true,
            ],
            [
                'name' => 'trpSudahDibayar',
                'label' => 'Sudah Dibayar',
                'placeholder' => 'Masukkan sudah dibayar',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
                'readonly' => true,
            ],
            [
                'name' => 'trpSisaCicilan',
                'label' => 'Sisa Cicilan',
                'placeholder' => 'Masukkan sisa cicilan',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
                'readonly' => true,
            ],
            [
                'name' => 'tppBayarPokok',
                'label' => 'Bayar Pokok',
                'placeholder' => 'Masukkan bayar pokok',
                'type' => 'angka',
                'col' => 'col-md-4',
                'required' => true,
                'readonly' => true,
            ],
            [
                'name' => 'tppBayarBunga',
                'label' => 'Bayar Bunga',
                'placeholder' => 'Masukkan bayar bunga',
                'type' => 'angka',
                'col' => 'col-md-4',
                'required' => true,
                'readonly' => true,
            ],
            [
                'name' => 'tppNominalBayar',
                'label' => 'Nominal Bayar (Bulan)',
                'placeholder' => 'Masukkan nominal bayar',
                'type' => 'angka',
                'col' => 'col-md-4',
                'required' => true,
            ],
            [
                'name' => 'tppTanggalBayar',
                'label' => 'Tanggal Bayar',
                'placeholder' => 'Masukkan tanggal bayar',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tppMetodeBayarId',
                'label' => 'Metode Pembayaran',
                'placeholder' => '-- Cari dan pilih metode pembayaran --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.metode-pembayaran.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'tppKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'textarea',
                'col' => 'col-md-12',
                'required' => false,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'No Bukti',
                    'field' => 'tppNoBukti',
                    'type' => 'text'
                ],
                [
                    'label' => 'Tanggal Bayar',
                    'field' => 'tppTanggalBayar',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nominal Bayar',
                    'field' => 'tppNominalBayar',
                    'type' => 'text'
                ],
                [
                    'label' => 'Keterangan',
                    'field' => 'tppKeterangan',
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

        return view('pinjaman.pembayaran', array_merge([
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

        $validated = $request->validate(
            $this->buildValidationRules()
        );

        try {
            DB::transaction(function () use ($modelClass, $validated) {
                $data = $validated;

                if (method_exists($this, 'beforeSave')) {
                    $data = $this->beforeSave($validated, null);
                }

                $data[$modelClass::CREATED_BY] = auth()->user()->name;
                $data[$modelClass::CREATED_AT] = now();

                $res = $modelClass::create($data);

                if (method_exists($this, 'afterSave')) {
                    $this->afterSave($res->toArray());
                }
            });

            return redirect()
                ->route($this->route . '.index')
                ->with('success', $this->titlePage . ' berhasil ditambahkan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $this->titlePage . ' gagal ditambahkan.');
        }
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $modelClass = $this->model;
        $record = $modelClass::where($this->primaryKey, $id)->firstOrFail();

        $validated = $request->validate(
            $this->buildValidationRules($id)
        );

        DB::transaction(function () use ($modelClass, $validated, $record, $id) {
            $data = $validated;

            if (method_exists($this, 'beforeUpdate')) {
                $data = $this->beforeUpdate($validated, $record);
            }

            $data[$modelClass::UPDATED_BY] = auth()->user()->name;
            $data[$modelClass::UPDATED_AT] = now();

            $pinjamanLamaId = $record->tppPinjamanId;

            $record->update($data);

            if (method_exists($this, 'afterUpdate')) {
                $this->afterUpdate($record->fresh()->toArray(), $id, $pinjamanLamaId);
            }
        });

        return redirect()
            ->route($this->route . '.index')
            ->with('success', $this->titlePage . ' berhasil diupdate.');
    }

    protected function beforeUpdate(array $data, $record): array
    {
        // Nomor bukti tidak diubah karena dokumen sudah terbit.
        $data['tppNoBukti'] = $record->tppNoBukti;
        $data['tppBayarDenda'] = 0;

        return $data;
    }

    protected function beforeSave(array $data, $record = null): array
    {
        $data['tppBayarDenda'] = 0;
        $data['tppNoBukti'] = 'NB-' . date('dmYHi') . '-' . rand(1000, 9999); // Generate No Bukti otomatis

        return $data;
    }

    protected function afterSave($data): void
    {
        $this->alokasiPembayaranKeJadwal($data);

        $counterTerima = $this->getTerimaCounter($data['tppTanggalBayar']);
        $counterKwitansi = $this->getKwitansiCounter($data['tppTanggalBayar']);

        $data['tppNoPenerimaan'] = str_pad($counterTerima, 5, '0', STR_PAD_LEFT) . '/PENERIMAAN/' . $data['tppTanggalBayar'];
        $data['tppNoKwitansi'] = str_pad($counterKwitansi, 5, '0', STR_PAD_LEFT) . '/' . $data['tppTanggalBayar'];

        $this->createPenerimaan($data);

        // Sinkronkan saldo pinjaman (sudah dibayar & sisa cicilan).
        $this->syncSaldoPinjaman($data['tppPinjamanId']);

        // Jika $sisaBayar > 0, ada kelebihan pembayaran
        // yang belum dialokasikan ke jadwal.
    }

    /**
     * Alokasi satu pembayaran ke jadwal angsuran yang masih punya
     * sisa tagihan, mulai dari jadwal paling tua.
     */
    protected function alokasiPembayaranKeJadwal(array $data): void
    {
        $getJadwal = JadwalAngsuran::where('tjaPinjamanId', $data['tppPinjamanId'])
            ->where('tjaStatus', 0)
            ->where('tjaSisaTagihan', '>', 0)
            ->orderBy('tjaTanggalAngsuran', 'asc')
            ->get();

        $sisaBayar = round((float) $data['tppNominalBayar'], 2);

        foreach ($getJadwal as $jadwal) {
            if ($sisaBayar <= 0) {
                break;
            }

            $sisaTagihan = round((float) $jadwal->tjaSisaTagihan, 2);

            // Alokasi tidak boleh melebihi sisa tagihan jadwal ini.
            $alokasiBayar = min($sisaBayar, $sisaTagihan);

            $sisaTagihan = round($sisaTagihan - $alokasiBayar, 2);
            $sisaBayar = round($sisaBayar - $alokasiBayar, 2);

            $jadwal->tjaSisaTagihan = $sisaTagihan;
            $jadwal->tjaStatus = $sisaTagihan <= 0 ? 1 : 0;
            $jadwal->save();
        }
    }

    /**
     * Reset sisa tagihan seluruh jadwal lalu terapkan ulang semua
     * pembayaran pinjaman secara berurutan. Dipakai saat update
     * atau hapus pembayaran agar jadwal selalu sinkron.
     */
    protected function alokasiUlangJadwalAngsuran($pinjamanId): void
    {
        JadwalAngsuran::where('tjaPinjamanId', $pinjamanId)
            ->update([
                'tjaSisaTagihan' => DB::raw('tjaTotalTagihan'),
                'tjaStatus' => 0,
            ]);

        $pembayarans = $this->model::where('tppPinjamanId', $pinjamanId)
            ->orderBy('tppTanggalBayar', 'asc')
            ->orderBy($this->primaryKey, 'asc')
            ->get();

        foreach ($pembayarans as $pembayaran) {
            $this->alokasiPembayaranKeJadwal($pembayaran->toArray());
        }
    }

    /**
     * Sinkronkan trpSudahDibayar, trpSisaCicilan, dan trpStatusPinjaman
     * di tr_pinjaman berdasarkan seluruh transaksi pembayaran.
     * Dipanggil setiap pembayaran ditambah, diupdate, atau dihapus.
     */
    protected function syncSaldoPinjaman($pinjamanId): void
    {
        $pinjaman = Pinjaman::where('trpjId', $pinjamanId)->first();

        if (!$pinjaman) {
            return;
        }

        $sudahDibayar = (float) $this->model::where('tppPinjamanId', $pinjamanId)
            ->sum('tppNominalBayar');

        $totalPinjaman = (float) $pinjaman->trpTotalCicilan * (float) $pinjaman->trpTenor;
        $sisa = $totalPinjaman - $sudahDibayar;

        $pinjaman->trpSudahDibayar = round($sudahDibayar, 2);
        $pinjaman->trpSisaCicilan = round(max($sisa, 0), 2);
        // 0: Belum Lunas, 1: Lunas
        $pinjaman->trpStatusPinjaman = $sisa <= 0 ? 1 : 0;
        $pinjaman->save();
    }

    protected function createPenerimaan(array $data): void
    {
        $res = Penerimaan::create([
            'tSumber' => 'BAYAR PINJAMAN',
            'tSumberId' => $data['tppId'],
            'tKwitansi' => $data['tppNoBukti'],
            'tNoPenerimaan' => $data['tppNoPenerimaan'],
            'tNilaiBayar' => $data['tppNominalBayar'],
            'tTglBayar' => $data['tppTanggalBayar'],
            'tAsalPenerimaan' => $data['tppAnggotaId'] ?? null,
            'tDeskripsi' => $data['tppKeterangan'] ?? null,
            'tCoa' => '1.01.03.01', // Contoh COA untuk penerimaan pinjaman
        ]);

        $this->createJurnal('PENERIMAAN', $res['tCoa'], $res, $res['tSumber']);
    }

    /**
     * Saat pembayaran diupdate: perbarui kas masuk + jurnal,
     * lalu alokasi ulang jadwal angsuran.
     */
    protected function afterUpdate(array $data, $id, $pinjamanLamaId = null): void
    {
        $detail = $this->getPenerimaanBySumber('BAYAR PINJAMAN', $id);

        if ($detail) {
            // Nomor dokumen (tNoPenerimaan/tKwitansi) tidak diubah karena sudah terbit.
            $detail->update([
                'tNilaiBayar' => $data['tppNominalBayar'],
                'tTglBayar' => $data['tppTanggalBayar'],
                'tAsalPenerimaan' => $data['tppAnggotaId'] ?? null,
                'tDeskripsi' => $data['tppKeterangan'] ?? null,
            ]);

            // createJurnal otomatis delete + insert ulang, jadi jurnal selalu sinkron.
            $this->createJurnal('PENERIMAAN', $detail->tCoa, $detail, 'BAYAR PINJAMAN');
        }

        // Bila pembayaran pindah ke pinjaman lain, kembalikan alokasi
        // jadwal pinjaman lama terlebih dahulu.
        if ($pinjamanLamaId && (string) $pinjamanLamaId !== (string) $data['tppPinjamanId']) {
            $this->alokasiUlangJadwalAngsuran($pinjamanLamaId);
        }

        $this->alokasiUlangJadwalAngsuran($data['tppPinjamanId']);

        // Sinkronkan saldo pinjaman lama dan baru.
        if ($pinjamanLamaId && (string) $pinjamanLamaId !== (string) $data['tppPinjamanId']) {
            $this->syncSaldoPinjaman($pinjamanLamaId);
        }

        $this->syncSaldoPinjaman($data['tppPinjamanId']);
    }

    public function destroy(string $id): RedirectResponse
    {
        $modelClass = $this->model;
        $record = $modelClass::where($this->primaryKey, $id)->firstOrFail();

        DB::transaction(function () use ($record, $id) {
            if (method_exists($this, 'beforeDelete')) {
                $this->beforeDelete($id);
            }

            $pinjamanId = $record->tppPinjamanId;

            $record->delete();

            if (method_exists($this, 'afterDelete')) {
                $this->afterDelete($id, $pinjamanId);
            }
        });

        return redirect()
            ->route($this->route . '.index')
            ->with('success', $this->titlePage . ' berhasil dihapus.');
    }

    protected function beforeDelete(string $id): void
    {
        $this->hapusPenerimaanBySumber('BAYAR PINJAMAN', $id);
    }

    /**
     * Kembalikan sisa tagihan jadwal angsuran setelah pembayaran dihapus.
     */
    protected function afterDelete($id, $pinjamanId = null): void
    {
        $this->alokasiUlangJadwalAngsuran($pinjamanId);

        $this->syncSaldoPinjaman($pinjamanId);
    }
}
