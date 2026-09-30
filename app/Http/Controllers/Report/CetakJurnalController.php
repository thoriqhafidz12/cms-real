<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\BaseController;
use App\Libraries\CustomPdf;
use App\Models\Jurnal\Jurnal;
use Illuminate\Http\Request;

class cFPDF extends CustomPdf
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

class CetakJurnalController extends BaseController
{
    public function index(Request $request)
    {
        // Parameter filter dikirim dari view (jurnal.blade.php) via query string.
        // Kosong → fallback ke periode bulan berjalan.
        $tglAwal = $request->jTglAwal ?: date('Y-m-01');
        $tglAkhir = $request->jTglAkhir ?: date('Y-m-t');

        $data = Jurnal::select('jNo', 'jTgl', 'jRekDebetKode', 'jRekDebetNama', 'jDebetNilai', 'jRekKreditKode', 'jRekKreditNama', 'jKreditNilai', 'jKeterangan')
            ->where('jTgl', '>=', $tglAwal)
            ->where('jTgl', '<=', $tglAkhir)
            ->orderBy('jTgl', 'asc')
            ->orderBy('jId', 'asc')
            ->get();

        $pdf = new cFPDF('L', 'mm', 'A4');
        $pdf->AliasNbPages();

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, 'Register Jurnal', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Periode: ' . $this->dateIDkosong($tglAwal) . ' s.d. ' . $this->dateIDkosong($tglAkhir), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7, 5, 'No', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Keterangan', 1, 0, 'C');
        $pdf->Cell(30, 5, 'No Jurnal', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Kode Debet', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nama Debet', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Kode Kredit', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nama Kredit', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nilai Debet', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nilai Kredit', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->setWidths([7, 30, 30, 30, 30, 30, 30, 30, 30, 30]);
        $pdf->setAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'R', 'R']);

        $tDebet = 0;
        $tKredit = 0;
        foreach ($data as $index => $row) {
            $tDebet += $row->jDebetNilai;
            $tKredit += $row->jKreditNilai;

            $pdf->Row([
                $index + 1,
                date('d-m-Y', strtotime($row->jTgl)),
                $row->jKeterangan,
                $row->jNo,
                $row->jRekDebetKode,
                $row->jRekDebetNama,
                $row->jRekKreditKode,
                $row->jRekKreditNama,
                $this->formatRupiah($row->jDebetNilai, 2),
                $this->formatRupiah($row->jKreditNilai, 2)
            ]);
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(217, 5, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(30, 5, $this->formatRupiah($tDebet, 2), 1, 0, 'R');
        $pdf->Cell(30, 5, $this->formatRupiah($tKredit, 2), 1, 1, 'R');

        // Ambil PDF sebagai string, lalu kirim melalui response Laravel.
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="register-jurnal.pdf"',
        ]);
    }
}