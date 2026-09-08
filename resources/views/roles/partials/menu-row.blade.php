{{--
    Baris menu pada halaman Atur Akses Menu (roles/menu.blade.php) — dirender rekursif,
    menampilkan turunan sampai level berapa pun dengan indent mengikuti kedalaman.

    Variabel dari menu.blade.php: $childMenus, $assignedMenuIds.
    Parameter include: $menu (wajib), $depth (default 1), $ancestors (default []) untuk
    penjaga cycle bila data mParentId membentuk loop.
--}}
@php
    $menuId   = $menu->mId;
    $children = $childMenus->get($menuId, collect());
    $indent   = 0.75 + (($depth - 1) * 1.5); // rem, padding-left sel level 2+
@endphp

<tr class="{{ $depth <= 1 ? 'table-secondary font-weight-bold' : '' }}">
    <td data-label="Pilih" @if ($depth > 1) style="padding-left: {{ $indent }}rem" @endif>
        <input type="checkbox" name="menu_ids[]" value="{{ $menuId }}"
               class="menu-checkbox"
               {{ in_array($menuId, $assignedMenuIds) ? 'checked' : '' }}>
    </td>
    <td data-label="Nama Menu" @if ($depth > 1) style="padding-left: {{ $indent }}rem" @endif>
        @if ($depth <= 1)
            <i class="fas fa-fw {{ $menu->mIcon ?: 'fa-folder' }}"></i>
        @else
            <span class="mr-1">&rdsh;</span>
        @endif
        {{ $menu->mNama }}
        @if (!$menu->mRoute && ($depth <= 1 || $children->isNotEmpty()))
            <span class="badge badge-warning">collapse</span>
        @endif
    </td>
    <td data-label="Route">
        {{ $menu->mRoute ?? ($depth <= 1 ? '— (tanpa route, collapse)' : '—') }}
    </td>
    <td data-label="Level">
        @if ($depth <= 1)
            <span class="badge badge-dark">Parent</span>
        @elseif ($depth == 2)
            <span class="badge badge-light">Child</span>
        @else
            <span class="badge badge-light">Level {{ $depth }}</span>
        @endif
    </td>
</tr>

@foreach ($children as $child)
    @if (!in_array($child->mId, $ancestors, true))
        @include('roles.partials.menu-row', [
            'menu'      => $child,
            'depth'     => $depth + 1,
            'ancestors' => array_merge($ancestors, [$menuId]),
        ])
    @endif
@endforeach
