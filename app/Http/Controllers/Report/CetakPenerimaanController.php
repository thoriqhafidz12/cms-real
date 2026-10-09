<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\BaseController;
use App\Libraries\CustomPdf;
use App\Models\Jurnal\Jurnal;
use App\Models\Jurnal\Penerimaan;
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

class CetakPenerimaanController extends BaseController
{
    public function index($id)
    {
        $data = Penerimaan::where('tId', $id)
            ->firstOrFail()
            ->toArray();

        $pdf = new cFPDF('P', 'mm', 'A4');
        $pdf->AliasNbPages();

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, 'LAPORAN PENERIMAAN', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(7, 5, 'No', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(30, 5, 'No Kwitansi', 1, 0, 'C');
        $pdf->Cell(30, 5, 'No Penerimaan', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nilai', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Asal Penerimaan', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Keterangan', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->setWidths([7, 30, 30, 30, 30, 30, 30]);
        $pdf->setAligns(['C', 'C', 'C', 'C', 'R', 'C', 'L']);

        $pdf->Row([
            1,
            date('d-m-Y', strtotime($data['tTglBayar'])),
            $data['tKwitansi'],
            $data['tNoPenerimaan'],
            $this->formatRupiah($data['tNilaiBayar'], 2),
            $data['tAsalPenerimaan'],
            $data['tDeskripsi'] ?? '',
        ]);

        // Ambil PDF sebagai string, lalu kirim melalui response Laravel.
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="laporan-penerimaan.pdf"',
        ]);
    }
}