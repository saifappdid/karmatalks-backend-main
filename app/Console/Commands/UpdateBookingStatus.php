<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:booking-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will update booking status whose time is eded';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bookings = Booking::with('scheduleConsultationLog')
            ->whereIn('status', ['confirmed', 'started'])
            ->where(function ($q) {
                $q->whereDate('session_date', '<', now()->toDateString())
                    ->orWhere(function ($q2) {
                        $q2->whereDate('session_date', '=', now()->toDateString())
                            ->whereTime('end_time', '<=', now()->toTimeString());
                    });
            })
            ->get();


        foreach ($bookings as $booking) {
            $bookingLog = $booking->scheduleConsultationLog->where('status', 'ongoing')->whereNull('call_end_time')->first();
            if ($bookingLog) {
                $bookingLog->status = 'completed';
                $bookingLog->remark = 'Time Up Ended By System';
                $bookingLog->save();
            }

            $booking->update([
                'status' => 'ended',
                'ended_at' => now()
            ]);
        }

        $this->info("Updated {$bookings->count()} bookings to ended.");
    }
}
