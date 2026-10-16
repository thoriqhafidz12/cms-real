<?php

namespace App\Http\Controllers;

use App\Models\Jurnal\Penerimaan;
use App\Models\Jurnal\Pengeluaran;
use App\Models\Master\MasterAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends BaseController
{
    /** Nama bulan pendek (Indonesia) untuk label sumbu grafik. */
    private const BULAN_PENDEK = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    /** Nama bulan panjang (Indonesia). */
    private const BULAN_PANJANG = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $now = Carbon::now();

        // ---- KPI keuangan tahun berjalan (helper BaseController) ----
        $labaRugi = $this->buildLabaRugi(
            Carbon::create($now->year, 1, 1)->format('Y-m-d'),
            $now->format('Y-m-d')
        );

        // ---- Anggota ----
        $totalAnggota = MasterAnggota::count();
        $anggotaBaruBulanIni = MasterAnggota::query()
            ->whereYear('maCreatedAt', $now->year)
            ->whereMonth('maCreatedAt', $now->month)
            ->count();

        // 12 bulan terakhir (termasuk bulan berjalan)
        $bulanList = collect(range(11, 0))
            ->map(fn (int $i) => $now->copy()->subMonthsNoOverflow($i));

        $bulanLabels = $bulanList
            ->map(fn (Carbon $b) => self::BULAN_PENDEK[(int) $b->format('n')] . " '" . $b->format('y'))
            ->all();

        $pertumbuhanAnggota = $this->pertumbuhanAnggota($bulanList);
        [$terimaBulanan, $keluarBulanan] = $this->mutasiKasBulanan($bulanList, $now);

        // Komposisi jenis kelamin (nilai selain Laki-laki/Perempuan dikelompokkan sebagai "Lainnya")
        $lakiLaki = MasterAnggota::where('maJnsKelamin', 'Laki-laki')->count();
        $perempuan = MasterAnggota::where('maJnsKelamin', 'Perempuan')->count();
        $genderData = [
            'labels' => ['Laki-laki', 'Perempuan', 'Lainnya'],
            'values' => [$lakiLaki, $perempuan, $totalAnggota - $lakiLaki - $perempuan],
        ];

        // ---- Daftar terbaru ----
        $anggotaTerbaru = $this->anggotaTerbaru(5);
        $aktivitas = $this->aktivitasTerbaru(7);

        return view('dashboard', [
            'totalAnggota' => $totalAnggota,
            'totalAnggotaText' => number_format($totalAnggota, 0, ',', '.'),
            'anggotaBaruBulanIni' => $anggotaBaruBulanIni,
            'labaRugi' => $labaRugi,
            'pendapatanText' => 'Rp ' . $this->formatRupiah($labaRugi['tPendapatan'], 0),
            'biayaText' => 'Rp ' . $this->formatRupiah($labaRugi['tBiaya'], 0),
            'labaText' => 'Rp ' . $this->formatSaldo($labaRugi['labaBersih'], 0),
            'labaPositif' => $labaRugi['laba'],
            'tahunIni' => $now->year,
            'hariIni' => $now->format('d') . ' ' . self::BULAN_PANJANG[(int) $now->format('n')] . ' ' . $now->format('Y'),
            'bulanLabels' => $bulanLabels,
            'pertumbuhanAnggota' => $pertumbuhanAnggota,
            'terimaBulanan' => $terimaBulanan,
            'keluarBulanan' => $keluarBulanan,
            'genderData' => $genderData,
            'anggotaTerbaru' => $anggotaTerbaru,
            'aktivitas' => $aktivitas,
            'hasTrendAnggota' => array_sum($pertumbuhanAnggota) > 0,
            'hasDataGender' => $totalAnggota > 0,
            'hasDataKeuangan' => array_sum($terimaBulanan) + array_sum($keluarBulanan) > 0,
        ]);
    }

    /**
     * Jumlah anggota kumulatif tiap bulan (12 bulan terakhir).
     * Anggota tanpa maCreatedAt tidak ikut dalam deret tren.
     */
    private function pertumbuhanAnggota($bulanList): array
    {
        $firstMonth = $bulanList->first();

        $perBulan = MasterAnggota::query()
            ->selectRaw("DATE_FORMAT(maCreatedAt, '%Y-%m') AS ym, COUNT(*) AS jumlah")
            ->whereNotNull('maCreatedAt')
            ->groupBy('ym')
            ->pluck('jumlah', 'ym');

        $kumulatif = MasterAnggota::query()
            ->where('maCreatedAt', '<', $firstMonth->copy()->startOfMonth()->format('Y-m-d'))
            ->count();

        $hasil = [];
        foreach ($bulanList as $bulan) {
            $kumulatif += (int) ($perBulan[$bulan->format('Y-m')] ?? 0);
            $hasil[] = $kumulatif;
        }

        return $hasil;
    }

    /**
     * Nilai penerimaan dan pengeluaran per bulan (12 bulan terakhir).
     */
    private function mutasiKasBulanan($bulanList, Carbon $now): array
    {
        $awal = $bulanList->first()->copy()->startOfMonth()->format('Y-m-d');
        $akhir = $now->format('Y-m-d');

        $terimaPerBulan = Penerimaan::query()
            ->selectRaw("DATE_FORMAT(tTglBayar, '%Y-%m') AS ym, SUM(tNilaiBayar) AS total")
            ->whereBetween('tTglBayar', [$awal, $akhir])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $keluarPerBulan = Pengeluaran::query()
            ->selectRaw("DATE_FORMAT(kTgl, '%Y-%m') AS ym, SUM(kNilai) AS total")
            ->whereBetween('kTgl', [$awal, $akhir])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $terima = [];
        $keluar = [];
        foreach ($bulanList as $bulan) {
            $ym = $bulan->format('Y-m');
            $terima[] = (float) ($terimaPerBulan[$ym] ?? 0);
            $keluar[] = (float) ($keluarPerBulan[$ym] ?? 0);
        }

        return [$terima, $keluar];
    }

    /** 5 anggota terdaftar terakhir (untuk daftar "Anggota Terbaru"). */
    private function anggotaTerbaru(int $limit): array
    {
        return MasterAnggota::query()
            ->orderByDesc('maCreatedAt')
            ->limit($limit)
            ->get(['maKode', 'maNama', 'maCreatedAt'])
            ->map(fn ($a) => [
                'kode' => $a->maKode ?: '-',
                'nama' => $a->maNama ?: '-',
                'inisial' => $this->inisial($a->maNama),
                'tanggal' => $a->maCreatedAt ? Carbon::parse($a->maCreatedAt)->format('d/m/Y') : '-',
            ])
            ->all();
    }

    /**
     * Gabungan transaksi penerimaan & pengeluaran terbaru, urut tanggal menurun.
     */
    private function aktivitasTerbaru(int $limit): array
    {
        $items = collect();

        foreach (Penerimaan::query()->orderByDesc('tNoPenerimaan')->limit($limit)->get() as $t) {
            $items->push([
                'tanggal' => Carbon::parse($t->tTglBayar)->format('d/m/Y'),
                'no' => $t->tNoPenerimaan,
                'deskripsi' => $t->tDeskripsi ?: ($t->tAsalPenerimaan ?: '-'),
                'nilai' => (float) $t->tNilaiBayar,
                'tipe' => 'Penerimaan',
            ]);
        }

        foreach (Pengeluaran::query()->orderByDesc('kTgl')->limit($limit)->get() as $k) {
            $items->push([
                'tanggal' => Carbon::parse($k->kTgl)->format('d/m/Y'),
                'no' => $k->kNo,
                'deskripsi' => $k->kKeterangan ?: '-',
                'nilai' => (float) $k->kNilai,
                'tipe' => 'Pengeluaran',
            ]);
        }

        return $items->sortByDesc('tanggal')->take($limit)->values()->all();
    }

    /** Inisial nama (maks. 2 huruf) untuk avatar daftar anggota. */
    private function inisial(?string $nama): string
    {
        $kata = preg_split('/\s+/', trim((string) $nama));
        $inisial = substr($kata[0] ?? '', 0, 1) . (isset($kata[1]) ? substr($kata[1], 0, 1) : '');

        return strtoupper($inisial) ?: '?';
    }
}
