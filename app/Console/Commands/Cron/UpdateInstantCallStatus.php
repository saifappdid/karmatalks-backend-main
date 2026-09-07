<?php

namespace App\Console\Commands\Cron;

use App\Models\Astro;
use App\Models\InstantConnectionRequest;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\WalletTransaction;
use App\Notifications\SendNotification;
use App\Models\InstantConsultationHistory;

class UpdateInstantCallStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:instant-call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update instant call status from backend if api not call since 2 min';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $instantCalls = InstantConsultationHistory::with(['consultationRequest:id,initiated_by,type'])->where(['status' => 'ongoing'])->where('updated_at', '<=', now()->subMinutes(5))->get();
        foreach ($instantCalls as $call) {
            $call = InstantConsultationHistory::find($call->id);
            $callRequest = InstantConnectionRequest::find($call->connection_request_id);
            $start = Carbon::parse($call->started_at);
            $updated = Carbon::parse($call->updated_at);
            $user = User::findOrFail($call->user_id);
            $astro = Astro::findOrFail($call->astro_id);
            $diffInSeconds = $updated->diffInSeconds($start);
            $callRequest->status = 'completed';
            $callRequest->save();
            $diffInMinutes = ceil($diffInSeconds / 60);
            if ($diffInMinutes <= 0) {
                $diffInMinutes = 1;
            }
            $call->update([
                'ended_at' => $call->updated_at,
                'duration' => $diffInMinutes,
                'amount' => $diffInMinutes * $call->session_rate,
                'status' => 'ended'
            ]);
            WalletTransaction::create([
                'user_id' => $call->user_id,
                'type' => 'debit',
                'amount' => $call->amount,
                'previous_balance' => $user->wallet,
                'updated_balance' => $user->wallet - $call->amount,
                'remark' => 'Session payment',
                'session_history_id' => $call->id,
            ]);
            if ($call->consultationRequest->type == 'chat') {
                $firebaseService = new SendNotification();
                $title = "Chat ended";
                $body = '';
                $firebaseService->sendNotification($astro->device_id, $title, $body, [
                    'call_type' => 'chat',
                    'status' => 'ended',
                ]);
                $firebaseService->sendNotification($user->device_id, $title, $body, [
                    'call_type' => 'chat',
                    'status' => 'ended',
                ]);
            }
        }
        return Command::SUCCESS;
    }
}
