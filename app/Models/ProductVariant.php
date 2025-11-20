<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id','sku','name','price','position','metadata'];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_variant_id');
    }

    protected static function booted()
    {
        static::deleting(function ($variant) {
            $variant->inventory()->delete();
        });
    }
}
