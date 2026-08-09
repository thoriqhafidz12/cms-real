<?php

namespace App\Http\Controllers;

use App\Models\Laporan\LaporanPerfaktualModel;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan summary keuangan dan grafik per bulan.
     * Data bersumber dari tabel rpt_perfaktual (pre-aggregated).
     * Filter tahun via query param: ?tahun=2026
     */
    public function index(Request $request): View
    {
        $tahun = $request->get('tahun', date('Y'));
        $tglAwal = date('Y-m-01');
        $tglAkhir = date('Y-m-t');
        $userId = auth()->user()->id;

        // ── Daftar tahun yang tersedia ────────────────
        $availableYears = LaporanPerfaktualModel::where('rpUserId', $userId)
            ->selectRaw('DISTINCT YEAR(rpTanggal) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $model = LaporanPerfaktualModel::where('rpUserId', $userId);

        // ── Summary bulan ini ──────────────────────────
        $summary = (clone $model)
            ->whereBetween('rpTanggal', [$tglAwal, $tglAkhir])
            ->selectRaw("
                COALESCE(SUM(rpTerimaNominal), 0) as terima,
                COALESCE(SUM(rpKeluarNominal), 0) as keluar,
                COUNT(*) as total
            ")
            ->first();

        $terimaBulanIni = (float) ($summary->terima ?? 0);
        $keluarBulanIni = (float) ($summary->keluar ?? 0);
        $saldoBulanIni = $terimaBulanIni - $keluarBulanIni;
        $totalTransaksi = (int) ($summary->total ?? 0);

        // ── Saldo sekarang (all-time) ─────────────────
        $saldoAll = (clone $model)
            ->selectRaw("COALESCE(SUM(rpTerimaNominal), 0) - COALESCE(SUM(rpKeluarNominal), 0) as saldo")
            ->value('saldo');
        $saldoSekarang = (float) ($saldoAll ?? 0);

        // ── Chart: 12 bulan tahun ini ──────────────────
        $chartData = (clone $model)
            ->whereYear('rpTanggal', $tahun)
            ->selectRaw("
                MONTH(rpTanggal) as bulan,
                COALESCE(SUM(rpTerimaNominal), 0) as terima,
                COALESCE(SUM(rpKeluarNominal), 0) as keluar
            ")
            ->groupBy(DB::raw('MONTH(rpTanggal)'))
            ->orderBy(DB::raw('MONTH(rpTanggal)'))
            ->get()
            ->keyBy('bulan');

        $labels = [];
        $dataTerima = [];
        $dataKeluar = [];
        $dataSaldo = [];

        for ($i = 1; $i <= 12; $i++) {
            $bln = str_pad($i, 2, '0', STR_PAD_LEFT);
            $namaBulan = date('M', strtotime("{$tahun}-{$bln}-01"));
            $t = (float) ($chartData[$i]->terima ?? 0);
            $k = (float) ($chartData[$i]->keluar ?? 0);

            $labels[] = $namaBulan;
            $dataTerima[] = $t;
            $dataKeluar[] = $k;
            $dataSaldo[] = $t - $k;
        }

        return view('dashboard', [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalMenus' => Menu::count(),
            'tahun' => $tahun,
            'availableYears' => $availableYears,
            'terimaBulanIni' => $terimaBulanIni,
            'keluarBulanIni' => $keluarBulanIni,
            'saldoBulanIni' => $saldoBulanIni,
            'saldoSekarang' => $saldoSekarang,
            'totalTransaksi' => $totalTransaksi,
            'chartLabels' => $labels,
            'chartTerima' => $dataTerima,
            'chartKeluar' => $dataKeluar,
            'chartSaldo' => $dataSaldo,
        ]);
    }
}
