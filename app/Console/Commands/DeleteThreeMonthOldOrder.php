<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteThreeMonthOldOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:orders';

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
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        // Define chunk size (adjust as needed)
        $chunkSize = 1000;

        Order::where('updated_at', '<', $threeMonthsAgo)
            ->chunk($chunkSize, function ($orders) {
                foreach ($orders as $order) {
                    $order->delete();
                }
            });
    }
}
