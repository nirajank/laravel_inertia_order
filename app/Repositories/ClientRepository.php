<?php

namespace App\Repositories;

use App\Contracts\ClientRepositoryInterface;
use App\Models\Order;
use App\Services\GetOrderList;

class ClientRepository implements ClientRepositoryInterface
{
    public function all()
    {
        $query = Order::query();
        $this->synchronize();
        return $this->filter($query)->paginate(10);
    }

    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function create(array $data): Order
    {
        // Validate data (optional)
        return Order::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $client = Order::find($id);
        if ($client) {
            return $client->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        return Order::destroy($id);
    }

    private function filter($query)
    {
        $filters = json_decode(request('filters'),true);
        if ($filters && !empty($filters)) {
            $query=$query->where(function ($q) use ($filters) {
                foreach ($filters as $key => $value) {
                    if($value){
                        $q->where($key,$value);
                    }
                }
            });
        }
        return $query;
    }

    private function synchronize():void
    {
        if(request()->synchronize){
          (new GetOrderList(config('app.order_url')))->getList();
        }
    }

}
