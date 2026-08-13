<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $res = $modelClass::create($this->beforeSave($validated, null));

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

        $record->update($this->beforeUpdate($validated, $id));

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
                case 'select':
                case 'autocomplete':
                    $fieldRules[] = 'integer';
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

            // Unique rule
            if (!empty($field['unique'])) {
                $uniqueRule = 'unique:' . $field['unique'];
                if ($excludeId !== null) {
                    $uniqueRule .= ',' . $excludeId . ',' . $this->primaryKey;
                }
                $fieldRules[] = $uniqueRule;
            }

            $rules[$field['name']] = $fieldRules;
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
}
