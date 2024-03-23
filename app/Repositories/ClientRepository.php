<?php

namespace App\Repositories;

use App\Contracts\ClientRepositoryInterface;
use App\Models\Order;
use App\Services\GetOrderList;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository implements ClientRepositoryInterface
{
    public function all(bool $synOnly=false) :  bool | Collection | LengthAwarePaginator
    {
        $query = Order::query()->with('items');
        $this->synchronize($synOnly);
        if($synOnly){
            return true;
        }
        $query=$this->sort($query);
        return $this->filter($query)->paginate(request('per_page')??10);
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
                    if($value && $q->getModel()->checkField($key)){
                        $q->where($key,$value);
                    }
                }
            });
        }
        return $query;
    }

    private function sort($query)
    {
        $sortBy = request()->input('sort_by', 'id'); // Default sort by id
        $sortDirection = request()->input('sort_dir', 'asc'); // Default ascending order
        $arrayCheck=$query->getModel()->getFillable();
        
        if (in_array($sortBy,$arrayCheck) || $sortBy=='id') {
            return $query->orderBy($sortBy, $sortDirection);

        }

       return $query;

    }

    private function synchronize(bool $sync=false):void
    {
        if(request()->synchronize || $sync){
          (new GetOrderList(config('app.order_url')))->getList();
        }
    }

}
