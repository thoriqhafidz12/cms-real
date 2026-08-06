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

        $modelClass::create($this->beforeSave($validated, null));

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

        $record->update($this->beforeUpdate($validated, $record));

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
                    $fieldRules[] = 'integer';
                    $fieldRules[] = 'exists:' . ($field['exists'] ?? '');
                    break;
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
}
