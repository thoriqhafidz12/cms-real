<?php

namespace App\Libraries;

use FPDF;

class CustomPdf extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function formatRupiah($angka, $digit = 2)
    {
        if (empty($angka)) {
            $angka = 0;
        } else {
            $angka = str_replace(',', '', $angka);
        }
        $hasil_rupiah = number_format($angka, $digit, ',', '.');
        return $hasil_rupiah;
    }

    function dateIDkosong($tgl)
    {
        if ($tgl != 0) {
            if (substr($tgl, 2, 1) == '/') {
                $a = explode('/', $tgl);
            } else {
                $a = explode('-', $tgl);
                $d = $a[2];
                $m = $a[1];
                $y = $a[0];
                $a[0] = $m + 0;
                $a[1] = $d;
                $a[2] = $y;
            }

            $nmbulan = '';

            switch ($a[0]) {
                case 1:
                    $nmbulan = 'Januari';
                    break;
                case 2:
                    $nmbulan = 'Februari';
                    break;
                case 3:
                    $nmbulan = 'Maret';
                    break;
                case 4:
                    $nmbulan = 'April';
                    break;
                case 5:
                    $nmbulan = 'Mei';
                    break;
                case 6:
                    $nmbulan = 'Juni';
                    break;
                case 7:
                    $nmbulan = 'Juli';
                    break;
                case 8:
                    $nmbulan = 'Agustus';
                    break;
                case 9:
                    $nmbulan = 'September';
                    break;
                case 10:
                    $nmbulan = 'Oktober';
                    break;
                case 11:
                    $nmbulan = 'November';
                    break;
                case 12:
                    $nmbulan = 'Desember';
                    break;
            }

            $res = $a[1] . ' ' . $nmbulan . ' ' . $a['2'];
        } else {
            $res = '';
        }
        return $res;
    }
}