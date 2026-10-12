# Panduan Flow & Konfigurasi Database — CMS Koperasi

> Dokumen ini menjelaskan **semua flow aplikasi dan artinya di database**, terutama bagian mana yang bisa diubah **cukup lewat data/konfigurasi di database (atau menu Master)** tanpa menyentuh kode.
>
> Status kolom/tabel: ✅ = **dibaca sistem** (mengubahnya berpengaruh), ⚠️ = **belum dibaca sistem** (data tersimpan, tapi perilaku belum terpengaruh).

---

## 1. Konsep Dasar

### 1.1 Satu sumber kebenaran: tabel `jurnal`

Semua laporan (Jurnal, Buku Besar, **Neraca**) membaca **satu tabel saja: `jurnal`**. Setiap transaksi (penerimaan, pengeluaran, saldo awal, pinjaman) otomatis menulis baris jurnal lewat `BaseController::createJurnal()`.

> **Aturan penting:** jangan edit tabel `jurnal` langsung. Perbaiki lewat menu transaksinya (Penerimaan/Pengeluaran/Pencairan/Pembayaran), karena edit/delete transaksi akan **menghapus & membuat ulang jurnal-nya** otomatis.

**Kolom `jurnal`:**

| Kolom | Arti |
|---|---|
| `jHeadId` | ID header transaksi asal: `tId` (penerimaan), `kId` (pengeluaran), `tsId` (saldo awal) |
| `jNo` | Nomor bukti transaksi asal |
| `jTgl` | Tanggal transaksi |
| `jKeterangan` | Uraian |
| `jRekDebetKode` / `jRekDebetNama` | Kode & nama **objek COA** sisi debet |
| `jDebetNilai` / `jKreditNilai` | Nilai debet / kredit |
| `jRekKreditKode` / `jRekKreditNama` | Kode & nama **objek COA** sisi kredit |
| `jSumber` | Asal transaksi: `M-TERIMA`, `M-KELUAR`, `SA-BANKKAS`, `BAYAR PINJAMAN`, `PENCAIRAN PINJAMAN` |
| `jStatus` | ⚠️ `0` = belum closing, `1` = closing (belum ada proses closing di aplikasi) |

### 1.2 Chart of Account (COA) — 4 level

Struktur akun dikendalikan penuh dari menu **Master → Data COA**:

| Tabel | Kode contoh | Arti |
|---|---|---|
| `ms_akun` | `1` | AKTIVA, `2` HUTANG, `3` MODAL/EKUITAS, `4` PENDAPATAN, `5` BEBAN |
| `ms_kelompok` | `1.01` | AKTIVA LANCAR, `1.02` AKTIVA TETAP, `2.01` HUTANG LANCAR, `3.01` EKUITAS, dst. |
| `ms_jenis` | `1.01.01` | KAS, BANK, PIUTANG, dst. |
| `ms_objek` | `1.01.01.01` | KAS BENDAHARA, dst. (level paling bawah — **yang dipakai jurnal & mapping**) |

- `ms_objek` menyimpan seluruh rantai induknya (`msoAkunKode`, `msoKelompokKode`, `msoJenisKode`) → laporan Neraca mengelompokkan otomatis berdasarkan kode.
- **Kode objek = penghubung antar tabel** (`jurnal.jRekDebetKode`, `mapping_*.mapKodeAsal`, `ms_bankkas.msbkObjekKd`, `ms_neraca.nrKodeAkun`). Kode harus **unik** per level.

### 1.3 Mapping = konfigurasi "transaksi ini debet apa, kredit apa"

Tiga tabel mapping menentukan **pasangan debet/kredit** setiap jenis transaksi. `mapKodeAsal` adalah "kunci" yang dipilih saat input transaksi.

| Tabel | Dipakai untuk | Posisi kunci |
|---|---|---|
| `mapping_penerimaan` | Penerimaan & Pembayaran pinjaman | `tr_terima.tCoa` |
| `mapping_pengeluaran` | Pengeluaran & Pencairan pinjaman | `tr_keluar.kCoa` |
| `mapping_saldoawal` | Saldo awal bank/kas | `ms_bankkas.msbkObjekKd` |

Kolom tiap mapping: `mapKodeAsal`, `mapNamaAsal`, `mapKodeDebet`, `mapNamaDebet`, `mapKodeKredit`, `mapNamaKredit`, `mapStatusPenagihan` ⚠️ (kolom ini **tidak dibaca** sistem — perilaku debet/kredit murni dari `mapKodeDebet`/`mapKodeKredit` yang Anda isi).

> **Cara mengubah posisi akun suatu transaksi:** ubah `mapKodeDebet` / `mapKodeKredit` pada baris mapping dengan `mapKodeAsal` yang sesuai — jurnal transaksi berikutnya langsung mengikuti.

---

## 2. Menu & Hak Akses

| Tabel | Arti |
|---|---|
| `menu` | Struktur sidebar. `mRoute` = nama route Laravel (mis. `rpt-neraca`). `mRoute = NULL` = dropdown tanpa link. `mParentId` = induk menu. `mOrder` = urutan. `mIsActive` = `1` tampil / `0` sembunyi |
| `role` | Daftar role (Admin, dst.) |
| `role_menu` | Hak akses role → menu (`rmRoleId`, `rmMenuId`). Tidak ada akses = route tersebut 403 |
| `users` | `role` kolom = ID role user |

- Middleware `checkRole`: route yang namanya **ada** di tabel `menu` wajib punya akses di `role_menu`; route yang **tidak ada** di `menu` bisa diakses semua user login (tapi tidak muncul di sidebar).
- Panduan lengkap menambah menu: lihat [`docs/tambah_menu.md`](tambah_menu.md).

---

## 3. Flow Master Bank/Kas & Saldo Awal

**Tabel:** `ms_bankkas` (master bank/kas) → `tr_saldoawal` (riwayat saldo awal) → `jurnal`.

1. Simpan bank/kas baru di **Master → Master Bank Kas** → sistem otomatis membuat 1 baris `tr_saldoawal` + jurnal.
2. Jurnal dibuat dari `mapping_saldoawal` baris `mapKodeAsal = msbkObjekKd`:
   - **Debet** = `mapKodeDebet` (objek bank/kas, mis. `1.01.01.01`)
   - **Kredit** = `mapKodeKredit` (lawan, mis. `3.01.01.01` MODAL DISETOR)
   - `jSumber = 'SA-BANKKAS'`, `jNo = SALDOAWAL-YYYYMMDD`
3. `tr_saldoawal` di-link ke `ms_bankkas.msbkKode` lewat `tsKodeBankKas`.

**Yang bisa diubah di DB:**
- ✅ Ubah lawan saldo awal (mis. dari MODAL ke akun lain) → ubah `mapKodeKredit` di `mapping_saldoawal`.
- ⚠️ `ms_bankkas.msbkSaldoSekarang` **tidak dipakai** — kolom "Saldo Sekarang" di layar dihitung langsung dari `jurnal` (`SUM debet − kredit` atas kode objek tsb).
- ⚠️ Edit bank/kas **tidak** memperbarui `tr_saldoawal`/jurnal-nya (hanya hapus yang menghapus jurnal). Ubah saldo awal = hapus & input ulang bank/kas.

---

## 4. Flow Penerimaan (Bendahara)

**Tabel:** `tr_terima`, `terima_counter`, `kwitansi_counter`, `mapping_penerimaan` → `jurnal`.

1. Input di menu **Bendahara → Penerimaan**: tanggal, nilai, asal penerimaan, dan **Kode Asal** (autocomplete dari `mapping_penerimaan`).
2. `tCoa` menyimpan `mapKodeAsal` mapping yang dipilih.
3. Nomor otomatis:
   - `tNoPenerimaan` = `00001/PENERIMAAN/YYYY-MM-DD` ← counter `terima_counter` (**per tahun**)
   - `tKwitansi` = `00001/YYYY-MM-DD` ← counter `kwitansi_counter` (**per tahun+bulan**)
4. Jurnal (`jSumber = 'M-TERIMA'`):
   - **Debet** = `mapKodeDebet`, **Kredit** = `mapKodeKredit`, nilai = `tNilaiBayar`
5. Edit/hapus penerimaan → jurnal ikut dihapus & dibuat ulang otomatis.

**Yang bisa diubah di DB:** ✅ tambah/ubah baris `mapping_penerimaan` untuk jenis penerimaan baru (mis. simpanan pokok, iuran) — langsung muncul di dropdown form & jurnalnya mengikuti. Reset penomoran: ubah `kCounter` di `terima_counter`/`kwitansi_counter`.

---

## 5. Flow Pengeluaran (Bendahara)

**Tabel:** `tr_keluar`, `keluar_counter`, `mapping_pengeluaran` → `jurnal`.

1. Input di **Bendahara → Pengeluaran**: tanggal, nilai, keterangan, **Kode Asal** (dari `mapping_pengeluaran`).
2. `kCoa` menyimpan `mapKodeAsal`; nomor `kNo` = `00001/PENGELUARAN/YYYY-MM-DD` (counter per tahun).
3. Jurnal (`jSumber = 'M-KELUAR'`): **Debet** = `mapKodeDebet`, **Kredit** = `mapKodeKredit`.

**Yang bisa diubah di DB:** ✅ sama seperti penerimaan — tambah/ubah `mapping_pengeluaran` sesuai kebutuhan (contoh di data live: `mapKodeAsal=1.01.05.01` → D PERSEDIAAN / K KAS).

> ⚠️ Kolom `kTerpakai`, `kIdPengajuanBelanja`, `kSumber`/`kSumberId` hanya terisi oleh flow pencairan pinjaman; input bendahara manual menyisakan NULL (belum ada logika pemakaian sebagian).

---

## 6. Flow Pinjaman (3 tahap)

### 6.1 Pengajuan (`tr_pengajuan`)

- Input di **Pinjaman → Pengajuan** (anggota, jumlah, tenor, bunga, jaminan, tujuan).
- **Status `tpStatus`:** `0` Pending → `1` Disetujui / `2` Ditolak → `4` Dicairkan. (`3` Dibatalkan ada di kamus data tapi belum dipakai kode.)
- Persetujuan (`pengajuan-pinjaman/{id}/persetujuan`) menulis `tpNilaiDisetuji` + `tpTenorDisetuji`. **Tidak membuat jurnal.**

### 6.2 Pencairan (`tr_pinjaman` + `tr_jadwalangsuran`)

- Input di **Pinjaman → Pencairan** (hanya pengajuan berstatus `1` yang muncul).
- **Perhitungan cicilan (bunga FLAT):**
  - `trpCicilanPokok` = nominal ÷ tenor
  - `trpCicilanBunga` = nominal × bunga% (flat penuh tiap bulan)
  - `trpTotalCicilan` = pokok + bunga
- `trpSisaCicilan` awal = `trpTotalCicilan × tenor`. Jadwal angsuran (`tr_jadwalangsuran`) dibuat 1 baris per tenor dengan tanggal dihitung dari **tanggal sekarang**, bukan tanggal cair.
- Otomatis: pengajuan → status `4`; membuat `tr_keluar` + jurnal (`jSumber = 'PENCAIRAN PINJAMAN'`): **Debet** = `mapKodeDebet`, **Kredit** = `mapKodeKredit` dari `mapping_pengeluaran` dengan **`mapKodeAsal = '1.01.03.01'` (hardcoded)** → data live: D PIUTANG ANGGOTA / K KAS.
- Hapus pencairan ditolak jika sudah ada pembayaran; jika belum, jadwal + kas keluar + jurnal ikut dihapus, pengajuan kembali ke status `1`.

### 6.3 Pembayaran (`tr_pembayaranpinjaman`)

- Input di **Pinjaman → Pembayaran Pinjaman**. Nomor bukti `tppNoBukti` memakai counter kwitansi.
- Alokasi FIFO: nominal bayar dialokasikan ke `tr_jadwalangsuran` berstatus `0` urut tanggal (kolom `tjaSisaTagihan` berkurang; `tjaStatus = 1` jika lunas).
- Saldo pinjaman disinkronkan: `trpSudahDibayar`, `trpSisaCicilan`, `trpStatusPinjaman` (`0` belum lunas / `1` lunas).
- Otomatis membuat `tr_terima` (`tSumber = 'BAYAR PINJAMAN'`, `tSumberId = tppId`) + jurnal (`jSumber = 'BAYAR PINJAMAN'`) dari `mapping_penerimaan` baris **`mapKodeAsal = '1.01.03.01'` (hardcoded)** → D KAS / K PIUTANG ANGGOTA.
- Edit/hapus pembayaran → alokasi jadwal & saldo pinjaman dihitung ulang.

**Yang bisa diubah di DB:**
- ✅ Akun piutang/kas pencairan & pembayaran → ubah baris `mapKodeAsal = '1.01.03.01'` di `mapping_pengeluaran` (pencairan) dan `mapping_penerimaan` (pembayaran).
- ⚠️ **`ms_jnspinjaman` belum dibaca di flow transaksi** — mengubah `mjSukuBunga`, `mjBiayaAdmin`, `mjDendaKeterlambatan`, `mjAkunPiutang`, dll. **tidak** mengubah perhitungan. Bunga yang dipakai = `trpBunga` yang diinput saat pencairan.
- ⚠️ Biaya admin/provisi & denda keterlambatan **belum diimplementasikan** (kolom `trpBiayaAdmin`, `tppBayarDenda` selalu 0).
- ⚠️ Kolom `trpMetodBayarId` / `tppMetodeBayarId` tersimpan tapi tidak memengaruhi jurnal (lihat §8).

---

## 7. Laporan (Akuntansi)

Semua laporan membaca `jurnal` + COA. Tidak ada tabel hasil laporan yang perlu di-maintain, kecuali Neraca.

### 7.1 Laporan Jurnal & Buku Besar
- Filter tanggal (awal–akhir) + akun. **Tidak ada konfigurasi DB khusus.**

### 7.2 Laporan Neraca (tabel konfigurasi `ms_neraca`) ✅

Susunan baris neraca **sepenuhnya dari tabel `ms_neraca`** (menu **Master → Master Neraca**). Kolom:

| Kolom | Arti | Contoh |
|---|---|---|
| `nrKode` | Kode unik baris | `1.01.01`, `T-AKTIVA`, `LR` |
| `nrNama` | Label tampil | `KAS`, `JUMLAH AKTIVA` |
| `nrPosisi` | Sisi neraca | `AKTIVA` / `PASIVA` |
| `nrJenis` | `header` (judul seksi, tebal abu-abu) / `item` (baris nilai) / `total` (baris jumlah) | |
| `nrKodeAkun` | Prefix kode objek yang dijumlahkan; **bisa beberapa dipisah koma** | `1.01.01` (semua objek di bawah KAS), `4,5` (pendapatan+beban) |
| `nrSaldoNormal` | Rumus saldo: `D` = debet − kredit (aktiva), `K` = kredit − debet (pasiva) | |
| `nrUrut` | Urutan tampil (disarankan kelipatan 10 agar mudah disisip) | |

- Baris `total` dihitung dari `nrKodeAkun`-nya sendiri (bukan penjumlahan baris di atasnya) → neraca tetap akurat walau ada baris item yang belum dibuat.
- `JUMLAH AKTIVA`/`JUMLAH PASIVA` = baris `nrKode = T-AKTIVA` / `T-PASIVA` (sistem mengenali dari `nrKode` ini).
- Saldo diakumulasi dari seluruh jurnal s.d. **tanggal akhir** filter.

**Contoh mengubah neraca (tanpa kode):**
- Tambah baris baru → menu Master Neraca → isi `nrKodeAkun` = kode jenis/objek yang diinginkan, atur posisi/saldo normal/urutan.
- Ganti nama baris → edit `nrNama`.
- Sembunyikan baris → hapus (total tetap benar karena berbasis prefix).
- Akumulasi penyusutan tampil negatif → biarkan `D` (atau ganti `K` bila ingin positif).
- Menambah jenis COA baru (mis. di `2.02` yang masih kosong) → jalankan ulang seeder `NeracaSeeder` (aman, tidak menimpa edit manual): `php artisan db:seed --class=NeracaSeeder`.

### 7.3 Laporan Laba Rugi (tabel konfigurasi `ms_labarugi`) ✅

Sama seperti Neraca, susunan baris laba rugi **sepenuhnya dari tabel `ms_labarugi`** (menu **Akuntansi → Master Laba Rugi**). Kolom identik dengan `ms_neraca` (prefix `lr`): `lrKode`, `lrNama`, `lrPosisi` (`PENDAPATAN`/`BIAYA`), `lrJenis` (`header`/`item`/`total`), `lrKodeAkun`, `lrSaldoNormal`, `lrUrut`.

- `JUMLAH PENDAPATAN`/`JUMLAH BIAYA` = baris `lrKode = T-PENDAPATAN` / `T-BIAYA`.
- `LABA (RUGI) BERSIH` = total pendapatan − total biaya (dihitung sistem, muncul sebagai baris footer hijau/merah).
- **Berbeda dengan neraca**: saldo laba rugi = mutasi dalam **rentang** `jTgl` filter (awal s.d. akhir), bukan akumulasi s.d. tanggal akhir — konsisten untuk periode yang dipilih.
- Data awal di-seed `LabaRugiSeeder` (baris konfigurasi + menu sidebar + akses role, idempotent): `php artisan db:seed --class=LabaRugiSeeder`.

---

## 8. Master Data Lain

| Tabel / Menu | Status | Keterangan |
|---|---|---|
| `ms_anggota` (Data Anggota) | ✅ dipakai | Dipilih di pengajuan/pencairan/pembayaran (autocomplete `api.anggota.search`) |
| `ms_jaminan`, `ms_tujuanpinjaman` | ✅ dipakai | Dipilih di form pengajuan (label saja) |
| `ms_jnspinjaman` (Jenis Pinjaman) | ⚠️ master saja | Belum dibaca flow pinjaman — lihat §6.3 |
| `ms_jnssimpanan` (Jenis Simpanan) | ⚠️ master saja | Belum ada transaksi simpanan sama sekali |
| `ms_metodebayar` (Metode Pembayaran) | ⚠️ data deskriptif | Dipilih di form pencairan/pembayaran, tapi `mmAkunGl` & `mmBiayaAdmin` **tidak** memengaruhi jurnal |

---

## 9. Tabel Counter (Penomoran Otomatis)

| Tabel | Dipakai | Format | Reset |
|---|---|---|---|
| `terima_counter` | No Penerimaan | `00001/PENERIMAAN/YYYY-MM-DD` | per tahun (`tTahun` unik) — ubah `tCounter` |
| `kwitansi_counter` | No Kwitansi & No Bukti bayar pinjaman | `00001/YYYY-MM-DD` | per tahun+bulan (`kBulan`+`kTahun` unik) — ubah `kCounter` |
| `keluar_counter` | No Pengeluaran & pencairan | `00001/PENGELUARAN/YYYY-MM-DD` | per tahun — ubah `kCounter` |

---

## 10. Cheat Sheet — "Ingin mengubah X, ubah di Y"

| Ingin… | Ubah di |
|---|---|
| Menambah/mengubah akun (COA) | `ms_akun` → `ms_kelompok` → `ms_jenis` → `ms_objek` (menu Data COA) |
| Mengubah pasangan debet/kredit suatu jenis penerimaan | `mapping_penerimaan` (`mapKodeDebet`/`mapKodeKredit`) |
| Mengubah pasangan debet/kredit suatu jenis pengeluaran | `mapping_pengeluaran` |
| Mengubah lawan saldo awal bank/kas | `mapping_saldoawal` (`mapKodeKredit`) |
| Mengubah akun piutang/kas pada pencairan pinjaman | `mapping_pengeluaran` baris `mapKodeAsal = '1.01.03.01'` |
| Mengubah akun kas/piutang pada pembayaran pinjaman | `mapping_penerimaan` baris `mapKodeAsal = '1.01.03.01'` |
| Mengubah susunan/label/rumus baris Neraca | `ms_neraca` (menu Master Neraca) |
| Mengubah susunan/label/rumus baris Laba Rugi | `ms_labarugi` (menu Master Laba Rugi) |
| Menambah menu sidebar / hak akses | `menu` + `role_menu` (lihat `docs/tambah_menu.md`) |
| Menyembunyikan menu | `menu.mIsActive = 0` |
| Mereset penomoran dokumen | `terima_counter` / `kwitansi_counter` / `keluar_counter` |
| Mengubah label status pengajuan | `tr_pengajuan.tpStatus` (kamus: 0–4) |
| Mengubah bunga/jenis pinjaman | ⚠️ belum jalan — edit di form pencairan (`trpBunga`) |

## 11. Catatan Penting (bug/limitasi yang perlu diketahui)

1. **Bug jurnal penerimaan:** `createJurnal` menghapus jurnal lama memakai key `pId` (kolom yang tidak ada) alih-alih `tId`. Flow Penerimaan manual aman (ada `hapusJurnal` eksplisit), tapi **edit Pembayaran Pinjaman dapat menggandakan jurnal `BAYAR PINJAMAN`**. Jika neraca tiba-tiba tidak seimbang, periksa duplikat di `jurnal` dengan `jSumber = 'BAYAR PINJAMAN'`.
2. **Tidak ada proses closing** (`jStatus = 1` belum pernah dipakai). Laba periode berjalan di Neraca dihitung on-the-fly dari akun 4 & 5.
3. **Jadwal angsuran** dihitung dari tanggal pencairan diinput (now), bukan dari `trpTanggalCair`.
4. **Bunga Efektif** (`ms_jnspinjaman.mjTipeBunga`) belum diimplementasikan — semua hitung bunga flat.
5. **Edit Master Bank/Kas tidak memperbarui saldo awal/jurnal** — untuk mengubah saldo awal, hapus & input ulang.
6. Kolom tidak terpakai (boleh diabaikan): `ms_bankkas.msbkSaldoSekarang`, `tr_pinjaman.trpBiayaAdmin`, `tr_terima.tInvoice`, `mapStatusPenagihan` (ketiga tabel mapping).
7. Migration keuangan ada di `database/migrations/Keuangan/` → jalankan dengan `php artisan migrate --path=database/migrations/Keuangan`.
