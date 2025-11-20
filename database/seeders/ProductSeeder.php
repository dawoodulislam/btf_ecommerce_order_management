<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $vendor = User::whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        })->first();
        Product::factory()->count(20)->create(['vendor_id' => $vendor->id])->each(function($p){
            for ($i=0;$i<10;$i++) {
                $v = ProductVariant::create([
                    'product_id'=>$p->id,
                    'sku' => $p->sku . '-' . ($i+1),
                    'name' => 'Variant '.($i+1),
                    'price' => $p->price + $i*5
                ]);
                Inventory::create(['product_variant_id' => $v->id, 'quantity' => rand(5,100)]);
            }
        });
    }
}
