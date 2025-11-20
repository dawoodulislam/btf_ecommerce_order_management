<?php
namespace App\Http\Controllers\API\v1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Jobs\ImportProductsJob;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller
{
    protected ProductService $service;
    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $req)
    {
        $perPage = (int) $req->get('per_page', 15);
        $filters = $req->only(['q','vendor_id']);
        $products = $this->service->paginate($filters, $perPage);
        return response()->json($products);
    }

    public function show($id)
    {
        $product = app(ProductRepositoryInterface::class)->find($id);
        return response()->json($product);
    }

    public function store(Request $req)
    {
        $this->authorize('create', Product::class);
        $vendor_id = $req->user()->id;

        $data = $req->validate([
            'sku' => 'required|string|unique:products,sku',      // if product SKU exists
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string',
            'variants.*.sku' => 'required|string|distinct',
            'variants.*.quantity' => 'required|integer|min:0'
        ]);
        $data['vendor_id'] = $vendor_id;

        $product = $this->service->createProduct($data);
        return response()->json([
                'message' => 'Product created successfully.',
                'product' => $product
            ], 201);;
    }

    public function update(Request $request, $id)
    {
        $product = $this->service->updateProduct($id, $request->all());
        return response()->json($product);
    }

    public function destroy($id)
    {
        $this->service->deleteProduct($id);
        return response()->json(['message' => 'Product deleted']);
    }

    public function import(Request $req)
    {
        $this->authorize('create', Product::class);
        $vendor_id = $req->user()->id;

        $req->validate(['file' => 'required|file|mimes:csv,txt']);

        $storedPath = $req->file('file')->store('imports');

        // Get full absolute path
        $fullPath = Storage::disk('local')->path($storedPath);

        // Dispatch job (sync or queue)
        ImportProductsJob::dispatch($fullPath, $vendor_id);

        return response()->json(['message' => 'Import started'], 202);
    }
}
