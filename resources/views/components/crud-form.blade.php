{{-- Dynamic CRUD Form — digunakan di index page kanan --}}
@php
    $isEdit = isset($editData) && $editData;
    $action = $isEdit ? route($route . '.update', $editData->{$primaryKey}) : route($route . '.store');
@endphp

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
