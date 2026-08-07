# 🚀 Laravel 13 Installation Guide

Panduan instalasi project **CMS Real** menggunakan Docker.

## Prerequisites

Pastikan perangkat telah terinstall:

* PHP 8.3+ (untuk Composer)
* Composer
* Docker
* Docker Compose
* Git

---

## 1. Clone Repository

Clone project sesuai branch yang ingin digunakan.

```bash
git clone -b <branch> https://github.com/thoriqhafidz12/cms-real.git <nama_folder>
```

Contoh:

```bash
git clone -b development https://github.com/thoriqhafidz12/cms-real.git cms-real
```

Masuk ke folder project.

```bash
cd <nama_folder>
```

---

## 2. Install Dependency

Jalankan salah satu perintah berikut:

```bash
composer install
```

atau

```bash
composer update
```

> **Disarankan menggunakan `composer install`** apabila file `composer.lock` tersedia agar dependency sesuai dengan versi project.

---

## 3. Setup Environment

Salin file environment.

```bash
cp .env.example .env
```

Kemudian sesuaikan konfigurasi pada file `.env` apabila diperlukan, seperti:

* Database
* Redis
* Mail
* APP_URL
* dan konfigurasi lainnya

---

## 4. Build dan Jalankan Docker

```bash
docker compose up -d --build
```

Pastikan seluruh container berhasil berjalan.

```bash
docker ps
```

---

## 5. Masuk ke Container PHP

Cari ID atau nama container terlebih dahulu.

```bash
docker ps
```

Masuk ke dalam container.

```bash
docker exec -it <id_container> /bin/bash
```

---

## 6. Generate Application Key

Di dalam container jalankan:

```bash
php artisan key:generate
```

---

## 7. Jalankan Migration dan Seeder

Masih di dalam container, jalankan:

```bash
php artisan migrate:fresh --seed
```

---

## ✅ Instalasi Selesai

Apabila seluruh proses berhasil, aplikasi siap dijalankan.

Untuk memastikan aplikasi berjalan dengan baik:

```bash
php artisan optimize
```

Kemudian akses aplikasi melalui URL yang telah dikonfigurasi pada Docker atau `APP_URL`.

---

## Troubleshooting

### Permission Error

Jika mengalami masalah permission pada folder `storage` atau `bootstrap/cache`, jalankan:

```bash
chmod -R 775 storage bootstrap/cache
```

---

### Container Tidak Berjalan

Cek status container:

```bash
docker ps -a
```

Lihat log container:

```bash
docker compose logs -f
```

---

### Migration Gagal

Pastikan:

* Database sudah aktif.
* Konfigurasi `.env` sudah benar.
* Container database telah berjalan sebelum menjalankan migration.

---

## Development Workflow

Apabila terdapat perubahan pada Dockerfile atau konfigurasi service:

```bash
docker compose down
docker compose up -d --build
```

Untuk melihat log aplikasi:

```bash
docker compose logs -f
```