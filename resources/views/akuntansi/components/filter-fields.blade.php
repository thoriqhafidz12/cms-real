{{-- Render field filter berdasarkan konfigurasi $form dari controller. --}}
@php
    $hasAutocomplete = false;
    foreach ($form as $f) {
        if (($f['type'] ?? '') === 'autocomplete') {
            $hasAutocomplete = true;
            break;
        }
    }
@endphp

{{-- Load Select2 hanya jika ada field autocomplete --}}
@if ($hasAutocomplete)
    @once
        @push('styles')
            <link href="{{ url('/') }}/assets/css/select2.min.css" rel="stylesheet">
        @endpush
        @push('scripts')
            <script src="{{ url('/') }}/assets/js/select2.min.js"></script>
        @endpush
    @endonce
@endif

{{-- Fields dirender di row sendiri yang memusatkan (justify-content-center):
     1 field → tepat di tengah, field col-md-12 → lebar penuh,
     beberapa field → dimulai dari tengah, bukan dari kiri. --}}
<div class="col-12">
    <div class="row align-items-end justify-content-center">
@foreach ($form as $field)
    @php
        $fieldName = $field['name'];
        $fieldDefault = $field['default'] ?? '';
        $fieldValue = old($fieldName, $fieldDefault);
    @endphp

    @if ($field['type'] === 'hidden')
        <input type="hidden" name="{{ $fieldName }}" id="{{ $fieldName }}" value="{{ $fieldValue }}">
    @elseif ($field['type'] === 'select')
        @php
            // Dukung: [value => label], [['value'=>..,'label'=>..]], atau objek model
            $selectOptions = $field['options'] ?? [];
        @endphp
        <div class="{{ $field['col'] ?? 'col-md-6' }} mb-2">
            <label>{{ $field['label'] }}
                @if (!empty($field['required']))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <select name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control">
                <option value="">{{ $field['placeholder'] ?? '-- Semua --' }}</option>
                @foreach ($selectOptions as $optKey => $optVal)
                    @php
                        if (is_object($optVal)) {
                            $key = $optVal->id ?? ($optVal->mId ?? ($optVal->rId ?? null));
                            $val = $optVal->name ?? ($optVal->mNama ?? ($optVal->rNama ?? (string) $optVal));
                        } elseif (is_array($optVal)) {
                            $key = $optVal['value'] ?? ($optVal['id'] ?? $optKey);
                            $val = $optVal['label'] ?? ($optVal['name'] ?? '');
                        } else {
                            $key = $optKey;
                            $val = $optVal;
                        }
                    @endphp
                    <option value="{{ $key }}" {{ (string) $fieldValue === (string) $key ? 'selected' : '' }}>
                        {{ $val }}
                    </option>
                @endforeach
            </select>
        </div>
    @elseif ($field['type'] === 'autocomplete')
        @php
            $acConfig = $field['autocomplete'] ?? [];
            $acUrl = $acConfig['url'] ?? '';
            $acTextField = $acConfig['textField'] ?? 'name';
            $acValueField = $acConfig['valueField'] ?? 'id';
            $acPlaceholder = $field['placeholder'] ?? '-- Cari dan pilih --';
            $acFill = $acConfig['fill'] ?? [];
        @endphp
        <div class="{{ $field['col'] ?? 'col-md-6' }} mb-2">
            <label>{{ $field['label'] }}
                @if (!empty($field['required']))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <select name="{{ $fieldName }}" id="{{ $fieldName }}"
                class="form-control autocomplete-select"
                data-ac-url="{{ $acUrl }}" data-ac-text-field="{{ $acTextField }}"
                data-ac-value-field="{{ $acValueField }}" data-ac-placeholder="{{ $acPlaceholder }}"
                data-ac-fill="{{ json_encode($acFill) }}" data-ac-hidden="{{ $field['nameValue'] ?? '' }}">
                @if ($fieldValue)
                    <option value="{{ $fieldValue }}" selected>{{ $fieldValue }}</option>
                @endif
            </select>
        </div>
    @elseif ($field['type'] === 'angka')
        @php
            $displayVal = $fieldValue !== '' ? number_format((float) $fieldValue, 0, ',', '.') : '';
        @endphp
        <div class="{{ $field['col'] ?? 'col-md-6' }} mb-2">
            <label>{{ $field['label'] }}
                @if (!empty($field['required']))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="text" id="{{ $fieldName }}_display" class="form-control"
                value="{{ $displayVal }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                oninput="autoNumericDot(this, '{{ $fieldName }}')" autocomplete="off">
            <input type="hidden" name="{{ $fieldName }}" id="{{ $fieldName }}" value="{{ $fieldValue }}">
        </div>
    @elseif ($field['type'] === 'textarea')
        <div class="{{ $field['col'] ?? 'col-md-6' }} mb-2">
            <label>{{ $field['label'] }}
                @if (!empty($field['required']))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control"
                placeholder="{{ $field['placeholder'] ?? '' }}">{{ $fieldValue }}</textarea>
        </div>
    @else
        {{-- date, text, email, number, dan tipe input lainnya --}}
        @php
            $inputType = in_array($field['type'], ['date', 'text', 'email', 'number', 'time', 'month']) ? $field['type'] : 'text';
        @endphp
        <div class="{{ $field['col'] ?? 'col-md-6' }} mb-2">
            <label>{{ $field['label'] }}
                @if (!empty($field['required']))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="{{ $inputType }}" name="{{ $fieldName }}" id="{{ $fieldName }}"
                class="form-control" value="{{ $fieldValue }}"
                placeholder="{{ $field['placeholder'] ?? '' }}">
        </div>
    @endif
@endforeach
    </div>
</div>

@if ($hasAutocomplete)
    @once
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const acSelects = [];

                    /**
                     * Batasi tinggi dropdown autocomplete agar tidak melewati footer.
                     */
                    function constrainSelect2Dropdown($select) {
                        const data = $select.data('select2');
                        if (!data) return;

                        const $dropdown = data.dropdown.$dropdown;
                        if (!$dropdown || !$dropdown.is(':visible')) return;

                        const $results = $dropdown.find('.select2-results__options');
                        if (!$results.length) return;

                        let limitBottom = window.innerHeight;
                        const $footer = $('footer').first();
                        if ($footer.length) {
                            limitBottom = Math.min(limitBottom, $footer[0].getBoundingClientRect().top);
                        }

                        const gap = 12;
                        const resultsTop = $results[0].getBoundingClientRect().top;
                        const available = Math.max(0, Math.floor(limitBottom - gap - resultsTop));

                        $results.css({
                            'max-height': available + 'px',
                            'overflow-y': 'auto'
                        });
                    }

                    document.querySelectorAll('.autocomplete-select').forEach(function(el) {
                        const url = el.dataset.acUrl;
                        const textField = el.dataset.acTextField || 'name';
                        const valueField = el.dataset.acValueField || 'id';
                        const placeholder = el.dataset.acPlaceholder || '-- Cari dan pilih --';
                        const fill = el.dataset.acFill ? JSON.parse(el.dataset.acFill) : {};
                        const $select = $(el);
                        const hiddenFieldName = el.dataset.acHidden || null;

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
                                data: function(params) {
                                    return {
                                        search: params.term || '',
                                        ...fill
                                    };
                                },
                                processResults: function(data) {
                                    const results = Array.isArray(data) ? data : (data.data || data
                                        .results || []);
                                    return {
                                        results: results.map(function(item) {
                                            return Object.assign({}, item, {
                                                id: item[valueField],
                                                text: item[textField]
                                            });
                                        })
                                    };
                                },
                                cache: true
                            },
                            minimumInputLength: 0,
                            templateResult: function(item) {
                                if (item.loading) {
                                    return $('<div class="select2-result-loading">' +
                                        '<i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</div>'
                                    );
                                }
                                return $('<div class="select2-result-item">' +
                                    '<i class="fas fa-tag mr-2 text-muted"></i>' +
                                    $('<span>').text(item.text).html() +
                                    '</div>');
                            },
                            templateSelection: function(item) {
                                if (!item.id) {
                                    return $('<span class="text-muted">' + placeholder + '</span>');
                                }
                                return $('<span class="select2-selection-text">' +
                                    '<i class="fas fa-check-circle mr-1 text-success"></i>' + item
                                    .text +
                                    '</span>');
                            },
                            language: {
                                searching: function() {
                                    return 'Mencari...';
                                },
                                noResults: function() {
                                    return '⛔ Data tidak ditemukan';
                                },
                                errorLoading: function() {
                                    return 'Gagal memuat data';
                                }
                            }
                        });

                        $select.on('select2:open', function() {
                            setTimeout(function() {
                                constrainSelect2Dropdown($select);
                            }, 0);

                            setTimeout(function() {
                                document.querySelector('.select2-search__field').focus();
                            }, 100);
                        });

                        if (hiddenFieldName) {
                            $select.on('select2:select', function(e) {
                                const item = e.params.data;
                                $('#' + hiddenFieldName).val(item.hiddenValue ?? '');
                            });

                            $select.on('select2:clear', function() {
                                $('#' + hiddenFieldName).val('');
                            });
                        }

                        acSelects.push($select);
                    });

                    $(window).on('resize', function() {
                        acSelects.forEach(function($select) {
                            constrainSelect2Dropdown($select);
                        });
                    });
                });
            </script>
        @endpush
    @endonce
@endif
