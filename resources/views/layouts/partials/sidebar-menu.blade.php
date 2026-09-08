{{--
    Item menu sidebar — dirender rekursif, mendukung collapse di dalam collapse (level 2+).
    Level 1 : <li class="nav-item"> + Bootstrap collapse (data-parent="#accordionSidebar")
    Level 2+: toggle "submenu" (ditangani custom.js) di dalam collapse-inner induknya.
              Sengaja TIDAK memakai class .collapse agar tidak ikut kena aturan
              flyout sb-admin-2 (.sidebar .nav-item .collapse → position:absolute).

    Variabel dari app.blade.php: $childMenus, $resolveRoute, $isMenuActive.
    Parameter include: $menu (wajib), $depth (default 1), $ancestors (default []) untuk
    penjaga cycle bila data mParentId membentuk loop.
--}}
@php
    $menuId      = $menu->mId;
    $children    = $childMenus->get($menuId, collect());
    $routeName   = $resolveRoute($menu->mRoute);
    $hasChildren = $children->isNotEmpty();
    $isCollapse  = $hasChildren || !$routeName;
    $activeTree  = $isMenuActive($menu);                               // menu ini ATAU turunannya aktif
    $activeSelf  = $routeName && request()->routeIs($routeName . '*'); // hanya link menu ini sendiri
@endphp

@if ($depth <= 1)
    @if ($isCollapse)
        {{-- Level 1: Collapse / Dropdown Menu --}}
        <li class="nav-item {{ $activeTree ? 'active' : '' }}">
            <a class="nav-link {{ $activeTree ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
                data-target="#collapseMenu{{ $menuId }}" aria-expanded="{{ $activeTree ? 'true' : 'false' }}"
                aria-controls="collapseMenu{{ $menuId }}">
                <i class="fas fa-fw {{ $menu->mIcon ?: 'fa-folder' }}"></i>
                <span>{{ $menu->mNama }}</span>
            </a>
            <div id="collapseMenu{{ $menuId }}" class="collapse {{ $activeTree ? 'show' : '' }}"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    @if ($routeName)
                        {{-- Parent juga punya link sendiri --}}
                        <a class="collapse-item {{ $activeSelf ? 'active' : '' }}" href="{{ route($routeName) }}">
                            {{ $menu->mNama }}
                        </a>
                    @endif
                    @foreach ($children as $child)
                        @if (!in_array($child->mId, $ancestors, true))
                            @include('layouts.partials.sidebar-menu', [
                                'menu'      => $child,
                                'depth'     => $depth + 1,
                                'ancestors' => array_merge($ancestors, [$menuId]),
                            ])
                        @endif
                    @endforeach
                </div>
            </div>
        </li>
    @else
        {{-- Level 1: Single Link Menu --}}
        <li class="nav-item {{ $activeTree ? 'active' : '' }}">
            <a class="nav-link" href="{{ route($routeName) }}">
                <i class="fas fa-fw {{ $menu->mIcon }}"></i>
                <span>{{ $menu->mNama }}</span>
            </a>
        </li>
    @endif
@else
    {{-- Level 2+: collapse di dalam collapse --}}
    @if ($isCollapse)
        <a class="collapse-item submenu-toggle {{ $activeTree ? '' : 'collapsed' }}" href="#"
            data-toggle="submenu" data-target="#submenu{{ $menuId }}"
            aria-expanded="{{ $activeTree ? 'true' : 'false' }}" aria-controls="submenu{{ $menuId }}">
            <span>{{ $menu->mNama }}</span>
        </a>
        <div id="submenu{{ $menuId }}" class="submenu {{ $activeTree ? 'show' : '' }}">
            <div class="submenu-inner">
                @if ($routeName)
                    {{-- Submenu juga punya link sendiri --}}
                    <a class="collapse-item {{ $activeSelf ? 'active' : '' }}" href="{{ route($routeName) }}">
                        {{ $menu->mNama }}
                    </a>
                @endif
                @foreach ($children as $child)
                    @if (!in_array($child->mId, $ancestors, true))
                        @include('layouts.partials.sidebar-menu', [
                            'menu'      => $child,
                            'depth'     => $depth + 1,
                            'ancestors' => array_merge($ancestors, [$menuId]),
                        ])
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <a class="collapse-item {{ $activeSelf ? 'active' : '' }}"
            href="{{ $routeName ? route($routeName) : '#' }}">
            {{ $menu->mNama }}
        </a>
    @endif
@endif
