<?php

namespace App\Http\Controllers;

use App\Models\Jurnal\Jurnal;
use App\Models\Jurnal\Penerimaan;
use App\Models\Jurnal\PenerimaanCounter;
use App\Models\Jurnal\Pengeluaran;
use App\Models\Jurnal\PengeluaranCounter;
use App\Models\Master\Mapping\MappingPenerimaan;
use App\Models\Master\Mapping\MappingPengeluaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

abstract class BaseController extends Controller
{
    /** @var string Model class name, e.g. \App\Models\Menu::class */
    protected string $model;

    /** @var string Route prefix, e.g. 'menus' */
    protected string $route;

    /** @var string Page title, e.g. 'Daftar Menu' */
    protected string $titlePage;
    /** @var string Validation rules, e.g. 'required|string|max:255' */
    protected array $rules = [];

    /** @var string Primary key column, e.g. 'mId' */
    protected string $primaryKey = 'id';

    /** @var string Table name untuk unique validation */
    protected string $table = '';

    /** @var array Field definitions untuk form */
    protected array $form = [];

    /** @var array Column definitions untuk tabel listing */
    protected array $grid = [];

    /** @var string|array Column name(s) untuk search */
    protected string|array $searchColumn = '';

    /** @var string Model relation untuk eager load (opsional) */
    protected string $withRelation = '';

    /** @var array Extra data untuk dikirim ke view */
    protected array $extraViewData = [];

    /**
     * Redirect create ke index (form tersedia di halaman index).
     */
    public function create(): RedirectResponse
    {
        return redirect()->route($this->route . '.index');
    }

    /**
     * Redirect edit ke index dengan query param inline edit.
     */
    public function edit(string $id): RedirectResponse
    {
        return redirect()->route($this->route . '.index', ['edit' => $id]);
    }

    /**
     * Redirect show ke index.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route($this->route . '.index');
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $modelClass = $this->model;

        $validated = $request->validate(
            $this->buildValidationRules()
        );

        // $res = $modelClass::create($this->beforeSave($validated, null));
        if (method_exists($this, 'beforeSave')) {
            $data = $this->beforeSave($validated, null);
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

    /**
     * Update data.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $modelClass = $this->model;
        $record = $modelClass::where($this->primaryKey, $id)->firstOrFail();

        $validated = $request->validate(
            $this->buildValidationRules($id)
        );

        if (method_exists($this, 'beforeUpdate')) {
            $data = $this->beforeUpdate($validated, $record);
        } else {
            $data = $validated;
        }

        $data[$modelClass::UPDATED_BY] = auth()->user()->name;
        $data[$modelClass::UPDATED_AT] = now();

        $record->update($data);
        // $record->update($this->beforeUpdate($validated, $id));

        if (method_exists($this, 'afterUpdate')) {
            $this->afterUpdate($record->fresh()->toArray(), $record->{$this->primaryKey});
        }

        return redirect()
            ->route($this->route . '.index')
            ->with('success', $this->titlePage . ' berhasil diupdate.');
    }

    /**
     * Hapus data.
     */
    public function destroy(string $id): RedirectResponse
    {
        $modelClass = $this->model;
        $record = $modelClass::where($this->primaryKey, $id)->firstOrFail();

        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete($id);
        }

        $record->delete();

        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete($id);
        }

        return redirect()
            ->route($this->route . '.index')
            ->with('success', $this->titlePage . ' berhasil dihapus.');
    }

    /**
     * Build validation rules dari $form array.
     */
    protected function buildValidationRules(?string $excludeId = null): array
    {
        $rules = [];

        foreach ($this->form as $field) {
            $fieldRules = [];

            // Required
            if (!empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type-specific rules
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    $fieldRules[] = 'max:255';
                    break;
                case 'password':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'min:4';
                    break;
                case 'autocomplete':
                    // Default: value berupa string (kode seperti '1-1000').
                    // Field dengan 'rules' sendiri boleh menimpa aturan ini.
                    if (empty($field['rules'])) {
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:225';
                    }
                    if (!empty($field['exists'])) {
                        $fieldRules[] = 'exists:' . $field['exists'];
                    }
                    break;
                case 'angka':
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:225';
                    break;
            }

            // Aturan tambahan/override spesifik field (mis. 'integer' untuk
            // autocomplete yang valuenya memang id numerik, atau
            // 'string|max:20' untuk kode yang lebih pendek dari default)
            if (!empty($field['rules'])) {
                $extra = is_array($field['rules']) ? $field['rules'] : explode('|', $field['rules']);
                $fieldRules = array_merge($fieldRules, $extra);
            }

            // Unique rule
            if (!empty($field['unique'])) {
                $uniqueRule = 'unique:' . $field['unique'];
                if ($excludeId !== null) {
                    $uniqueRule .= ',' . $excludeId . ',' . $this->primaryKey;
                }
                $fieldRules[] = $uniqueRule;
            }

            $rules[$field['name']] = $fieldRules;

            // Field pasangan (nameValue) pada tipe autocomplete_name ikut
            // divalidasi agar nilainya (mis. nama) ikut tersimpan ke database.
            if (!empty($field['nameValue']) && !isset($rules[$field['nameValue']])) {
                $rules[$field['nameValue']] = ['nullable', 'string', 'max:225'];
            }
        }

        // Merge rules tambahan dari child class ($this->rules).
        // Aturan unique di sini juga mengecualikan id record yang sedang diedit.
        foreach ($this->rules as $fieldName => $fieldRules) {
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            if ($excludeId !== null) {
                $fieldRules = array_map(function ($rule) use ($excludeId) {
                    if (is_string($rule) && str_starts_with($rule, 'unique:')) {
                        // Format 'unique:table,column' → tambahkan id yang dikecualikan
                        $segments = explode(',', substr($rule, strlen('unique:')));
                        if (count($segments) === 2) {
                            $rule .= ',' . $excludeId . ',' . $this->primaryKey;
                        }
                    }
                    return $rule;
                }, $fieldRules);
            }

            $rules[$fieldName] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Hook: transform data sebelum insert. Override di child class jika perlu.
     */
    protected function beforeSave(array $data, $record = null): array
    {
        return $data;
    }

    /**
     * Hook: transform data sebelum update. Override di child class jika perlu.
     */
    protected function beforeUpdate(array $data, $record): array
    {
        return $data;
    }

    //  ======================== HELPER TRANSACTIONS ========================

    public function getKeluarCounter($date = null): int
    {
        $year = $date ? Carbon::parse($date)->year : now()->year;

        return DB::transaction(function () use ($year) {
            // Belum ada: insert.
            // Sudah ada: abaikan insert karena kTahun UNIQUE.
            PengeluaranCounter::query()->insertOrIgnore([
                'kTahun' => $year,
                'kCounter' => 0,
            ]);

            // Proses lain yang ingin mengunci baris ini harus menunggu.
            $counter = PengeluaranCounter::where('kTahun', $year)
                ->lockForUpdate()
                ->firstOrFail();

            $counter->kCounter = (int) $counter->kCounter + 1;

            if (!$counter->save()) {
                throw new \RuntimeException('Gagal menyimpan counter.');
            }

            return (int) $counter->kCounter;
        }, 5);
    }

    public function getTerimaCounter($date = null): int
    {
        $year = $date ? Carbon::parse($date)->year : now()->year;

        return DB::transaction(function () use ($year) {
            // Belum ada: insert.
            // Sudah ada: abaikan insert karena tTahun UNIQUE.
            PenerimaanCounter::query()->insertOrIgnore([
                'tTahun' => $year,
                'tCounter' => 0,
            ]);

            // Proses lain yang ingin mengunci baris ini harus menunggu.
            $counter = PenerimaanCounter::where('tTahun', $year)
                ->lockForUpdate()
                ->firstOrFail();

            $counter->tCounter = (int) $counter->tCounter + 1;

            if (!$counter->save()) {
                throw new \RuntimeException('Gagal menyimpan counter.');
            }

            return (int) $counter->tCounter;
        }, 5);
    }

    public function getMapping($jenisMapping, $sumber)
    {
        if ($jenisMapping === 'PENERIMAAN') {
            $mapping = MappingPenerimaan::where('mapKodeAsal', $sumber)->join('ms_objek', 'msoKode', '=', 'mapKodeDebet')->get();
        } elseif ($jenisMapping === 'PENGELUARAN') {
            $mapping = MappingPengeluaran::where('mapKodeAsal', $sumber)->join('ms_objek', 'msoKode', '=', 'mapKodeKredit')->get();
        } else {
            throw new \InvalidArgumentException("Jenis mapping tidak valid: {$jenisMapping}");
        }

        if (!$mapping) {
            throw new \RuntimeException("Mapping tidak ditemukan untuk sumber: {$sumber}, jenis mapping: {$jenisMapping}");
        }

        return $mapping;
    }

    public function createJurnal($jenisMapping, $sumber, $data): void
    {
        $mapping = $this->getMapping($jenisMapping, $sumber);

        if ($jenisMapping === 'PENERIMAAN') {
            Jurnal::where('jHeadId', $data['pId'] ?? null)
                ->where('jSumber', $jenisMapping)
                ->delete();

            foreach ($mapping as $map) {
                $jurnalData = [
                    'jHeadId' => $data['headId'] ?? null,
                    'jNo' => $data['no'] ?? null,
                    'jTgl' => $data['tgl'] ?? null,
                    'jKeterangan' => $data['keterangan'] ?? null,
                    'jRekDebetKode' => $map['mapKodeDebet'] ?? null,
                    'jRekDebetNama' => $map['mapNamaDebet'] ?? null,
                    'jDebetNilai' => $data['debetNilai'] ?? 0,
                    'jRekKreditKode' => $map['mapKodeKredit'] ?? null,
                    'jRekKreditNama' => $map['mapNamaKredit'] ?? null,
                    'jKreditNilai' => $data['kreditNilai'] ?? 0,
                    'jSumber' => $jenisMapping,
                    'jStatus' => 0
                ];
                Jurnal::insert($jurnalData);
            }
        } else if ($jenisMapping === 'PENGELUARAN') {
            Jurnal::where('jHeadId', $data['kId'] ?? null)
                ->where('jSumber', $jenisMapping)
                ->delete();

            foreach ($mapping as $map) {
                $jurnalData = [
                    'jHeadId' => $data['kId'] ?? null,
                    'jNo' => $data['kNo'] ?? null,
                    'jTgl' => $data['kTgl'] ?? null,
                    'jKeterangan' => $data['kKeterangan'] ?? null,
                    'jRekDebetKode' => $map['mapKodeDebet'] ?? null,
                    'jRekDebetNama' => $map['mapNamaDebet'] ?? null,
                    'jDebetNilai' => $data['kNilai'] ?? 0,
                    'jRekKreditKode' => $map['mapKodeKredit'] ?? null,
                    'jRekKreditNama' => $map['mapNamaKredit'] ?? null,
                    'jKreditNilai' => $data['kNilai'] ?? 0,
                    'jSumber' => $jenisMapping,
                    'jStatus' => 0
                ];
                Jurnal::insert($jurnalData);
            }
        } else {
            throw new \InvalidArgumentException("Jenis mapping tidak valid: {$jenisMapping}");
        }
    }

    /** Cari record kas keluar berdasarkan sumber transaksi. */
    public function getPengeluaranBySumber($sumber, $sumberId)
    {
        return Pengeluaran::where('kSumber', $sumber)
            ->where('kSumberId', $sumberId)
            ->first();
    }

    /** Cari record kas masuk berdasarkan sumber transaksi. */
    public function getPenerimaanBySumber($sumber, $sumberId)
    {
        return Penerimaan::where('tSumber', $sumber)
            ->where('tSumberId', $sumberId)
            ->first();
    }

    /** Hapus semua baris jurnal milik satu header kas. */
    public function hapusJurnal($jenisMapping, $headId): void
    {
        Jurnal::where('jHeadId', $headId)
            ->where('jSumber', $jenisMapping)
            ->delete();
    }

    /**
     * Hapus kas keluar beserta jurnalnya berdasarkan sumber transaksi.
     * Counter tidak dikembalikan agar nomor dokumen tidak pernah dipakai ulang.
     */
    public function hapusPengeluaranBySumber($sumber, $sumberId): void
    {
        $detail = $this->getPengeluaranBySumber($sumber, $sumberId);

        if ($detail) {
            $this->hapusJurnal('PENGELUARAN', $detail->kId);
            $detail->delete();
        }
    }

    /**
     * Hapus kas masuk beserta jurnalnya berdasarkan sumber transaksi.
     * Counter tidak dikembalikan agar nomor dokumen tidak pernah dipakai ulang.
     */
    public function hapusPenerimaanBySumber($sumber, $sumberId): void
    {
        $detail = $this->getPenerimaanBySumber($sumber, $sumberId);

        if ($detail) {
            $this->hapusJurnal('PENERIMAAN', $detail->tId);
            $detail->delete();
        }
    }

    //  ======================== HELPER FUNCTIONS ========================
    public function formatDate($date): string
    {
        $tanggal = '';
        if (empty($date)) {
            $tanggal = '';
        } else {
            if (substr($date, 2, 1) == '/') {
                $a = explode('/', $date);
            } else {
                $a = explode('-', $date);
                $d = $a[2];
                $m = $a[1];
                $y = $a[0];
                $a[0] = $m + 0;
                $a[1] = $d;
                $a[2] = $y;
            }
            $tanggal = $a[2] . '-' . $a[1] . '-' . $a[0];
        }
        return $tanggal;
    }

    function dateID($tgl)
    {

        if (substr($tgl, 2, 1) == '/') {
            $a = explode('/', $tgl);
        } else {
            $a = explode('-', $tgl);
            $d = $a[2];
            $m = $a[1];
            $y = $a[0];
            $a[0] = $m + 0;
            $a[1] = $d;
            $a[2] = $y;
        }

        $nmbulan = '';

        switch ($a[0]) {
            case 1:
                $nmbulan = 'Januari';
                break;
            case 2:
                $nmbulan = 'Februari';
                break;
            case 3:
                $nmbulan = 'Maret';
                break;
            case 4:
                $nmbulan = 'April';
                break;
            case 5:
                $nmbulan = 'Mei';
                break;
            case 6:
                $nmbulan = 'Juni';
                break;
            case 7:
                $nmbulan = 'Juli';
                break;
            case 8:
                $nmbulan = 'Agustus';
                break;
            case 9:
                $nmbulan = 'September';
                break;
            case 10:
                $nmbulan = 'Oktober';
                break;
            case 11:
                $nmbulan = 'November';
                break;
            case 12:
                $nmbulan = 'Desember';
                break;
        }

        return $a[1] . ' ' . $nmbulan . ' ' . $a['2'];
    }

    function dateIDkosong($tgl)
    {
        if ($tgl != 0) {
            if (substr($tgl, 2, 1) == '/') {
                $a = explode('/', $tgl);
            } else {
                $a = explode('-', $tgl);
                $d = $a[2];
                $m = $a[1];
                $y = $a[0];
                $a[0] = $m + 0;
                $a[1] = $d;
                $a[2] = $y;
            }

            $nmbulan = '';

            switch ($a[0]) {
                case 1:
                    $nmbulan = 'Januari';
                    break;
                case 2:
                    $nmbulan = 'Februari';
                    break;
                case 3:
                    $nmbulan = 'Maret';
                    break;
                case 4:
                    $nmbulan = 'April';
                    break;
                case 5:
                    $nmbulan = 'Mei';
                    break;
                case 6:
                    $nmbulan = 'Juni';
                    break;
                case 7:
                    $nmbulan = 'Juli';
                    break;
                case 8:
                    $nmbulan = 'Agustus';
                    break;
                case 9:
                    $nmbulan = 'September';
                    break;
                case 10:
                    $nmbulan = 'Oktober';
                    break;
                case 11:
                    $nmbulan = 'November';
                    break;
                case 12:
                    $nmbulan = 'Desember';
                    break;
            }

            $res = $a[1] . ' ' . $nmbulan . ' ' . $a['2'];
        } else {
            $res = '';
        }
        return $res;
    }

    function formatRupiah($angka, $digit = 2)
    {
        if (empty($angka)) {
            $angka = 0;
        } else {
            $angka = str_replace(',', '', $angka);
        }
        $hasil_rupiah = number_format($angka, $digit, ',', '.');
        return $hasil_rupiah;
    }
}
