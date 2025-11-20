<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    // Customers can view their own orders
    public function view(User $user, Order $order)
    {
        return $user->id === $order->user_id
            || $user->hasRole('admin')
            || $user->hasRole('vendor');
    }

    // Vendor/admin can update order status
    public function update(User $user, Order $order)
    {
        return $user->hasAnyRole(['admin', 'vendor']);
    }
}
