<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArusKasController extends BaseController
{
    public function __construct()
    {
        $this->titlePage = 'Laporan Arus Kas';
        $this->route = 'lap-arus-kas';

        $this->form = [
            [
                'name' => 'rpTglAwal',
                'label' => 'Tanggal Awal',
                'placeholder' => 'Masukkan tanggal awal',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'rpTglAkhir',
                'label' => 'Tanggal Akhir',
                'placeholder' => 'Masukkan tanggal akhir',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
        ];

        $this->grid = [
            [
                'label' => 'Tanggal',
                'field' => 'tanggal',
                'type' => 'date',
                'class' => 'text-center'
            ],
            [
                'label' => 'Keterangan',
                'field' => 'keterangan',
                'type' => 'text'
            ],
            [
                'label' => 'Penerimaan',
                'field' => 'penerimaan',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Pengeluaran',
                'field' => 'pengeluaran',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Saldo',
                'field' => 'saldo',
                'type' => 'angka',
                'class' => 'text-right'
            ],
        ];
    }

    /**
     * Halaman laporan arus kas.
     */
    public function index(Request $request): View
    {
        return view('laporan.laporan-arus-kas', [
            'titlePage' => $this->titlePage,
            'route' => $this->route,
            'form' => $this->form,
            'grid' => $this->grid,
        ]);
    }

    public function loadData(Request $request): JsonResponse
    {
        $tglAwal = $request->get('rpTglAwal', date('Y-m-01'));
        $tglAkhir = $request->get('rpTglAkhir', date('Y-m-t'));

        $query = DB::table('rpt_perfaktual')
            ->select('rpTanggal as tanggal', 'rpKeterangan as keterangan', 'rpTerimaNominal as nTerima', 'rpKeluarNominal as nKeluar')
            ->where('rpTanggal', '>=', $tglAwal)
            ->where('rpTanggal', '<=', $tglAkhir)
            ->orderBy('rpTanggal')
            ->get();

        $saldo = 0;
        $data = [];
        $tKeluar = 0;
        $tTerima = 0;

        foreach ($query as $row) {
            $tTerima += $row->nTerima;
            $tKeluar += $row->nKeluar;

            $saldo += ($row->nTerima - $row->nKeluar);

            $data[] = [
                'tanggal' => $row->tanggal,
                'keterangan' => $row->keterangan ?: '-',
                'penerimaan' => $row->nTerima,
                'pengeluaran' => $row->nKeluar,
                'saldo' => $saldo,
            ];
        }

        return response()->json([
            'data' => $data,
            'total' => count($data),
            'saldoAkhir' => $saldo,
            'totalPenerimaan' => $tTerima,
            'totalPengeluaran' => $tKeluar,
        ]);
    }
}
