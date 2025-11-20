<?php

namespace App\Repositories\Eloquent;

use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Models\ProductVariantInventory;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;

class ProductVariantRepository implements ProductVariantRepositoryInterface
{
    public function createForProduct($productId, array $data)
    {
        $variant = ProductVariant::create([
            'product_id' => $productId,
            'name'       => $data['name'],
            'sku'        => $data['sku'],
            'price'      => $data['price'],
        ]);

        Inventory::create([
            'product_variant_id' => $variant->id,
            'quantity' => $data['quantity'] ?? 0
        ]);

        return $variant->load('inventory');
    }

    public function update($id, array $data)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->update($data);

        if (isset($data['quantity'])) {
            $variant->inventory->update(['quantity' => $data['quantity']]);
        }

        return $variant->load('inventory');
    }

    public function delete($id)
    {
        return ProductVariant::findOrFail($id)->delete();
    }
}
