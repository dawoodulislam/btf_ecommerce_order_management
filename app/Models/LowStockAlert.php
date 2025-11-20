<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LowStockAlert extends Model
{
    protected $fillable = [
        'variant_id', 'sku', 'quantity'
    ];
}
