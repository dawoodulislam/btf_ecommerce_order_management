<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'vendor']);
    }

    public function update(User $user, Product $product)
    {
        // Admin can update anything
        if ($user->hasRole('admin')) {
            return true;
        }

        // Vendor can update his own products
        return $user->hasRole('vendor') && $product->vendor_id === $user->id;
    }

    public function delete(User $user, Product $product)
    {
        return $this->update($user, $product);
    }
}
