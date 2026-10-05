<?php

namespace App\Http\Controllers\Report;

use App\Exports\BukuBesarExport;
use App\Http\Controllers\BaseController;
use App\Libraries\CustomPdf;
use App\Models\Jurnal\Jurnal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class cFPDFBukuBesar extends CustomPdf
{
    // Custom footer for the PDF
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Set font and add page number
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Hal ' . $this->PageNo() . ' - {nb}', 0, 0, 'C');
    }
}

class CetakBukuBesarController extends BaseController
{
    /**
     * Load data buku besar via AJAX. Data dikelompokkan per akun,
     * tiap akun berisi saldo awal, baris transaksi, dan jumlah.
     */
    public function load(Request $request): JsonResponse
    {
        $tglAwal = $request->jTglAwal ?: date('Y-m-01');
        $tglAkhir = $request->jTglAkhir ?: date('Y-m-t');
        $jAkun = $request->jAkun ?: null;

        $buku = $this->buildBukuBesar($tglAwal, $tglAkhir, $jAkun);

        $data = [];
        foreach ($buku['sections'] as $section) {
            $rows = [];
            foreach ($section['rows'] as $row) {
                $rows[] = [
                    'jTgl' => $row['jTgl'],
                    'jNo' => $row['jNo'],
                    'uraian' => $row['uraian'],
                    'ref' => $row['ref'],
                    'debet' => $this->formatRupiah($row['debet']),
                    'kredit' => $this->formatRupiah($row['kredit']),
                    'saldo' => $this->formatSaldo($row['saldo']),
                ];
            }

            $data[] = [
                'kode' => $section['kode'],
                'nama' => $section['nama'],
                'saldoAwalD' => $this->formatRupiah($section['saldoAwalD']),
                'saldoAwalK' => $this->formatRupiah($section['saldoAwalK']),
                'saldoAwal' => $this->formatSaldo($section['saldoAwal']),
                'rows' => $rows,
                'tDebet' => $this->formatRupiah($section['tDebet']),
                'tKredit' => $this->formatRupiah($section['tKredit']),
                'saldoAkhir' => $this->formatSaldo($section['saldoAkhir']),
            ];
        }

        return response()->json([
            'data' => $data,
            'tDebet' => $this->formatRupiah($buku['tDebet']),
            'tKredit' => $this->formatRupiah($buku['tKredit']),
            'saldoAkhir' => $this->formatSaldo($buku['saldoAkhir']),
        ]);
    }

    /**
     * Cetak PDF buku besar. Layout mengikuti CetakJurnalController.
     */
    public function pdf(Request $request)
    {
        $tglAwal = $request->jTglAwal ?: date('Y-m-01');
        $tglAkhir = $request->jTglAkhir ?: date('Y-m-t');
        $jAkun = $request->jAkun ?: null;

        $buku = $this->buildBukuBesar($tglAwal, $tglAkhir, $jAkun);

        $pdf = new cFPDFBukuBesar('L', 'mm', 'A4');
        $pdf->AliasNbPages();

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, 'Buku Besar', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Periode: ' . $this->dateIDkosong($tglAwal) . ' s.d. ' . $this->dateIDkosong($tglAkhir), 0, 1, 'C');
        if ($jAkun) {
            $pdf->Cell(0, 5, 'Akun: ' . $jAkun, 0, 1, 'C');
        }
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(22, 5, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(60, 5, 'No Jurnal', 1, 0, 'C');
        $pdf->Cell(65, 5, 'Uraian', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Referensi', 1, 0, 'C');
        $pdf->Cell(34, 5, 'Debet', 1, 0, 'C');
        $pdf->Cell(34, 5, 'Kredit', 1, 0, 'C');
        $pdf->Cell(35, 5, 'Saldo Akhir', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->setWidths([22, 60, 65, 25, 34, 34, 35]);
        $pdf->setAligns(['C', 'C', 'L', 'C', 'R', 'R', 'R']);

        foreach ($buku['sections'] as $section) {
            // Header akun: "1.01.01.01 | KAS BENDAHARA"
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(275, 5, $section['kode'] . ' | ' . $section['nama'], 1, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);

            // Baris saldo awal
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(172, 5, 'SALDO AWAL', 1, 0, 'R');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(34, 5, $this->formatRupiah($section['saldoAwalD']), 1, 0, 'R');
            $pdf->Cell(34, 5, $this->formatRupiah($section['saldoAwalK']), 1, 0, 'R');
            $pdf->Cell(35, 5, $this->formatSaldo($section['saldoAwal']), 1, 1, 'R');

            foreach ($section['rows'] as $row) {
                $pdf->Row([
                    $row['jTgl'] ? date('d-m-Y', strtotime($row['jTgl'])) : '-',
                    $row['jNo'],
                    $row['uraian'],
                    $row['ref'],
                    $this->formatRupiah($row['debet']),
                    $this->formatRupiah($row['kredit']),
                    $this->formatSaldo($row['saldo']),
                ]);
            }

            // Baris jumlah per akun
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(172, 5, 'JUMLAH', 1, 0, 'R');
            $pdf->Cell(34, 5, $this->formatRupiah($section['tDebet']), 1, 0, 'R');
            $pdf->Cell(34, 5, $this->formatRupiah($section['tKredit']), 1, 0, 'R');
            $pdf->Cell(35, 5, $this->formatSaldo($section['saldoAkhir']), 1, 1, 'R');
            $pdf->Ln();
        }

        // Total keseluruhan
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(172, 5, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(34, 5, $this->formatRupiah($buku['tDebet']), 1, 0, 'R');
        $pdf->Cell(34, 5, $this->formatRupiah($buku['tKredit']), 1, 0, 'R');
        $pdf->Cell(35, 5, $this->formatSaldo($buku['saldoAkhir']), 1, 1, 'R');

        // Ambil PDF sebagai string, lalu kirim melalui response Laravel.
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="BukuBesar"',
        ]);
    }

    /**
     * Export Excel: parameter filter sama dengan cetak PDF.
     * Layout & styling ada di App\Exports\BukuBesarExport.
     */
    public function excel(Request $request)
    {
        $tglAwal = $request->jTglAwal ?: date('Y-m-01');
        $tglAkhir = $request->jTglAkhir ?: date('Y-m-t');
        $jAkun = $request->jAkun ?: null;

        $buku = $this->buildBukuBesar($tglAwal, $tglAkhir, $jAkun);

        $export = new BukuBesarExport(
            $buku['sections'],
            $buku['tDebet'],
            $buku['tKredit'],
            $buku['saldoAkhir'],
            $this->dateIDkosong($tglAwal),
            $this->dateIDkosong($tglAkhir)
        );

        return Excel::download($export, 'Buku-Besar.xlsx');
    }

    /**
     * Susun data buku besar: jurnal periode berjalan dikelompokkan per
     * akun (akun bisa berada di sisi debet ATAU kredit), lengkap dengan
     * saldo awal dari transaksi sebelum tanggal awal.
     */
    private function buildBukuBesar($tglAwal, $tglAkhir, $jAkun = null): array
    {
        // Saldo awal per akun: akumulasi transaksi sebelum tanggal awal.
        $saldoAwalD = [];
        $saldoAwalK = [];
        $namaAkun = [];

        $rowsAwal = Jurnal::where('jTgl', '<', $tglAwal)
            ->get(['jRekDebetKode', 'jRekDebetNama', 'jDebetNilai', 'jRekKreditKode', 'jRekKreditNama', 'jKreditNilai']);

        foreach ($rowsAwal as $row) {
            $saldoAwalD[$row->jRekDebetKode] = ($saldoAwalD[$row->jRekDebetKode] ?? 0) + (float) $row->jDebetNilai;
            $saldoAwalK[$row->jRekKreditKode] = ($saldoAwalK[$row->jRekKreditKode] ?? 0) + (float) $row->jKreditNilai;
            $namaAkun[$row->jRekDebetKode] = $namaAkun[$row->jRekDebetKode] ?? $row->jRekDebetNama;
            $namaAkun[$row->jRekKreditKode] = $namaAkun[$row->jRekKreditKode] ?? $row->jRekKreditNama;
        }

        $query = Jurnal::where('jTgl', '>=', $tglAwal)
            ->where('jTgl', '<=', $tglAkhir);

        // Filter akun (opsional): akun ada di sisi debet ATAU kredit.
        if ($jAkun) {
            $query->where(function ($q) use ($jAkun) {
                $q->where('jRekDebetKode', $jAkun)
                    ->orWhere('jRekKreditKode', $jAkun);
            });
        }

        $rows = $query->orderBy('jTgl', 'asc')
            ->orderBy('jId', 'asc')
            ->get();

        $sections = [];

        $addEntry = function ($kode, $nama, $row, $debet, $kredit) use (&$sections, &$namaAkun, $saldoAwalD, $saldoAwalK) {
            if ($kode === null || $kode === '') {
                return;
            }

            if (!isset($sections[$kode])) {
                $sections[$kode] = [
                    'kode' => $kode,
                    'nama' => $namaAkun[$kode] ?? $nama ?? $kode,
                    'saldoAwalD' => $saldoAwalD[$kode] ?? 0,
                    'saldoAwalK' => $saldoAwalK[$kode] ?? 0,
                    'rows' => [],
                    'tDebet' => 0,
                    'tKredit' => 0,
                ];
            }

            $sections[$kode]['rows'][] = [
                'jTgl' => $row->jTgl,
                'jNo' => $row->jNo ?: '-',
                'uraian' => $row->jKeterangan ?: '-',
                'ref' => '-',
                'debet' => round((float) $debet, 2),
                'kredit' => round((float) $kredit, 2),
            ];
            $sections[$kode]['tDebet'] = round($sections[$kode]['tDebet'] + (float) $debet, 2);
            $sections[$kode]['tKredit'] = round($sections[$kode]['tKredit'] + (float) $kredit, 2);
        };

        foreach ($rows as $row) {
            // Satu baris jurnal bisa masuk ke dua akun sekaligus:
            // akun debet dan akun kredit. Saat filter akun dipakai,
            // hanya sisi milik akun terpilih yang dihitung.
            if ($jAkun) {
                if ($row->jRekDebetKode === $jAkun) {
                    $addEntry($row->jRekDebetKode, $row->jRekDebetNama, $row, $row->jDebetNilai, 0);
                }
                if ($row->jRekKreditKode === $jAkun) {
                    $addEntry($row->jRekKreditKode, $row->jRekKreditNama, $row, 0, $row->jKreditNilai);
                }
            } else {
                $addEntry($row->jRekDebetKode, $row->jRekDebetNama, $row, $row->jDebetNilai, 0);
                $addEntry($row->jRekKreditKode, $row->jRekKreditNama, $row, 0, $row->jKreditNilai);
            }
        }

        // Urutkan akun berdasarkan kode, lalu hitung saldo berjalan.
        ksort($sections);

        $tDebet = 0;
        $tKredit = 0;
        $saldoTotal = 0;

        foreach ($sections as $kode => &$section) {
            $saldo = round($section['saldoAwalD'] - $section['saldoAwalK'], 2);
            $section['saldoAwal'] = $saldo;

            foreach ($section['rows'] as &$row) {
                $saldo = round($saldo + $row['debet'] - $row['kredit'], 2);
                $row['saldo'] = $saldo;
            }
            unset($row);

            $section['saldoAkhir'] = $saldo;
            $tDebet = round($tDebet + $section['tDebet'], 2);
            $tKredit = round($tKredit + $section['tKredit'], 2);
            $saldoTotal = round($saldoTotal + $section['saldoAkhir'], 2);
        }
        unset($section);

        return [
            'sections' => array_values($sections),
            'tDebet' => $tDebet,
            'tKredit' => $tKredit,
            'saldoAkhir' => $saldoTotal,
        ];
    }
}
