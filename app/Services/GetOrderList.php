<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetOrderList
{
    private string $url;
    private int $maxRetries;
    private int $retryDelay;

    public function __construct(string $url, int $maxRetries = 3, int $retryDelay = 5)
    {
        $this->url = $url;
        $this->maxRetries = $maxRetries;
        $this->retryDelay = $retryDelay;
    }
    public function getList()
    {
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                DB::transaction(function () {
                    $response = Http::get($this->url . '&after=' . $this->getDateBeforeThirtyDays());

                    if ($response->successful()) {
                        $data = $response->json();
                        $this->createOrderAndItem($data);
                    }
                }); // DB::transaction() ends here

                return; // Exit the loop if successful

            } catch (Exception $e) {
                Log::error("Order list fetch attempt $attempt failed with transaction rollback: " . $e->getMessage());
                sleep($this->retryDelay); // Wait before retrying
            }
        }

        throw new Exception("Failed to fetch order list after " . $this->maxRetries . " 
        retries.Problems may be you havenot set url or incorrect key");
    }


    private function createOrderAndItem($data): void
    {
        foreach (array_chunk($data, 1000) as $chunk) {
            foreach ($chunk as $order) {
                $orderFilter = $this->filterOrder($order);
                $orderData = Order::updateOrCreate(['order_id' => $orderFilter['order_id']], $orderFilter);

                foreach ($order['line_items'] as $item) {
                    $itemData = $this->filterItem($item, $orderData['id']);
                    Item::updateOrCreate(['item_id' => $itemData['item_id']], $itemData);
                }
            }
        }
    }


    private function getDateBeforeThirtyDays()
    {
        return Carbon::now()->subDays(30)->format('Y-m-d\TH:i:s');
    }



    private function filterOrder($data): array
    {
        $selectedKeys = [
            'number',
            'order_key',
            'status',
            'date_created',
            'total',
            'customer_id',
            'customer_note',
            'billing',
            'shipping'
        ];
        $order['order_id'] = $data['id'];
        $filterOrder = array_filter($data, function ($key) use ($selectedKeys) {
            return in_array($key, $selectedKeys);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($order, $filterOrder);
    }


    private function filterItem($data, $order_id): array
    {
        $selectedKeys = [
            'name',
            'product_id',
            'variant_id',
            'quantity',
            'taxed_price',
        ];
        $order['item_id'] = $data['id'];
        $order['order_id'] = $order_id;
        $filterOrder = array_filter($data, function ($key) use ($selectedKeys) {
            return in_array($key, $selectedKeys);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($order, $filterOrder);
    }
}
