<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class GenerateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for admin creation.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Admin Generation in Process. Please Fill below Details');
        $name = $this->ask('Enter Name');
        $email = $this->ask("Enter Your Email");
        $password = $this->ask('Enter Password');

        if ($email || $password) {
            $storeAdmin = new Admin;
            $storeAdmin->name = $name;
            $storeAdmin->email = $email;
            $storeAdmin->password = Hash::make($password);
            $storeAdmin->save();
            $this->info('Admin added.');
        } else {
            $this->error('Email and Password Cannot be empty');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
