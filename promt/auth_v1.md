# PRD - Authentication Module

## Laravel 13 CMS Foundation

**Version:** 1.0

---

# 1. Overview

Membangun sistem Authentication sebagai pondasi utama CMS menggunakan Laravel 13 dengan arsitektur yang bersih (Clean Architecture), mudah dikembangkan, mudah di-debug, dan seluruh alur request selalu melalui Route → Middleware → Controller → Service → View.

Authentication bukan hanya berfungsi untuk login, tetapi menjadi fondasi seluruh sistem Authorization (Role Based Access Control) yang akan digunakan oleh seluruh modul CMS.

---

# 2. Objectives

Tujuan utama:

* Membuat Authentication yang aman.
* Seluruh request harus melewati Route Laravel.
* Menggunakan Middleware untuk proteksi halaman.
* Mudah dilakukan debugging.
* Mudah ditambahkan Role baru.
* Mudah ditambahkan Permission di masa depan.
* Tidak menggunakan shortcut atau bypass langsung ke view.

---

# 3. Technology Stack

Backend

* Laravel 13
* PHP 8.3

Frontend

* Blade
* Tailwind CSS
* Alpine.js (untuk interaksi ringan)

Database

* MySQL / MariaDB

Authentication

* Laravel Session Authentication

---

# 4. Authentication Flow

```text
Guest
    │
    ▼
GET /login
    │
    ▼
Login Form
    │
POST /login
    │
    ▼
Authentication
    │
    ▼
Session Created
    │
    ▼
Redirect Dashboard
    │
    ▼
Route
    │
Middleware
    │
Controller
    │
Service
    │
View
```

Seluruh halaman setelah login WAJIB melewati:

Route

↓

Middleware

↓

Controller

↓

Service

↓

Blade View

Tidak diperbolehkan mengakses View secara langsung.

---

# 5. Directory Structure

```text
app/

    Http/
        Controllers/
            Auth/
                LoginController.php
                LogoutController.php

            Dashboard/
                DashboardController.php

        Middleware/
            Authenticate.php
            RoleMiddleware.php

    Services/
        Auth/
            LoginService.php

resources/

    views/

        auth/
            login.blade.php

        dashboard/
            index.blade.php

routes/

    web.php
```

---

# 6. Authentication Process

## Login Page

URL

```
GET /login
```

Menampilkan:

* Username / Email
* Password
* Remember Me
* Login Button

---

## Login Process

```
POST /login
```

Flow:

Validate Request

↓

Authenticate

↓

Regenerate Session

↓

Save User Session

↓

Redirect Dashboard

Jika gagal:

↓

Redirect Login

↓

Flash Error

---

## Logout

```
POST /logout
```

Flow

Destroy Session

↓

Invalidate Session

↓

Regenerate CSRF

↓

Redirect Login

---

# 7. Route Architecture

Semua endpoint harus berada pada Route.

Contoh:

```php
Route::middleware('guest')->group(function () {

    Route::get('/login', ...);

    Route::post('/login', ...);

});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', ...);

});

Route::middleware(['auth','role:admin'])->group(function () {

    Route::resource('users', UserController::class);

});
```

Tidak diperbolehkan memanggil View tanpa Route.

---

# 8. Middleware Layer

Middleware bertugas melakukan validasi akses.

Flow:

```
Request

↓

Route

↓

Authenticate Middleware

↓

Role Middleware

↓

Controller
```

Jenis Middleware:

## Authenticate

Memastikan user sudah login.

Jika belum login

↓

Redirect Login

---

## Role Middleware

Memastikan role user sesuai.

Misal

```
admin

staff

manager

superadmin
```

Jika role tidak sesuai

↓

403 Forbidden

---

# 9. Authorization

Menggunakan Role Based Access Control.

Contoh:

```
Super Admin

↓

Admin

↓

Manager

↓

Staff

↓

Guest
```

Role akan digunakan pada:

* Middleware
* Menu
* Dashboard
* Module Access

Permission akan dikembangkan pada fase berikutnya.

---

# 10. Dashboard Flow

```
Login

↓

Authenticated

↓

DashboardController

↓

DashboardService

↓

Dashboard View
```

Dashboard tidak boleh mengambil data langsung dari View.

---

# 11. Controller Responsibility

Controller hanya bertugas:

* menerima Request
* validasi sederhana
* memanggil Service
* mengembalikan Response

Controller tidak boleh berisi Business Logic.

---

# 12. Service Responsibility

Business Logic berada di Service.

Contoh:

```
LoginService

DashboardService

UserService

RoleService
```

Keuntungan:

* mudah unit testing
* reusable
* debugging mudah

---

# 13. Session Management

Saat Login:

* Regenerate Session
* Simpan User ID
* Simpan Role
* Simpan Login Time

Saat Logout:

* Destroy Session
* Regenerate Token
* Redirect Login

---

# 14. Security

Wajib menggunakan:

* CSRF Protection
* Session Regeneration
* Password Hashing (Laravel Hash)
* Validation Request
* Middleware Authentication
* Middleware Authorization
* HTTPS Ready

---

# 15. UI Design

Login Page

Komponen:

* Logo
* Judul CMS
* Username / Email
* Password
* Remember Me
* Login Button
* Error Message
* Loading State

Style

* Modern
* Minimal
* Responsive
* Tailwind CSS

---

# 16. Error Handling

401

Belum login

↓

Redirect Login

403

Tidak memiliki akses

↓

Forbidden Page

404

Halaman tidak ditemukan

↓

404 Page

419

CSRF Expired

↓

Redirect Login

500

Internal Error

↓

Error Page

---

# 17. Debugging Philosophy

Seluruh request harus mudah ditelusuri.

Flow debugging:

```
Browser

↓

Route

↓

Middleware

↓

Controller

↓

Service

↓

Model

↓

Database
```

Tidak diperbolehkan:

* langsung memanggil View
* Business Logic di Blade
* Query Database di Blade
* Logic kompleks di Controller

---

# 18. Future Scalability

Authentication harus menjadi fondasi untuk modul berikut:

* User Management
* Role Management
* Permission Management
* Audit Log
* Activity Log
* Profile
* Notification
* Multi Company
* Multi Branch
* API Authentication (Sanctum)
* Single Sign-On (SSO)

Tanpa mengubah arsitektur utama.

---

# 19. Success Criteria

Authentication dianggap berhasil apabila:

* Login berhasil menggunakan Session Authentication.
* Seluruh halaman hanya dapat diakses melalui Route.
* Middleware berhasil membatasi akses berdasarkan status login dan role.
* Dashboard hanya dapat diakses oleh user yang telah terautentikasi.
* Struktur Controller tetap tipis, dengan Business Logic berada di Service.
* UI Login responsif menggunakan Blade + Tailwind CSS.
* Arsitektur siap dikembangkan menjadi CMS/ERP berskala besar tanpa perubahan fundamental.
