<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuBesarExport implements FromArray, WithColumnFormatting, WithStyles, WithEvents
{
    /** @var array Baris siap tulis: judul, periode, header, lalu per akun: header akun, saldo awal, transaksi, jumlah. */
    private array $rows;

    /** @var array Nomor baris section (header akun & JUMLAH) untuk styling bold + merge. */
    private array $sectionRows = [];

    /** @var int Nomor baris terakhir (baris TOTAL). */
    private int $lastRow;

    public function __construct(array $sections, float $tDebet, float $tKredit, float $saldoAkhir, string $tglAwalFormat, string $tglAkhirFormat)
    {
        $this->rows = [
            ['Buku Besar', '', '', '', '', '', ''],
            ['Periode: ' . $tglAwalFormat . ' s.d. ' . $tglAkhirFormat, '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            ['Tanggal', 'No. Jurnal', 'Uraian', 'Referensi', 'Debet', 'Kredit', 'Saldo Akhir'],
        ];

        $no = 4; // Baris header (1-index)

        foreach ($sections as $section) {
            $no++;
            $this->rows[] = [$section['kode'] . ' | ' . $section['nama'], '', '', '', '', '', ''];
            $this->sectionRows[] = $no;

            $no++;
            $this->rows[] = [
                'SALDO AWAL',
                '',
                '',
                '',
                (float) $section['saldoAwalD'],
                (float) $section['saldoAwalK'],
                (float) $section['saldoAwal'],
            ];

            foreach ($section['rows'] as $row) {
                $no++;
                $this->rows[] = [
                    $row['jTgl'] ? date('d-m-Y', strtotime($row['jTgl'])) : '-',
                    $row['jNo'],
                    $row['uraian'],
                    $row['ref'],
                    (float) $row['debet'],
                    (float) $row['kredit'],
                    (float) $row['saldo'],
                ];
            }

            $no++;
            $this->rows[] = [
                'JUMLAH',
                '',
                '',
                '',
                (float) $section['tDebet'],
                (float) $section['tKredit'],
                (float) $section['saldoAkhir'],
            ];
            $this->sectionRows[] = $no;

            // Baris kosong sebagai pemisah antar akun.
            $no++;
            $this->rows[] = ['', '', '', '', '', '', ''];
        }

        $no++;
        $this->rows[] = ['TOTAL', '', '', '', (float) $tDebet, (float) $tKredit, (float) $saldoAkhir];

        $this->lastRow = $no;
    }

    public function array(): array
    {
        return $this->rows;
    }

    /**
     * Format angka ribuan untuk kolom Debet (E) dan Kredit (F),
     * sedangkan Saldo Akhir (G) menampilkan nilai negatif dalam kurung.
     */
    public function columnFormats(): array
    {
        return [
            'E' => '#,##0.00',
            'F' => '#,##0.00',
            'G' => '#,##0.00;(#,##0.00)',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            4 => ['font' => ['bold' => true]],
            $this->lastRow => ['font' => ['bold' => true]],
        ];
    }

    /** Gabung sel A1:G1 (judul) dan A2:G2 (periode), lalu tebalkan baris section akun. */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $worksheet = $event->sheet->getDelegate();
                $worksheet->mergeCells('A1:G1');
                $worksheet->mergeCells('A2:G2');

                foreach ($this->sectionRows as $rowNo) {
                    $worksheet->mergeCells("A{$rowNo}:G{$rowNo}");
                    $worksheet->getStyle("A{$rowNo}:G{$rowNo}")->getFont()->setBold(true);
                }
            },
        ];
    }
}
