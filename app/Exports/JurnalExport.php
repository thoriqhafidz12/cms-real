<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JurnalExport implements FromArray, WithColumnFormatting, WithStyles, WithEvents
{
    /** @var array Baris siap tulis: judul, periode, baris kosong, header, data, total. */
    private array $rows;

    /**
     * @param iterable $data          Hasil getJurnalPeriode() dari BaseController.
     * @param string   $tglAwalFormat Tanggal awal sudah terformat (mis. "01 September 2026").
     * @param string   $tglAkhirFormat Tanggal akhir sudah terformat.
     */
    public function __construct(iterable $data, string $tglAwalFormat, string $tglAkhirFormat)
    {
        $this->rows = [
            ['Register Jurnal', '', '', '', '', '', '', '', '', ''],
            ['Periode: ' . $tglAwalFormat . ' s.d. ' . $tglAkhirFormat, '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['No', 'Tanggal', 'Keterangan', 'No Jurnal', 'Kode Debet', 'Nama Debet', 'Kode Kredit', 'Nama Kredit', 'Nilai Debet', 'Nilai Kredit'],
        ];

        $tDebet = 0;
        $tKredit = 0;
        $index = 0;
        foreach ($data as $row) {
            $tDebet += $row->jDebetNilai;
            $tKredit += $row->jKreditNilai;

            $this->rows[] = [
                ++$index,
                date('d-m-Y', strtotime($row->jTgl)),
                $row->jKeterangan,
                $row->jNo,
                $row->jRekDebetKode,
                $row->jRekDebetNama,
                $row->jRekKreditKode,
                $row->jRekKreditNama,
                (float) $row->jDebetNilai,
                (float) $row->jKreditNilai,
            ];
        }

        $this->rows[] = ['TOTAL', '', '', '', '', '', '', '', (float) $tDebet, (float) $tKredit];
    }

    public function array(): array
    {
        return $this->rows;
    }

    /** Format angka ribuan untuk kolom I (Nilai Debet) dan J (Nilai Kredit). */
    public function columnFormats(): array
    {
        return [
            'I' => '#,##0.00',
            'J' => '#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = count($this->rows);

        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            4 => ['font' => ['bold' => true]],
            $last => ['font' => ['bold' => true]],
        ];
    }

    /** Gabung sel A1:J1 (judul) dan A2:J2 (periode). */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $worksheet = $event->sheet->getDelegate();
                $worksheet->mergeCells('A1:J1');
                $worksheet->mergeCells('A2:J2');
            },
        ];
    }
}
