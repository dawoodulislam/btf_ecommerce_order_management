<?php
namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function find(int $id);
    public function findBySku(string $sku);
    public function paginate(array $filters = [], int $perPage = 15);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete($id);
}
