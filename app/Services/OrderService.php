<?php
namespace App\Services;

use App\Models\OrderItem;
use App\Events\OrderCreated;
use App\Models\ProductVariant;
use App\Events\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    protected $orders;

    public function __construct(OrderRepositoryInterface $orders)
    {
        $this->orders = $orders;
    }

    public function createOrder($user, array $items)
    {
        return DB::transaction(function () use ($user, $items) {

            $total = 0;
            foreach ($items as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);
                $total += $variant->price * $item['quantity'];
            }

            $order = $this->orders->createOrder([
                'user_id' => $user->id,
                'order_number' => rand(100000, 999999),
                'status'  => 'pending',
                'subtotal' => $total,
                'total' => $total,
                'shipping' => 100,
                'shipping_address' => null,
                'billing_address' => null
            ]);

            foreach ($items as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_title' => $variant->name,
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $variant->price,
                    'line_total' => $variant->price
                ]);
            }

            event(new OrderCreated($order));

            return $order->fresh('items.variant.inventory');
        });
    }

    public function processStatusChange($order, $status)
    {
        return DB::transaction(function () use ($order, $status) {

            $old = $order->status;

            $order = $this->orders->updateStatus($order, $status);

            if ($old === 'pending' && $status === 'processing') {
                // Deduct inventory
                foreach ($order->items as $item) {
                    $inv = $item->variant->inventory;
                    $inv->quantity -= $item->quantity;
                    $inv->save();
                }
            }

            if (in_array($old, ['pending','processing']) && $status === 'cancelled') {
                // Restore inventory
                foreach ($order->items as $item) {
                    $inv = $item->variant->inventory;
                    $inv->quantity += $item->quantity;
                    $inv->save();
                }
            }

            event(new OrderStatusChanged($order, $old));

            return $order;
        });
    }
}


