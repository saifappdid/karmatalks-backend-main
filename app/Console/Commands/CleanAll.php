<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CleanAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all cache files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('cache:clear');
        Artisan::call('clear-compiled');
        Artisan::call('clockwork:clean', ['--all' => true]);
        Artisan::call('config:clear');
        Artisan::call('event:clear');
        Artisan::call('optimize:clear');
        Artisan::call('route:clear');
        Artisan::call('schedule:clear-cache');
        Artisan::call('view:clear');

        $this->info('All cache files cleaned successfully');
    }
}
