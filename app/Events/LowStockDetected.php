<?php

namespace App\Events;

use App\Models\ProductVariant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public ProductVariant $variant;

    public function __construct(ProductVariant $variant)
    {
        $this->variant = $variant;
    }
}
