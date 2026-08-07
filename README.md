# 🚀 CMS Real - Laravel 13

Panduan instalasi project Laravel 13 menggunakan Docker.

## Requirements

Pastikan sudah menginstall:

* Git
* Docker
* Docker Compose

---

## 1. Clone Repository

Clone repository dan pilih branch yang ingin digunakan.

```bash
git clone -b <branch> https://github.com/thoriqhafidz12/cms-real.git <nama_folder>
```

Contoh:

```bash
git clone -b develop https://github.com/thoriqhafidz12/cms-real.git cms-real
```

Masuk ke folder project.

```bash
cd <nama_folder>
```

---

## 2. Setup Environment

Salin file environment.

```bash
cp .env.example .env
```

Sesuaikan konfigurasi pada file `.env` apabila diperlukan, seperti:

* Database
* APP_URL
* Mail
* Redis
* dan konfigurasi lainnya

---

## 3. Jalankan Docker

Build image dan jalankan seluruh container.

```bash
docker compose up -d --build
```

Pastikan seluruh container berjalan.

```bash
docker ps
```

---

## 4. Masuk ke Container PHP

Cari ID atau nama container yang menjalankan Laravel.

```bash
docker ps
```

Masuk ke dalam container.

```bash
docker exec -it <id_container> /bin/bash
```

---

## 5. Generate Application Key

Di dalam container jalankan:

```bash
php artisan key:generate
```

---

## 6. Migrasi Database dan Seeder

Jalankan perintah berikut untuk membuat seluruh tabel dan mengisi data awal.

```bash
php artisan migrate:fresh --seed
```

---

## 🎉 Instalasi Selesai

Apabila seluruh proses berhasil, aplikasi Laravel siap dijalankan.

Jika menggunakan Docker Compose, aplikasi dapat diakses melalui URL yang telah dikonfigurasi pada file `.env` atau port yang diekspos pada `docker-compose.yml`.
