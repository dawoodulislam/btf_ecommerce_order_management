<?php
namespace App\Jobs;

use App\Models\ProductVariant;
use App\Models\LowStockAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;

class SendLowStockNotificationsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $connection = 'redis';
    public $queue = 'notifications';

    public function __construct(public ProductVariant $variant) {}

    public function handle()
    {
        // 1) create a DB alert (optional table/model)
        LowStockAlert::create([
            'product_variant_id' => $this->variant->id,
            'message' => "Low stock for {$this->variant->sku}",
            'quantity' => $this->variant->inventory->quantity ?? 0,
        ]);

        // 2) notify admins / vendor
        // Example: notify vendor and admin users
        $vendor = $this->variant->product->vendor;
        $admins = \App\Models\User::whereHas('roles', fn($q) => $q->where('name','admin'))->get();

        $recipients = collect([$vendor])->merge($admins)->unique('id')->filter();

        if ($recipients->count()) {
            Notification::send($recipients, new LowStockNotification($this->variant));
        }
    }
}
