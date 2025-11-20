<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['sku','title','description','price','vendor_id','active','meta'];

    protected $casts = [
        'meta' => 'array',
        'price' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)
                    ->with('inventory');
    }

    protected static function booted()
    {
        static::deleting(function ($product) {
            $product->variants()->each(function ($v) {
                $v->delete();   // Will trigger variant model delete
            });
        });
    }
}
