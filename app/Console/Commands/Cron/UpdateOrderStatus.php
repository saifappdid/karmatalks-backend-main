<?php

namespace App\Console\Commands\Cron;

use App\Models\Razorpay;
use App\Services\OrderService;
use Illuminate\Console\Command;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process order status updates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new OrderService();
        $orders = Razorpay::where(['status' => 'created', 'type' => 'purchase'])->where('created_at', '<=', now()->subMinutes(15))->get();
        $orders->map(function ($order) use ($service) {
            $service->fetchOrder($order);
        });
        return $this->info('Orders Processed Successfully!');
    }
}
