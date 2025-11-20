<?php
namespace App\Jobs;

use TCPDF;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use PDF; // e.g. barryvdh/laravel-dompdf
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public Order $order) {}

    public function handle()
    {
        $html = view('pdf.invoice', compact('order'))->render();

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->writeHTML($html);
        $filename = 'order_invoices.pdf';
        Storage::disk('local')->put($filename, $pdf->Output('', 'S'));
    }
}
