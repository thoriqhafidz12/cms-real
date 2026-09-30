<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\BaseController;
use App\Models\Jurnal\Jurnal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurnalController extends BaseController
{
    public function __construct()
    {
        $this->titlePage = 'Laporan Jurnal';
        $this->route = 'rpt-jurnal';
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
            // [
            //     'name' => 'jAkun',
            //     'label' => 'Kode Objek',
            //     'placeholder' => '-- Cari Objek --',
            //     'type' => 'autocomplete',
            //     'col' => 'col-md-3',
            //     'required' => false,
            //     'autocomplete' => [
            //         'url' => route('api.objek.search'),
            //         'textField' => 'text',
            //         'valueField' => 'id',
            //     ],
            //     // Akun bisa muncul di sisi debet ATAU kredit → kondisi OR
            //     'filter' => [
            //         ['column' => 'jRekDebetKode', 'operator' => '='],
            //         ['column' => 'jRekKreditKode', 'operator' => '='],
            //     ],
            // ],
        ];

        $this->grid = [
            [
                'label' => 'Tanggal',
                'field' => 'jTgl',
                'type' => 'date',
                'class' => 'text-center'
            ],
            [
                'label' => 'Keterangan',
                'field' => 'jKeterangan',
                'type' => 'text'
            ],
            [
                'label' => 'No Jurnal',
                'field' => 'jNo',
                'type' => 'text',
                'class' => 'text-center'
            ],
            [
                'label' => 'Kode Debet',
                'field' => 'jRekDebetKode',
                'type' => 'text',
                'class' => 'text-center'
            ],
            [
                'label' => 'Nama Debet',
                'field' => 'jDebetNama',
                'type' => 'text'
            ],
            [
                'label' => 'Kode Kredit',
                'field' => 'jRekKreditKode',
                'type' => 'text',
                'class' => 'text-right'
            ],
            [
                'label' => 'Nama Kredit',
                'field' => 'jKreditNama',
                'type' => 'text'
            ],
            [
                'label' => 'Nilai Debet',
                'field' => 'jDebetNilai',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Nilai Kredit',
                'field' => 'jKreditNilai',
                'type' => 'angka',
                'class' => 'text-right'
            ]
        ];
    }

    /**
     * Halaman laporan jurnal.
     */
    public function index(Request $request): View
    {
        return view('akuntansi.jurnal', [
            'titlePage' => $this->titlePage,
            'route' => $this->route,
            'form' => $this->form,
            'grid' => $this->grid,
        ]);
    }

    public function loadData(Request $request): JsonResponse
    {
        $tglAwal = $request->jTglAwal;
        $tglAkhir = $request->jTglAkhir;

        $query = $this->model::where('jTgl', '>=', $tglAwal)
            ->where('jTgl', '<=', $tglAkhir)
            ->orderBy('jTgl')->orderBy('jId')->get();
        ;

        // // Terapkan filter dinamis berdasarkan konfigurasi $this->form.
        // foreach ($this->form as $field) {
        //     $name = $field['name'];
        //     $value = $request->get($name);

        //     // Kosongkan nilai → pakai default dari konfigurasi field.
        //     if ($value === null || $value === '') {
        //         $value = $field['default'] ?? null;
        //     }

        //     // Tetap kosong → field ini tidak memfilter apa pun
        //     // (mis. select "-- Semua --" atau autocomplete yang dikosongkan).
        //     if ($value === null || $value === '') {
        //         continue;
        //     }

        //     $filter = $field['filter'] ?? null;
        //     if (!$filter) {
        //         // Tanpa konfigurasi filter: cari LIKE pada kolom sesuai nama field.
        //         $filter = [['column' => $name, 'operator' => 'like']];
        //     } elseif (isset($filter['column'])) {
        //         // Satu kondisi → jadikan array kondisi.
        //         $filter = [$filter];
        //     }

        //     // Kondisi dalam satu field digabung OR
        //     // (mis. akun ada di debet ATAU kredit).
        //     $query->where(function ($q) use ($filter, $value) {
        //         foreach ($filter as $index => $cond) {
        //             $method = $index === 0 ? 'where' : 'orWhere';
        //             $column = $cond['column'];
        //             $operator = $cond['operator'] ?? '=';

        //             if ($operator === 'like') {
        //                 $q->{$method}($column, 'like', '%' . $value . '%');
        //             } else {
        //                 $q->{$method}($column, $operator, $value);
        //             }
        //         }
        //     });
        // }

        $saldo = 0;
        $tDebet = 0;
        $tKredit = 0;
        $data = [];
        foreach ($query as $row) {
            $tDebet += $row->jDebetNilai;
            $tKredit += $row->jKreditNilai;

            $saldo += $row->jDebetNilai - $row->jKreditNilai;

            // Key disamakan dengan field di $this->grid agar tabel terisi otomatis.
            $data[] = [
                'jTgl' => $row->jTgl,
                'jKeterangan' => $row->jKeterangan ?: '-',
                'jNo' => $row->jNo,
                'jRekDebetKode' => $row->jRekDebetKode,
                'jDebetNama' => $row->jRekDebetNama,
                'jDebetNilai' => $this->formatRupiah($row->jDebetNilai),
                'jRekKreditKode' => $row->jRekKreditKode,
                'jKreditNama' => $row->jRekKreditNama,
                'jKreditNilai' => $this->formatRupiah($row->jKreditNilai),
            ];
        }

        return response()->json([
            'data' => $data,
            'tDebet' => $this->formatRupiah($tDebet),
            'tKredit' => $this->formatRupiah($tKredit),
            'saldoAkhir' => $this->formatRupiah($saldo),
        ]);
    }
}
