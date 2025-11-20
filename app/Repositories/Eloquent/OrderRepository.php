<?php
namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrder(array $data)
    {
        return Order::create($data);
    }

    public function find($id)
    {
        return Order::with('items.variant.inventory')
            ->findOrFail($id);
    }

    public function updateStatus($order, $status)
    {
        $order->status = $status;
        $order->save();

        return $order->fresh('items.variant.inventory');
    }
}


