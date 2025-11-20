<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LowStockNotification;
use App\Models\LowStockAlert;

class HandleLowStock implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;          // retry attempts
    public $backoff = 10;       // retry delay
    public $timeout = 60;       // worker timeout

    public function handle(LowStockDetected $event)
    {
        $variant = $event->variant;

        // Create alert in DB
        LowStockAlert::create([
            'variant_id' => $variant->id,
            'sku'        => $variant->sku,
            'quantity'   => $variant->inventory->quantity,
        ]);

        // Notify vendor
        if ($variant->product->vendor) {
            $variant->product->vendor->notify(
                new LowStockNotification($variant)
            );
        }
    }
}
