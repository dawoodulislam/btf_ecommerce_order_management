<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $repo, protected ProductVariantRepositoryInterface $variants) {}

    public function paginate(array $filters = [], int $perPage = 15)
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function createProduct(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateProduct($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->repo->delete($id);
    }


    public function importCsv($csvPath, $vendorId = null)
    {
        // Example of an action that should be queued for big imports.
        // This is synchronous sample; in production dispatch an import job.
        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $data = array_combine($header,$row);
                $this->repo->create(array_merge($data, ['vendor_id'=>$vendorId]));
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
