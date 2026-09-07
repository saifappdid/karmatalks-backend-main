<?php

namespace App\Console\Commands\Cron;

use App\Models\Astro;
use Illuminate\Console\Command;
use App\Notifications\SendNotification;

class UpdateAstroActiveStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'astro:update-active-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update astro active status based on last active time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $astros = Astro::where('status', 'active')->where(function ($query) {
            $query->orWhere('online_for_voice_call', 'yes')
                ->orWhere('online_for_video_call', 'yes')
                ->orWhere('online_for_chat', 'yes');
        })->whereNotNull('last_active')->get();

        foreach ($astros as $astro) {
            $inactiveMinutes = now()->diffInMinutes($astro->last_active);
            if ($inactiveMinutes === 5) {
                if ($astro->device_id) {
                    $firebaseService = new SendNotification();
                    $title = "It seems you are inactive";
                    $body = 'Your status has been changed to inactive due to inactivity for more than 5 minutes.';
                    $firebaseService->sendNotification($astro->device_id, $title, $body);
                }
            } elseif ($inactiveMinutes >= 6) {
                $astro->online_for_voice_call = 'no';
                $astro->online_for_video_call = 'no';
                $astro->online_for_chat = 'no';
                $astro->save();
                if ($astro->device_id) {
                    $firebaseService = new SendNotification();
                    $title = "It seems you are inactive";
                    $body = 'Your status has been changed to inactive due to inactivity for more than 5 minutes.';
                    $firebaseService->sendNotification($astro->device_id, $title, $body);
                }
            }
        }

        $this->info('Astro active status updated successfully.');
        return Command::SUCCESS;
    }
}
