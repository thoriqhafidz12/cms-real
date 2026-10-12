<?php

namespace Database\Seeders;

use App\Models\Master\LabaRugi;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder layout Laporan Laba Rugi (tabel ms_labarugi) + menu sidebar.
 *
 * Aman dijalankan ulang: baris konfigurasi hanya di-insert bila lrKode belum ada
 * (edit manual lewat menu Master Laba Rugi tidak ditimpa), menu dicari via mRoute,
 * dan akses role_menu dicek eksistensinya.
 */
class LabaRugiSeeder extends Seeder
{
    /**
     * [lrKode, lrNama, lrPosisi, lrJenis, lrKodeAkun, lrSaldoNormal, lrUrut]
     */
    private const ROWS = [
        // ── PENDAPATAN (saldo normal K) ────────────────────────────────────
        ['PENDAPATAN', 'PENDAPATAN', 'PENDAPATAN', 'header', null, 'K', 10],
        ['4.01', 'PENDAPATAN USAHA', 'PENDAPATAN', 'header', null, 'K', 20],
        ['4.01.01', 'PENDAPATAN USAHA', 'PENDAPATAN', 'item', '4.01.01', 'K', 30],
        ['4.02', 'PENDAPATAN LAIN-LAIN', 'PENDAPATAN', 'header', null, 'K', 40],
        ['4.02.01', 'PENDAPATAN DI LUAR USAHA', 'PENDAPATAN', 'item', '4.02.01', 'K', 50],
        ['T-PENDAPATAN', 'JUMLAH PENDAPATAN', 'PENDAPATAN', 'total', '4', 'K', 60],

        // ── BIAYA (saldo normal D) ─────────────────────────────────────────
        ['BIAYA', 'BIAYA', 'BIAYA', 'header', null, 'D', 70],
        ['5.01', 'HPP/BEBAN USAHA LANGSUNG', 'BIAYA', 'header', null, 'D', 80],
        ['5.01.01', 'BIAYA PEMAKAIAN BAHAN', 'BIAYA', 'item', '5.01.01', 'D', 90],
        ['5.02', 'BIAYA TIDAK LANGSUNG', 'BIAYA', 'header', null, 'D', 100],
        ['5.02.01', 'BIAYA KARYAWAN', 'BIAYA', 'item', '5.02.01', 'D', 110],
        ['5.02.02', 'BIAYA PEMASARAN', 'BIAYA', 'item', '5.02.02', 'D', 120],
        ['5.02.03', 'BIAYA ADM & UMUM', 'BIAYA', 'item', '5.02.03', 'D', 130],
        ['5.02.04', 'BIAYA PEMELIHARAAN', 'BIAYA', 'item', '5.02.04', 'D', 140],
        ['5.02.05', 'BIAYA PENYUSUTAN', 'BIAYA', 'item', '5.02.05', 'D', 150],
        ['5.02.06', 'BIAYA KONSULTAN', 'BIAYA', 'item', '5.02.06', 'D', 160],
        ['5.02.07', 'BIAYA CADANGAN', 'BIAYA', 'item', '5.02.07', 'D', 170],
        ['5.03', 'BIAYA LAIN-LAIN', 'BIAYA', 'header', null, 'D', 180],
        ['5.03.01', 'BIAYA DILUAR USAHA', 'BIAYA', 'item', '5.03.01', 'D', 190],
        ['T-BIAYA', 'JUMLAH BIAYA', 'BIAYA', 'total', '5', 'D', 200],
    ];

    /**
     * Menu sidebar: [nama, route, parentId, order].
     * Parent 27 = dropdown Akuntansi (sama dengan Laporan/Master Neraca).
     */
    private const MENUS = [
        ['Laporan Laba Rugi', 'rpt-labarugi', 27, 5],
        ['Master Laba Rugi', 'ms-labarugi', 27, 6],
    ];

    public function run(): void
    {
        // 1. Baris konfigurasi ms_labarugi (insert bila belum ada, edit manual tidak ditimpa).
        foreach (self::ROWS as [$kode, $nama, $posisi, $jenis, $kodeAkun, $saldoNormal, $urut]) {
            LabaRugi::firstOrCreate(
                ['lrKode' => $kode],
                [
                    'lrNama' => $nama,
                    'lrPosisi' => $posisi,
                    'lrJenis' => $jenis,
                    'lrKodeAkun' => $kodeAkun,
                    'lrSaldoNormal' => $saldoNormal,
                    'lrUrut' => $urut,
                    'lrCreatedBy' => 'Seeder',
                ]
            );
        }

        // 2. Menu sidebar (cari via mRoute agar idempotent).
        $menuIds = [];
        foreach (self::MENUS as [$nama, $route, $parentId, $order]) {
            $menu = Menu::firstOrCreate(
                ['mRoute' => $route],
                [
                    'mNama' => $nama,
                    'mParentId' => $parentId,
                    'mIcon' => '',
                    'mOrder' => $order,
                    'mIsActive' => 1,
                    'mCreatedBy' => 'Seeder',
                ]
            );
            $menuIds[] = $menu->mId;
        }

        // 3. Akses menu untuk semua role yang sudah punya akses ke Laporan Neraca
        //    (menu 34) — role tanpa assignment menu lolos dari checkRole,
        //    tapi role ber-assignment harus diberi akses eksplisit.
        $roleIds = DB::table('role_menu')
            ->where('rmMenuId', 34) // Laporan Neraca
            ->pluck('rmRoleId');

        foreach ($menuIds as $menuId) {
            foreach ($roleIds as $roleId) {
                $exists = DB::table('role_menu')
                    ->where('rmRoleId', $roleId)
                    ->where('rmMenuId', $menuId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_menu')->insert([
                        'rmRoleId' => $roleId,
                        'rmMenuId' => $menuId,
                        'rmCreatedAt' => now(),
                        'rmUpdatedAt' => now(),
                    ]);
                }
            }
        }
    }
}
