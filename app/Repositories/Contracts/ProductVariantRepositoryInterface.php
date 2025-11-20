<?php

namespace App\Repositories\Contracts;

interface ProductVariantRepositoryInterface
{
    public function createForProduct($productId, array $data);
    public function update($id, array $data);
    public function delete($id);
}
