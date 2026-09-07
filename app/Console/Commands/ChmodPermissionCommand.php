<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChmodPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chmod:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change file permissions recursively'; 

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Changing file permissions recursively');
        exec('chmod -R 755 storage');
        exec('chmod -R 755 bootstrap/cache');
        Log::info('File permissions changed successfully');
    }
}
