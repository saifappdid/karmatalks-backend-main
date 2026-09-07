<?php

namespace App\Console\Commands\Cron;

use App\Models\Razorpay;
use App\Models\WalletPackage;
use App\Services\WalletService;
use Illuminate\Console\Command;

class UpdateWalletTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending wallet transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new WalletService();
        $transactions = Razorpay::where(['status' => 'created', 'type' => 'wallet'])->where('created_at', '<=', now()->subMinutes(15))->get();
        $transactions->map(function ($transactions) use ($service) {
            $service->fetchWallet($transactions, fromCron: true);
        });

        return $this->info('Wallet Transactions Processed Successfully!');
    }
}
