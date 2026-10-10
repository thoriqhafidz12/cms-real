<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\BaseController;
use App\Libraries\CustomPdf;
use App\Models\Jurnal\Pengeluaran;

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

class CetakPengeluaranController extends BaseController
{
    public function index($id)
    {
        $data = Pengeluaran::where('kId', $id)
            ->firstOrFail()
            ->toArray();

        $pdf = new cFPDF('P', 'mm', 'A4');
        $pdf->AliasNbPages();

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, 'LAPORAN PENGELUARAN', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7, 5, 'No', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(62, 5, 'No. Pengeluaran', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nilai Bayar', 1, 0, 'C');
        $pdf->Cell(62, 5, 'Keterangan', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->setWidths([7, 30, 62, 30, 62]);
        $pdf->setAligns(['C', 'C', 'C', 'R', 'L']);

        $pdf->Row([
            1,
            $this->dateIDkosong($data['kTgl']),
            $data['kNo'],
            $this->formatRupiah($data['kNilai'], 2),
            $data['kKeterangan'],
        ]);

        // Ambil PDF sebagai string, lalu kirim melalui response Laravel.
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="laporan-pengeluaran.pdf"',
        ]);
    }
}