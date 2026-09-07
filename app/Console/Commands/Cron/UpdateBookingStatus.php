<?php

namespace App\Console\Commands\Cron;

use App\Models\Razorpay;
use Illuminate\Console\Command;
use App\Services\BookingService;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process booking status updates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new BookingService();
        $transactions = Razorpay::where(['status' => 'created', 'type' => 'booking'])->where('created_at', '<=', now()->subMinutes(15))->get();
        $transactions->map(function ($order) use ($service) {
            $service->fetchBooking($order);
        });
        return $this->info('Booking Transactions Processed Successfully!');
    }
}
