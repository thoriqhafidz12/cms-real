<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Report\CetakBukuBesarController;
use App\Models\Jurnal\Jurnal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuBesarController extends BaseController
{
    public function __construct()
    {
        $this->titlePage = 'Laporan Buku Besar';
        $this->route = 'rpt-buku-besar';
        $this->model = Jurnal::class;

        $this->form = [
            [
                'name' => 'jTglAwal',
                'label' => 'Tanggal Awal',
                'placeholder' => 'Masukkan tanggal awal',
                'type' => 'date',
                'col' => 'col-md-3',
                'required' => true,
                'default' => date('Y-m-01')
            ],
            [
                'name' => 'jTglAkhir',
                'label' => 'Tanggal Akhir',
                'placeholder' => 'Masukkan tanggal akhir',
                'type' => 'date',
                'col' => 'col-md-3',
                'required' => true,
                'default' => date('Y-m-t')
            ],
            // [
            //     'name' => 'jSumber',
            //     'label' => 'Sumber',
            //     'placeholder' => '-- Semua --',
            //     'type' => 'select',
            //     'col' => 'col-md-3',
            //     'required' => false,
            //     'options' => [
            //         ['value' => 'PENERIMAAN', 'label' => 'Penerimaan'],
            //         ['value' => 'PENGELUARAN', 'label' => 'Pengeluaran'],
            //     ],
            //     'filter' => ['column' => 'jSumber', 'operator' => '='],
            // ],
            [
                'name' => 'jAkun',
                'label' => 'Kode Objek',
                'placeholder' => '-- Cari Objek --',
                'type' => 'autocomplete',
                'col' => 'col-md-3',
                'required' => false,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ],
                // Akun bisa muncul di sisi debet ATAU kredit → kondisi OR
                'filter' => [
                    ['column' => 'jRekDebetKode', 'operator' => '='],
                    ['column' => 'jRekKreditKode', 'operator' => '='],
                ],
            ],
        ];

        $this->grid = [
            [
                'label' => 'Tanggal',
                'field' => 'jTgl',
                'type' => 'date',
                'class' => 'text-center'
            ],
            [
                'label' => 'No Jurnal',
                'field' => 'jNo',
                'type' => 'text',
                'class' => 'text-center'
            ],
            [
                'label' => 'Uraian',
                'field' => 'uraian',
                'type' => 'text'
            ],
            [
                'label' => 'Referensi',
                'field' => 'ref',
                'type' => 'text',
                'class' => 'text-center'
            ],
            [
                'label' => 'Debet',
                'field' => 'debet',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Kredit',
                'field' => 'kredit',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Saldo Akhir',
                'field' => 'saldo',
                'type' => 'angka',
                'class' => 'text-right'
            ]
        ];
    }

    /**
     * Halaman laporan buku besar.
     */
    public function index(Request $request): View
    {
        return view('akuntansi.bukubesar', [
            'titlePage' => $this->titlePage,
            'route' => $this->route,
            'form' => $this->form,
            'grid' => $this->grid,
        ]);
    }

    public function loadData(Request $request): JsonResponse
    {
        $cetak = new CetakBukuBesarController();
        return $cetak->load($request);
    }

    public function pdf(Request $request)
    {
        $cetak = new CetakBukuBesarController();
        return $cetak->pdf($request);
    }


    /**
     * Export Excel: parameter filter sama dengan cetak PDF.
     * Layout & styling ada di App\Exports\BukuBesarExport.
     */
    public function excel(Request $request)
    {
        $cetak = new CetakBukuBesarController();
        return $cetak->excel($request);
    }
}
