<?php
namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Events\LowStockDetected;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected Product $model) {}

    public function find(int $id)
    {
        return $this->model->with('variants.inventory')->findOrFail($id);
    }

    public function findBySku(string $sku)
    {
        return $this->model->where('sku', $sku)->with('variants.inventory')->first();
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->query();

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            // Fulltext search fallback
            $query->whereRaw("MATCH(title, description) AGAINST (? IN BOOLEAN MODE)", [$q.'*']);
        }

        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        return $query->with(['variants','variants.inventory'])->paginate($perPage);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Create product
            $product = Product::create([
                'title'       => $data['title'],
                'sku'       => $data['sku'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'],
                'vendor_id'   => $data['vendor_id'],
            ]);

            // Create variants + inventory
            foreach ($data['variants'] as $row) {

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'name'       => $row['name'],
                    'sku'        => $row['sku'],
                    'price'      => $data['price'],
                ]);

                $inventory = Inventory::create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $row['quantity']
                ]);

                // Trigger low-stock event if threshold reached
                if ($inventory->quantity <= config('inventory.low_stock_threshold', 5)) {
                    event(new LowStockDetected($variant));
                }
            }

            return $product->load('variants.inventory');
        });
    }

    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);

        return $product->fresh(['variants.inventory']);
    }

    public function delete($id)
    {
        $p = Product::findOrFail($id);
        return $p->delete();
    }
}
