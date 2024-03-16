<?php

namespace App\Console\Commands;

use App\Services\GetOrderList;
use Illuminate\Console\Command;

class GetOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderListService = new GetOrderList(config('app.order_url'));
        $orderListService->getList();
    }
}
