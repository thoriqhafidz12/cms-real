{{-- Dynamic CRUD Form — digunakan di index page kanan --}}
@php
    $isEdit = isset($editData) && $editData;
    $action = $isEdit ? route($route . '.update', $editData->{$primaryKey}) : route($route . '.store');

    // Cek apakah ada field autocomplete di form ini
    $hasAutocomplete = false;
    foreach ($form as $f) {
        if (($f['type'] ?? '') === 'autocomplete') {
            $hasAutocomplete = true;
            break;
        }
    }
@endphp

{{-- Load Select2 hanya jika ada field autocomplete --}}
@if($hasAutocomplete)
    @once
        @push('styles')
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
        @endpush
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        @endpush
    @endonce
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @foreach ($form as $field)
        @php
            $oldVal = old($field['name'], $isEdit ? $editData->{$field['name']} : null);
        @endphp

        @if (in_array($field['type'], ['text', 'email', 'password', 'number']))
            <div class="{{ $field['col'] ?? 'col-md-12' }} mb-2">
                <label>
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                    class="form-control @error($field['name']) is-invalid @enderror" value="{{ $oldVal }}"
                    placeholder="{{ $field['placeholder'] }}" {{ !empty($field['required']) ? 'required' : '' }}>
                @error($field['name'])
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        @elseif ($field['type'] === 'angka')
            @php
                $displayVal = $oldVal ? number_format((float) $oldVal, 0, ',', '.') : '';
                $rawVal = $oldVal ?? '';
            @endphp
            <div class="{{ $field['col'] ?? 'col-md-12' }} mb-2">
                <label>
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <input type="text" id="{{ $field['name'] }}_display"
                    class="form-control @error($field['name']) is-invalid @enderror" value="{{ $displayVal }}"
                    placeholder="{{ $field['placeholder'] }}" {{ !empty($field['required']) ? 'required' : '' }}
                    oninput="autoNumericDot(this, '{{ $field['name'] }}')"
                    autocomplete="off">
                <input type="hidden" name="{{ $field['name'] }}" id="{{ $field['name'] }}" value="{{ $rawVal }}">
                @error($field['name'])
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        @elseif ($field['type'] === 'date')
            <div class="{{ $field['col'] ?? 'col-md-12' }} mb-2">
                <label>
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                    class="form-control @error($field['name']) is-invalid @enderror" value="{{ $oldVal }}"
                    placeholder="{{ $field['placeholder'] }}" {{ !empty($field['required']) ? 'required' : '' }}>
                @error($field['name'])
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        @elseif ($field['type'] === 'select')
            @php
                // Resolve options dari variable yang tersedia di view
                // Prioritas: $field['options'] → $parentMenus / $roles / etc.
                $selectOptions = $field['options'] ?? [];
                if (empty($selectOptions)) {
                    $tryVars = [$field['name'] . 's', $field['name'] . 'Options'];
                    foreach ($tryVars as $var) {
                        if (!empty($$var)) {
                            $selectOptions = $$var;
                            break;
                        }
                    }
                }
                // Fallback khusus: mParentId pakai $parentMenus
                if (empty($selectOptions) && $field['name'] === 'mParentId' && !empty($parentMenus)) {
                    $selectOptions = $parentMenus;
                }
                // Fallback: role pakai $roles
                if (empty($selectOptions) && $field['name'] === 'role' && !empty($roles)) {
                    $selectOptions = $roles;
                }
            @endphp
            <div class="{{ $field['col'] ?? 'col-md-12' }} mb-2">
                <label>
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <select name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                    class="form-control @error($field['name']) is-invalid @enderror"
                    {{ !empty($field['required']) ? 'required' : '' }}>
                    <option value="">{{ $field['placeholder'] }}</option>
                    @foreach ($selectOptions as $opt)
                        @if (is_object($opt))
                            @php
                                $optKey = $opt->mId ?? ($opt->rId ?? ($opt->id ?? null));
                                $optVal = $opt->mNama ?? ($opt->rNama ?? ($opt->name ?? (string) $opt));
                            @endphp
                            <option value="{{ $optKey }}" {{ $oldVal == $optKey ? 'selected' : '' }}>
                                {{ $optVal }}
                            </option>
                        @elseif (is_array($opt))
                            <option value="{{ $opt['value'] ?? ($opt['id'] ?? '') }}"
                                {{ $oldVal == ($opt['value'] ?? ($opt['id'] ?? '')) ? 'selected' : '' }}>
                                {{ $opt['label'] ?? ($opt['name'] ?? '') }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error($field['name'])
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        @elseif ($field['type'] === 'autocomplete')
            @php
                $acConfig = $field['autocomplete'] ?? [];
                $acUrl = $acConfig['url'] ?? '';
                $acTextField = $acConfig['textField'] ?? 'name';
                $acValueField = $acConfig['valueField'] ?? 'id';
                $acPlaceholder = $field['placeholder'] ?? '-- Cari dan pilih --';
                $selectedText = $autocompleteSelected[$field['name']] ?? null;
            @endphp
            <div class="{{ $field['col'] ?? 'col-md-12' }} mb-2">
                <label>
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <select name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                    class="form-control autocomplete-select @error($field['name']) is-invalid @enderror"
                    data-ac-url="{{ $acUrl }}"
                    data-ac-text-field="{{ $acTextField }}"
                    data-ac-value-field="{{ $acValueField }}"
                    data-ac-placeholder="{{ $acPlaceholder }}"
                    {{ !empty($field['required']) ? 'required' : '' }}>
                    @if ($oldVal)
                        <option value="{{ $oldVal }}" selected>
                            {{ $selectedText ?? $oldVal }}
                        </option>
                    @endif
                </select>
                @error($field['name'])
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        @endif
    @endforeach

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
    </button>

    @if ($isEdit)
        <a href="{{ route($route . '.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Batal
        </a>
    @endif
</form>

@if($hasAutocomplete)
    @once
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.autocomplete-select').forEach(function (el) {
                    const url = el.dataset.acUrl;
                    const textField = el.dataset.acTextField || 'name';
                    const valueField = el.dataset.acValueField || 'id';
                    const placeholder = el.dataset.acPlaceholder || '-- Cari dan pilih --';
                    const $select = $(el);

                    $select.select2({
                        theme: 'bootstrap4',
                        placeholder: placeholder,
                        allowClear: true,
                        width: '100%',
                        closeOnSelect: true,
                        dropdownCssClass: 'select2-dropdown-custom',
                        ajax: {
                            url: url,
                            dataType: 'json',
                            delay: 300,
                            data: function (params) {
                                return { search: params.term || '' };
                            },
                            processResults: function (data) {
                                const results = Array.isArray(data) ? data : (data.data || data.results || []);
                                return {
                                    results: results.map(function (item) {
                                        return {
                                            id: item[valueField],
                                            text: item[textField]
                                        };
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0,
                        templateResult: function (item) {
                            if (item.loading) {
                                return $('<div class="select2-result-loading">' +
                                    '<i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</div>');
                            }
                            return $('<div class="select2-result-item">' +
                                '<i class="fas fa-tag mr-2 text-muted"></i>' +
                                $('<span>').text(item.text).html() +
                                '</div>');
                        },
                        templateSelection: function (item) {
                            if (!item.id) {
                                return $('<span class="text-muted">' + placeholder + '</span>');
                            }
                            return $('<span class="select2-selection-text">' +
                                '<i class="fas fa-check-circle mr-1 text-success"></i>' + item.text +
                                '</span>');
                        },
                        language: {
                            searching: function () { return 'Mencari...'; },
                            noResults: function () { return '⛔ Data tidak ditemukan'; },
                            errorLoading: function () { return 'Gagal memuat data'; }
                        }
                    });

                    // Auto-fokus ke search input saat dropdown terbuka
                    $select.on('select2:open', function () {
                        setTimeout(function () {
                            document.querySelector('.select2-search__field').focus();
                        }, 100);
                    });
                });
            });
        </script>
        @endpush
    @endonce
@endif
