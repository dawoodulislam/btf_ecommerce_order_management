<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderCreated;
use App\Listeners\SendOrderEmail;
use App\Listeners\QueueInvoiceGeneration;

use App\Events\LowStockDetected;
use App\Listeners\HandleLowStock;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            SendOrderEmail::class,
            QueueInvoiceGeneration::class,
        ],

        LowStockDetected::class => [
            HandleLowStock::class,
        ],
    ];
}
