<?php

namespace App\Services;

use TCPDF;
use App\Models\Order;

class InvoiceService
{
    public function generate(Order $order)
    {
        $html = view('pdf.invoice', compact('order'))->render();

        $pdf = new \TCPDF();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        return $pdf->Output('', 'S'); // return raw PDF content
    }
}

