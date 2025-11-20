<?php
namespace App\Repositories\Contracts;

interface OrderRepositoryInterface {
    public function createOrder(array $data);
    public function updateStatus($id, $status);
    public function find($id);
}

