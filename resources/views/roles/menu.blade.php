@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Role Menu: <span class="text-primary">{{ $role->rNama }}</span>
        </h1>
        <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Atur Akses Menu</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAll()">
                    <i class="fas fa-check-square"></i> Check All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAll()">
                    <i class="fas fa-square"></i> Uncheck All
                </button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.updateMenu', $role->rId) }}" method="POST" id="menuForm">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-bordered table-mobile-card" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="checkAllToggle" onchange="toggleAll(this)">
                                </th>
                                <th>Nama Menu</th>
                                <th>Route</th>
                                <th>Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parentMenus as $menu)
                                @php
                                    $children = $childMenus->get($menu->mId, collect());
                                @endphp

                                {{-- Parent Row --}}
                                <tr class="table-secondary font-weight-bold">
                                    <td data-label="Pilih">
                                        <input type="checkbox" name="menu_ids[]" value="{{ $menu->mId }}"
                                               class="menu-checkbox"
                                               {{ in_array($menu->mId, $assignedMenuIds) ? 'checked' : '' }}>
                                    </td>
                                    <td data-label="Nama Menu">
                                        <i class="fas fa-fw {{ $menu->mIcon ?: 'fa-folder' }}"></i>
                                        {{ $menu->mNama }}
                                        @if (!$menu->mRoute)
                                            <span class="badge badge-warning">collapse</span>
                                        @endif
                                    </td>
                                    <td data-label="Route">{{ $menu->mRoute ?? '— (tanpa route, collapse)' }}</td>
                                    <td data-label="Level"><span class="badge badge-dark">Parent</span></td>
                                </tr>

                                {{-- Child Rows --}}
                                @foreach ($children as $child)
                                    <tr>
                                        <td class="pl-5" data-label="Pilih">
                                            <input type="checkbox" name="menu_ids[]" value="{{ $child->mId }}"
                                                   class="menu-checkbox"
                                                   {{ in_array($child->mId, $assignedMenuIds) ? 'checked' : '' }}>
                                        </td>
                                        <td data-label="Nama Menu">
                                            <span class="ml-3">&rdsh; {{ $child->mNama }}</span>
                                        </td>
                                        <td data-label="Route">{{ $child->mRoute ?? '—' }}</td>
                                        <td data-label="Level"><span class="badge badge-light">Child</span></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Akses Menu
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function checkAll() {
        document.querySelectorAll('.menu-checkbox').forEach(cb => cb.checked = true);
        document.getElementById('checkAllToggle').checked = true;
    }

    function uncheckAll() {
        document.querySelectorAll('.menu-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('checkAllToggle').checked = false;
    }

    function toggleAll(el) {
        document.querySelectorAll('.menu-checkbox').forEach(cb => cb.checked = el.checked);
    }
</script>
@endpush
