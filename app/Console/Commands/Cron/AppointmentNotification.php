<?php

namespace App\Console\Commands\Cron;

use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Console\Command;
use App\Notifications\SendNotification;

class AppointmentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointment:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to user before 15 minutes of appointment';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from = Carbon::now()->addMinutes(14);
        $to   = Carbon::now()->addMinutes(15);

        $appointments = Booking::with(['user:id,name,device_id', 'astro'])
            ->where('status', 'confirmed')
            ->whereBetween('start_time', [$from, $to])
            ->get();
        foreach ($appointments as $appointment) {
            $firebase = new SendNotification();
            $title = 'Appointment Reminder';
            $body = 'Your appointment is scheduled for ' . $appointment->start_time . '.';
            $firebase->sendNotification($appointment->user->device_id, $title, $body);
        }
        $this->info('Appointment notifications sent successfully.');
        return Command::SUCCESS;
    }
}
