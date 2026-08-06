# Rules: Cara Menambah Menu Baru

Dokumen ini menjelaskan langkah-langkah yang harus dilakukan ketika ingin menambahkan menu baru ke sidebar CMS.

---

## Daftar Isi

1. [Konsep Menu](#1-konsep-menu)
2. [Langkah-Langkah Menambah Menu](#2-langkah-langkah-menambah-menu)
3. [Jenis-Jenis Menu](#3-jenis-jenis-menu)
4. [Referensi Field Tabel `menu`](#4-referensi-field-tabel-menu)
5. [Contoh Lengkap](#5-contoh-lengkap)
6. [Checklist](#6-checklist)

---

## 1. Konsep Menu

Menu di sidebar dirender dari tabel `menu` di database. Setiap menu memiliki relasi many-to-many ke tabel `role` melalui pivot table `role_menu`, sehingga setiap role bisa diberikan akses ke menu tertentu.

### Struktur Menu

```
Sidebar
├── Dashboard              ← standalone link  (mRoute = 'dashboard',  mParentId = null)
└── Management  ▼          ← collapse parent   (mRoute = null,        mParentId = null)
    ├── Users              ← child item        (mRoute = 'users',     mParentId = 2)
    ├── Role               ← child item        (mRoute = 'roles',     mParentId = 2)
    └── Menu               ← child item        (mRoute = 'menus',     mParentId = 2)
```

### File yang Terlibat

| File | Fungsi |
|---|---|
| `database/migrations/..._create_menu_table.php` | Struktur tabel menu |
| `database/migrations/..._create_role_menu_table.php` | Pivot table role-menu |
| `app/Models/Menu.php` | Model Menu |
| `app/Models/Role.php` | Model Role (ada method `menus()`) |
| `app/Http/Controllers/BaseController.php` | Base controller untuk CRUD |
| `resources/views/layouts/app.blade.php` | Sidebar render logic |
| `database/seeders/DatabaseSeeder.php` | Data awal menu |
| `routes/web.php` | Route definitions |

---

## 2. Langkah-Langkah Menambah Menu

### Step 1: Tentukan Route yang Dibutuhkan

Menu membutuhkan nama route Laravel. Pastikan route sudah terdaftar di `routes/web.php`.

**Contoh route yang sudah ada:**

```php
// routes/web.php

// Route sederhana
Route::get('/dashboard', function () { ... })->name('dashboard');

// Resource route (otomatis menghasilkan users.index, users.create, dll.)
Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
```

**Rule nama route:**
- Route sederhana: `dashboard` → `route('dashboard')`
- Route resource: `users` → `route('users.index')` (prefix `.index` ditambahkan otomatis oleh sidebar)
- Route tidak boleh pakai spasi atau karakter khusus

> **Catatan**: Sidebar akan otomatis resolve prefix route. Jika `Route::has('users')` tidak ditemukan, sidebar akan mencoba `Route::has('users.index')`.

---

### Step 2: Buat Controller (Jika Diperlukan)

Jika menu baru membutuhkan CRUD, buat controller yang mengextends `BaseController`.

**Template controller:**

```php
<?php

namespace App\Http\Controllers;

use App\Models\NamaModel;

class NamaController extends BaseController
{
    public function __construct()
    {
        $this->model = NamaModel::class;      // Model class
        $this->route = 'nama_route';          // Prefix route (harus sama dengan routes/web.php)
        $this->titlePage = 'Judul Halaman';   // Judul halaman index
        $this->primaryKey = 'id';             // Primary key tabel
        $this->table = 'nama_tabel';          // Nama tabel untuk unique validation
        $this->searchColumn = 'nama_kolom';   // Kolom untuk search

        // Definisikan field form (kanan di halaman index)
        $this->form = [
            [
                'name'        => 'field_name',
                'label'       => 'Label Field',
                'placeholder' => 'Placeholder',
                'type'        => 'text',      // 'text' | 'email' | 'password' | 'number' | 'select'
                'col'         => 'col-md-12',
                'required'    => true,
                'unique'      => 'table,column', // optional, format: "table,column"
            ],
            // ... tambah field lain
        ];

        // Data tambahan untuk view (misal: dropdown options)
        $this->extraViewData = [
            'nama_variabel' => fn () => SomeModel::orderBy('name')->get(),
        ];
    }

    // Hook: modifikasi data sebelum insert (opsional)
    protected function beforeSave(array $data, $record = null): array
    {
        $data['created_by'] = auth()->user()->name;
        return $data;
    }

    // Hook: modifikasi data sebelum update (opsional)
    protected function beforeUpdate(array $data, $record): array
    {
        $data['updated_by'] = auth()->user()->name;
        return $data;
    }
}
```

---

### Step 3: Daftarkan Route di `routes/web.php`

Tambahkan resource route di dalam group `auth`:

```php
Route::middleware('auth')->group(function () {
    // ... routes yang sudah ada

    Route::resource('nama_route', NamaController::class);  // ← tambah ini
});
```

> **PENTING**: Urutan penulisan route berpengaruh. Jika ada custom route (seperti `roles.menu`), tulis sebelum `Route::resource()`.

---

### Step 4: Tambah Data Menu ke Database

Ada **3 cara** untuk menambah menu:

#### Cara A: Via Seeder (Rekomendasi untuk data awal)

Edit `database/seeders/DatabaseSeeder.php`:

```php
// Menu baru — standalone (link langsung)
Menu::create([
    'mId'        => 6,                    // ID unique (cek ID terakhir, increment)
    'mNama'      => 'Nama Menu',          // Nama yang muncul di sidebar
    'mRoute'     => 'nama_route',         // Prefix nama route (atau null untuk collapse)
    'mParentId'  => null,                 // null jika top-level, isi mId parent jika child
    'mIcon'      => 'fa-icon-name',       // Font Awesome icon class (e.g. fa-cog)
    'mOrder'     => 1,                    // Urutan di dalam parent
    'mIsActive'  => 1,                    // 1 = tampil, 0 = sembunyi
    'mCreatedBy' => 'Seeder',
    'mUpdatedBy' => 'Seeder',
    'mCreatedAt' => now(),
    'mUpdatedAt' => now(),
]);
```

Kemudian assign menu ke role via pivot table:

```php
// Assign ke Admin role (rId=1)
\Illuminate\Support\Facades\DB::table('role_menu')->insert([
    'rmRoleId'    => 1,              // ID role
    'rmMenuId'    => 6,              // ID menu yang baru dibuat
    'rmCreatedAt' => now(),
    'rmUpdatedAt' => now(),
]);
```

#### Cara B: Via Halaman Admin (Menu Management)

1. Login sebagai admin
2. Buka sidebar **Management > Menu**
3. Isi form di sebelah kanan:
   - **Nama Menu**: Nama yang muncul di sidebar
   - **Route Prefix**: Nama route Laravel (kosongkan jika collapse)
   - **Icon**: Font Awesome class (e.g. `fa-cog`)
   - **Urutan**: Angka urutan
   - **Parent Menu**: Pilih parent jika child, kosongkan jika top-level
4. Klik **Simpan**
5. Buka **Management > Role > Menu** (tombol biru di row Admin)
6. Centang menu yang baru dibuat
7. Klik **Simpan Akses Menu**

#### Cara C: Via Database Seeder Command

```bash
php artisan make:seeder TambahMenuBaruSeeder
```

Lalu isi dengan data menu, dan jalankan:

```bash
php artisan db:seed --class=TambahMenuBaruSeeder
```

---

### Step 5: Assign Menu ke Role

Setiap role harus diberikan akses ke menu. Tanpa ini, menu tidak akan muncul di sidebar.

**Via halaman admin:**
1. Buka **Management > Role**
2. Klik tombol **Menu** (biru, icon `☰`) pada row role yang ingin diberi akses
3. Centang menu-menu yang boleh diakses role tersebut
4. Klik **Simpan Akses Menu**

**Via seeder:**
```php
DB::table('role_menu')->insert([
    'rmRoleId' => $roleId,
    'rmMenuId' => $menuId,
    'rmCreatedAt' => now(),
    'rmUpdatedAt' => now(),
]);
```

---

### Step 6: Buat View (Jika Diperlukan)

Untuk controller yang extends `BaseController`, halaman index (`resources/views/nama_route/index.blade.php`) otomatis menggunakan two-column layout. Gunakan template berikut:

```blade
@extends('layouts.app')

@section('content')
    <div class="row">
        {{-- Left: Table Listing --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $titlePage }}</h6>
                </div>
                <div class="card-body">
                    {{-- Search --}}
                    <form method="GET" action="{{ route($route . '.index') }}" class="mb-3">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="search" class="form-control bg-light border-0 small"
                                   placeholder="Cari ..." value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr class="{{ isset($editData) && $editData->{$primaryKey} == $item->{$primaryKey} ? 'table-warning' : '' }}">
                                        <td>{{ $loop->iteration + $items->firstItem() - 1 }}</td>
                                        <td>{{ $item->nama_field }}</td>
                                        <td>
                                            <a href="{{ route($route . '.index', ['edit' => $item->{$primaryKey}]) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route($route . '.destroy', $item->{$primaryKey}) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ isset($editData) ? 'Edit Data' : 'Tambah Data' }}
                    </h6>
                </div>
                <div class="card-body">
                    @include('components.crud-form')
                </div>
            </div>
        </div>
    </div>
@endsection
```

> **Catatan**: File ini disimpan di `resources/views/{nama_route}/index.blade.php`. Pastikan nama folder = prefix route.

---

## 3. Jenis-Jenis Menu

### A. Standalone Link (Single Menu)

Menu yang langsung mengarah ke halaman tertentu. Tidak punya anak.

```php
[
    'mNama'     => 'Dashboard',
    'mRoute'    => 'dashboard',    // ← nama route Laravel
    'mParentId' => null,           // ← null = top-level
    'mIcon'     => 'fa-tachometer-alt',
    'mOrder'    => 1,
    'mIsActive' => 1,
]
```

**Syarat**: `mRoute` harus diisi dengan nama route yang valid.

**Hasil di sidebar**: `<a href="/dashboard"> Dashboard</a>`

---

### B. Collapse Parent (Dropdown Menu)

Menu yang berfungsi sebagai header dropdown. Tidak memiliki link sendiri, hanya toggle untuk membuka/tutup anak-anaknya.

```php
[
    'mNama'     => 'Management',
    'mRoute'    => null,           // ← null = tidak ada link, hanya collapse
    'mParentId' => null,           // ← null = top-level
    'mIcon'     => 'fa-cogs',
    'mOrder'    => 2,
    'mIsActive' => 1,
]
```

**Syarat**: `mRoute` harus **null**. Menu ini otomatis menjadi collapse.

**Hasil di sidebar**: Toggle dropdown yang bisa diklik untuk membuka anak-anaknya.

---

### C. Child Menu (Item di Dalam Collapse)

Menu yang berada di dalam collapse parent. Didefinisikan dengan mengisi `mParentId`.

```php
[
    'mNama'     => 'Users',
    'mRoute'    => 'users',        // ← nama route
    'mParentId' => 2,              // ← mId dari parent (Management)
    'mIcon'     => '',
    'mOrder'    => 1,              // ← urutan di dalam collapse
    'mIsActive' => 1,
]
```

**Syarat**: `mParentId` harus diisi dengan `mId` dari menu parent.

**Hasil di sidebar**: Item di dalam collapse, tampil saat collapse dibuka.

---

## 4. Referensi Field Tabel `menu`

| Field | Tipe | Keterangan |
|---|---|---|
| `mId` | bigint (PK) | Auto-increment primary key |
| `mNama` | string | Nama menu yang tampil di sidebar |
| `mRoute` | string/null | Nama route Laravel. **null** → collapse parent |
| `mParentId` | bigint/null | **null** → top-level. Diisi `mId` parent → child menu |
| `mIcon` | string/null | Font Awesome class tanpa `fa-`. Contoh: `fa-cog` → ketik `fa-cog` |
| `mOrder` | integer | Urutan menu (ascending). Dalam parent & antar parent |
| `mIsActive` | integer | `1` = tampil, `0` = sembunyi |
| `mCreatedAt` | datetime | Auto timestamp |
| `mCreatedBy` | string | Nama pembuat |
| `mUpdatedAt` | datetime | Auto timestamp |
| `mUpdatedBy` | string | Nama pengupdate |

---

## 5. Contoh Lengkap

### Studi Kasus: Menambah Menu "Artikel"

Kita ingin menambah menu **Artikel** sebagai child dari **Management**, dengan CRUD sendiri.

#### 5.1 Buat Model & Migration

```bash
php artisan make:model Artikel -m
```

Edit migration, tambahkan kolom yang dibutuhkan.

#### 5.2 Buat Controller

```php
// app/Http/Controllers/ArtikelController.php

namespace App\Http\Controllers;

use App\Models\Artikel;

class ArtikelController extends BaseController
{
    public function __construct()
    {
        $this->model = Artikel::class;
        $this->route = 'artikels';
        $this->titlePage = 'Daftar Artikel';
        $this->primaryKey = 'id';
        $this->table = 'artikels';
        $this->searchColumn = 'judul';

        $this->form = [
            [
                'name'        => 'judul',
                'label'       => 'Judul Artikel',
                'placeholder' => 'Masukkan judul artikel',
                'type'        => 'text',
                'col'         => 'col-md-12',
                'required'    => true,
                'unique'      => 'artikels,judul',
            ],
        ];
    }
}
```

#### 5.3 Daftarkan Route

```php
// routes/web.php — di dalam group auth
Route::resource('artikels', ArtikelController::class);
```

#### 5.4 Buat View

Buat file `resources/views/artikels/index.blade.php` (gunakan template dari [Step 6](#step-6-buat-view-jika-diperlukan)).

#### 5.5 Tambah Data Menu di Seeder

```php
// database/seeders/DatabaseSeeder.php

Menu::create([
    'mId'        => 6,
    'mNama'      => 'Artikel',
    'mRoute'     => 'artikels',
    'mParentId'  => 2,             // masuk ke Management
    'mIcon'      => 'fa-newspaper',
    'mOrder'     => 4,             // setelah Menu (order 3)
    'mIsActive'  => 1,
    'mCreatedBy' => 'Seeder',
    'mUpdatedBy' => 'Seeder',
    'mCreatedAt' => now(),
    'mUpdatedAt' => now(),
]);

// Assign ke Admin
DB::table('role_menu')->insert([
    'rmRoleId'    => 1,
    'rmMenuId'    => 6,
    'rmCreatedAt' => now(),
    'rmUpdatedAt' => now(),
]);
```

#### 5.6 Jalankan Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

---

## 6. Checklist

Sebelum commit, pastikan semua item ini sudah dilakukan:

- [ ] Route terdaftar di `routes/web.php` dalam group `auth`
- [ ] Controller extends `BaseController` dengan property lengkap (`$model`, `$route`, `$titlePage`, `$primaryKey`, `$table`, `$searchColumn`, `$form`)
- [ ] View `index.blade.php` ada di `resources/views/{route}/`
- [ ] Data menu ditambahkan di `DatabaseSeeder` (atau via halaman admin)
- [ ] Menu di-assign ke Role di tabel `role_menu` (via seeder atau halaman Role Menu)
- [ ] `mRoute` sesuai dengan nama route Laravel (cek dengan `php artisan route:list`)
- [ ] `mParentId` diisi dengan benar (null untuk top-level, `mId` parent untuk child)
- [ ] `mOrder` unik dan terurut dengan benar
- [ ] `mIsActive = 1`
- [ ] Test: login → sidebar muncul → klik menu → halaman terbuka
- [ ] Test: buka Role Menu → centang/uncheck → sidebar terupdate
