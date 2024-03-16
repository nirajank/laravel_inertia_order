<?php
namespace App\Contracts;

use App\Models\Order;

interface ClientRepositoryInterface
{
    public function all();

    public function find(int $id): ?Order;

    public function create(array $data): Order;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
