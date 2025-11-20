<?php
namespace App\Http\Controllers\API\v1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductVariantService;

class ProductVariantController extends Controller
{
    protected ProductVariantService $variant_service;
    public function __construct(ProductVariantService $variant_service)
    {
        $this->variant_service = $variant_service;
        // $this->middleware('auth.api')->except(['index','show','search']);
    }

    public function store(Request $request, $productId)
    {
        $data = $request->validate([
            'name'     => 'required|string',
            'sku'      => 'required|string|unique:product_variants,sku',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        return $this->variant_service->createVariant($productId, $data);
    }

    public function update(Request $request, $variantId)
    {
        return $this->variant_service->updateVariant($variantId, $request->all());
    }

    public function destroy($variantId)
    {
        $this->variant_service->deleteVariant($variantId);
        return response()->json(['message' => 'Variant deleted']);
    }
}
