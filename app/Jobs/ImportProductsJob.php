<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use App\Models\ProductVariant;
use App\Events\LowStockDetected;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductVariantInventory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Configure queue
    // public $connection = 'redis';
    // public $queue = 'imports';
    // public $tries = 3;
    // public $backoff = [20, 40, 80];
    // public $timeout = 180;

    protected string $filePath;
    protected int $lowStockThreshold;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $lowStockThreshold = 5)
    {
        $this->filePath = $filePath;
        $this->lowStockThreshold = $lowStockThreshold;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (!file_exists($this->filePath)) {
            Log::error("CSV file not found: {$this->filePath}");
            return;
        }

        $handle = fopen($this->filePath, 'r');

        if (!$handle) {
            Log::error("Unable to open CSV: {$this->filePath}");
            return;
        }

        $header = fgetcsv($handle); // first row (title, description, price,...)

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            DB::beginTransaction();
            try {
                // 1. Create Product OR fetch existing matching title
                $product = Product::firstOrCreate(
                    ['title' => $data['title']],
                    [
                        'description' => $data['description'] ?? null,
                        'sku' => $data['product_sku'] ?? null,
                        'price' => $data['price'] ?? 0,
                        'vendor_id' => auth('api')->id() ?? null // optional
                    ]
                );

                // 2. Create Variant
                $variant = ProductVariant::firstOrCreate(
                    ['sku' => $data['variant_sku']],
                    [
                        'product_id' => $product->id,
                        'name' => $data['variant_name'],
                    ]
                );

                // 3. Inventory (always update)
                $inventory = Inventory::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['quantity' => (int)$data['variant_quantity']]
                );

                // 4. Trigger low-stock event
                if ($inventory->quantity <= $this->lowStockThreshold) {
                    event(new LowStockDetected($variant));
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Import error: {$e->getMessage()}", ['row' => $data]);
            }
        }

        fclose($handle);

        Log::info("Product import completed successfully.");
    }
}
