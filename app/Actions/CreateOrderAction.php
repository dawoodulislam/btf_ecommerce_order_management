<?php
namespace App\Actions;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Events\OrderCreated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function execute(array $payload, $user): Order
    {
        // Payload example:
        // ['items' => [['variant_id'=>1,'quantity'=>2], ...], 'shipping'=>..., 'addresses'=>...]
        return DB::transaction(function() use ($payload, $user) {
            // create order
            $order = Order::create([
                'order_number' => strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => 0,
                'shipping' => $payload['shipping'] ?? 0,
                'total' => 0,
                'shipping_address' => $payload['shipping_address'] ?? null,
                'billing_address' => $payload['billing_address'] ?? null,
            ]);

            $subtotal = 0;
            foreach ($payload['items'] as $item) {
                /** @var ProductVariant $variant */
                $variant = ProductVariant::with('inventory')->lockForUpdate()->findOrFail($item['variant_id']);
                if ($variant->inventory->quantity - $variant->inventory->reserved < $item['quantity']) {
                    throw new \Exception("Insufficient stock for variant {$variant->sku}");
                }
                // reserve or deduct immediately on confirmation depending on rules
                $variant->inventory->decrement('quantity', $item['quantity']);
                $lineTotal = ($variant->price ?? $variant->product->price) * $item['quantity'];
                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_title' => $variant->product->title . ' - ' . $variant->name,
                    'unit_price' => $variant->price ?? $variant->product->price,
                    'quantity' => $item['quantity'],
                    'line_total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;

                // low stock event check
                if ($variant->inventory->quantity <= config('inventory.low_stock_threshold', 5)) {
                    event(new \App\Events\LowStockDetected($variant));
                }
            }

            $order->subtotal = $subtotal;
            $order->total = $subtotal + ($payload['shipping'] ?? 0);
            $order->save();

            event(new OrderCreated($order));

            return $order->load('items');
        });
    }
}
