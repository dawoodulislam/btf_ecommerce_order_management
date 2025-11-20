<?php
namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendEmailJob;

class SendOrderEmail
{
    public function handle(OrderCreated $event)
    {
        // Queue email job
        SendEmailJob::dispatch($event->order->user->email, 'emails.order.created', ['order' => $event->order]);
    }
}
