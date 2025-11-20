<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public $variant) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Low Stock Alert: {$this->variant->sku}")
            ->line("The variant {$this->variant->sku} is low on stock.")
            ->line("Current quantity: {$this->variant->inventory->quantity}")
            ->line("Please restock soon.");
    }

    public function toArray($notifiable)
    {
        return [
            'variant_id' => $this->variant->id,
            'sku'        => $this->variant->sku,
            'quantity'   => $this->variant->inventory->quantity,
        ];
    }
}
