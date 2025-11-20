<?php
namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\GenerateInvoicePdfJob;

class QueueInvoiceGeneration
{
    public function handle(OrderCreated $event)
    {
        GenerateInvoicePdfJob::dispatch($event->order);
    }
}
