<?php

namespace App\Services;

use App\Repositories\Contracts\ProductVariantRepositoryInterface;

class ProductVariantService
{
    protected $variants;

    public function __construct(ProductVariantRepositoryInterface $variants)
    {
        $this->variants = $variants;
    }

    public function createVariant($productId, array $data)
    {
        return $this->variants->createForProduct($productId, $data);
    }

    public function updateVariant($id, array $data)
    {
        return $this->variants->update($id, $data);
    }

    public function deleteVariant($id)
    {
        return $this->variants->delete($id);
    }
}
