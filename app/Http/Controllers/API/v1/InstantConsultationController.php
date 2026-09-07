<?php
namespace App\Http\Controllers\API\v1;

use App\Helpers\Agora\RtcTokenBuilder;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstantConsultationHistoryDetailResource;
use App\Http\Resources\InstantConsultationHistoryResource;
use App\Models\Astro;
use App\Models\AstroLeadSuggestion;
use App\Models\Booking;
use App\Models\Country;
use App\Models\InstantConnectionRequest;
use App\Models\InstantConsultationHistory;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\SendNotification;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstantConsultationController extends Controller
{
    use ResponseTrait;
    public $currency;
    public function __construct()
    {
        $user    = auth('sanctum')->user();
        $country = Country::find($user->country_id);

        $this->currency = ($country->iso3 === 'IND')
            ? 'INR'
            : 'USD';
    }
    public function initiateCallRequest(Request $request)
    {
        $request->validate([
            'session_type' => 'required|in:voice,video,chat',
            'astro_id'     => 'required_if:caller_type,user|exists:astros,id',
        ]);

        $userWalletBalance = auth()->user()->wallet;

        if ($userWalletBalance <= 0) {
            throw ValidationException::withMessages(['Insufficient wallet balance. Please recharge your wallet to initiate the call.']);
        }

        $astro = Astro::where('id', $request->astro_id)->first();

        if (($astro->online_for_voice_call == 'no' && $astro->online_for_video_call == 'no') || $astro->status == 'blocked') {
            throw ValidationException::withMessages(['Astro is not available for instant consultation']);
        }
        if ($astro->online_for_voice_call == 'no' && $request->session_type == 'voice') {
            throw ValidationException::withMessages(['Astro is not available for voice consultation']);
        }
        if ($astro->online_for_video_call == 'no' && $request->session_type == 'video') {
            throw ValidationException::withMessages(['Astro is not available for video consultation']);
        }

        if ($request->session_type == 'voice') {
            if (auth()->user()->country_id == 101) {
                if ($astro->instant_voice_call_charge <= 0) {
                    throw ValidationException::withMessages(['Astro has not set voice call charge']);
                }
                if ($userWalletBalance < ($astro->instant_voice_call_charge * 5)) {
                    throw ValidationException::withMessages(['Insufficient wallet balance to initiate the call. Please recharge your wallet.']);
                }
            } else {
                if ($astro->instant_voice_call_charge_dollar <= 0) {
                    throw ValidationException::withMessages(['Astro has not set voice call charge']);
                }
            }
        }

        if ($request->session_type == 'video') {
            if (auth()->user()->country_id == 101) {
                if ($astro->instant_video_call_charge <= 0) {
                    throw ValidationException::withMessages(['Astro has not set voice call charge']);
                }
                if ($userWalletBalance < ($astro->instant_video_call_charge * 5)) {
                    throw ValidationException::withMessages(['Insufficient wallet balance to initiate the call. Please recharge your wallet.']);
                }
            } else {
                if ($astro->instant_video_call_charge_dollar <= 0) {
                    throw ValidationException::withMessages(['Astro has not set voice call charge']);
                }
            }
        }

        if ($request->session_type == 'chat') {
            if (auth()->user()->country_id == 101) {
                if ($astro->instant_chat_charge <= 0) {
                    throw ValidationException::withMessages(['Astro has not set voice call charge']);
                }
                if ($userWalletBalance < ($astro->instant_chat_charge * 5)) {
                    throw ValidationException::withMessages(['Insufficient wallet balance to initiate the call. Please recharge your wallet.']);
                }
            } else {
                if ($astro->instant_chat_charge_dollar <= 0) {
                    throw ValidationException::withMessages(['Astro has not set voice call charge']);
                }
            }
        }

        $isAstroBusy   = InstantConsultationHistory::where('astro_id', $request->astro_id)->where('status', 'ongoing')->first();
        $astroBookings = Booking::where('astro_id', $request->astro_id)
            ->whereDate('session_date', now())
            ->whereTime('start_time', '<=', now())->whereTime('end_time', '>=', now())->where('status', '!=', 'ended')->first();
        if ($isAstroBusy || $astroBookings) {
            throw ValidationException::withMessages(['Astro Is Busy please try after sometime']);
        }
        $appID          = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $caller         = User::findOrFail(auth('sanctum')->id());
        $user           = User::where('id', $request->user_id)->first();

        $channelName = 'astro_' . $astro->id;
        $uid         = rand(1, 999999);

        $expireTimeInSeconds = 3600;
        $privilegeExpiredTs  = now()->timestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            1,
            $privilegeExpiredTs
        );
        DB::beginTransaction();
        $connectionRequest = InstantConnectionRequest::create([
            'type'         => $request->session_type,
            'initiated_by' => 'user',
            'user_id'      => auth('sanctum')->id(),
            'astro_id'     => $request->astro_id,
            'channel_id'   => $channelName,
            'status'       => 'initiated',
            'currency'     => $this->currency,
        ]);
        $receiverData = [
            'id'    => $caller->id,
            'name'  => $caller->name,
            'image' => $caller->image,
        ];
        $deviceToken     = $user->device_id ?? $astro->device_id;
        $title           = 'Incoming Call';
        $body            = $caller->name . ' is calling you.';
        $data            = ['token' => $token, 'call_type' => $request->session_type, 'channel_name' => $channelName, 'uid' => $uid, 'caller_type' => $request->caller_type, 'receiver_data' => json_encode($receiverData), 'call_log' => json_encode($connectionRequest)];
        $firebaseService = new SendNotification();
        $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        DB::commit();
        $response = [
            'token'        => $token,
            'channel_name' => $channelName,
            'uid'          => $uid,
            'caller_type'  => $request->caller_type,
            'call_log'     => $connectionRequest,
            'call_type'    => $request->session_type,
        ];
        return $this->success($response, 'Call request initiated successfully');
    }

    public function updateCallLog(Request $request)
    {

        $request->validate([
            'id'        => 'required|exists:instant_connection_requests,id',
            'status'    => 'required|in:ringing,ongoing,completed,failed,busy,no-answer,cancelled,declined',
            'status_by' => 'nullable|in:user,astro',
        ]);
        $callRequest = InstantConnectionRequest::findOrFail($request->id);
        DB::beginTransaction();
        $callRequest->update([
            'status' => $request->status,
            // 'call_start_time' => $request->status == 'ongoing' ? now() : $callRequest->call_start_time,
            // 'call_end_time' => in_array($request->status, ['completed', 'failed', 'busy', 'no-answer', 'cancelled']) ? now() : $callRequest->call_end_time,
        ]);
        // if ($request->completed_by == 'user' || $request->completed_by == 'astro') {
        //     $caller = $request->completed_by == 'user' ? Astro::findOrFail($callRequest->astro_id) : User::findOrFail($callRequest->user_id);
        // } else {
        //     }
        $caller = $request->status_by == 'user' ? Astro::findOrFail($callRequest->astro_id) : User::findOrFail($callRequest->user_id);

        if (in_array($request->status, ['completed', 'no-answer', 'declined'])) {
            $deviceToken     = $caller->device_id;
            $title           = $request->status;
            $body            = '';
            $data            = ['call_status' => $request->status, 'remark' => $request->remark, 'channel_name' => $callRequest->channel_id, 'call_log' => json_encode($callRequest)];
            $firebaseService = new SendNotification();
            $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        }
        DB::commit();
        return $this->success($callRequest, 'Call history updated successfully');
    }

    public function startSession(Request $request)
    {
        $request->validate([
            'connection_request_id' => 'required|exists:instant_connection_requests,id',
            'astro_id'              => 'required|numeric|exists:astros,id',
            'session_type'          => 'required|string|in:voice,chat,video',
            'duration'              => 'required|numeric',
            'session_rate'          => 'required|numeric',
        ]);
        $callLog = InstantConnectionRequest::findOrFail($request->connection_request_id);
        $user    = User::with(['country'])->findOrFail($callLog->user_id);
        $astro   = Astro::findOrFail($callLog->astro_id);
        if ($request->session_type == 'voice') {
            $sessionRate = $callLog->currency != 'INR' ? $astro->instant_voice_call_charge_dollar : $astro->instant_voice_call_charge;
        } else {
            $sessionRate = $callLog->currency != 'INR' ? $astro->instant_video_call_charge_dollar : $astro->instant_video_call_charge;
        }
        if ($user->wallet < ($sessionRate * 5)) {
            return response(['errors' => 'insufficient balance']);
        }
        DB::beginTransaction();
        $session = InstantConsultationHistory::create([
            'user_id'               => $user->id,
            'astro_id'              => $request->astro_id,
            'connection_request_id' => $request->connection_request_id,
            'amount'                => $sessionRate,
            'session_type'          => $request->session_type,
            'duration'              => $request->duration,
            'session_rate'          => $sessionRate,
            'started_at'            => now(),
            'remark'                => 'Session started',
            'type'                  => 'instant',
            'currency'              => $callLog->currency,
        ]);

        $wallet       = $user->wallet - $sessionRate;
        $user->wallet = $wallet;
        $user->save();
        DB::commit();
        $deviceToken     = $user->device_id;
        $title           = 'Call started';
        $body            = '';
        $data            = ['call_type' => $session->session_type, 'session_type' => 'instant', 'status' => 'started', 'call_log' => json_encode($session)];
        $firebaseService = new SendNotification();
        $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        $firebaseService->sendNotification($astro->device_id, $title, $body, $data ?? []);
        $response = [
            'session'        => $session,
            'wallet_balance' => $wallet,
        ];

        return $this->success($response, 'Session started successfully');
    }
    public function updateSession(Request $request)
    {
        $request->validate([
            'id'           => 'required|numeric',
            'session_rate' => 'required|numeric',
            'duration'     => 'required|numeric',
        ]);
        $session = InstantConsultationHistory::find($request->id);
        $user    = User::findOrFail(auth('sanctum')->id());
        $astro   = Astro::findOrFail($session->astro_id);

        if ($user->wallet <= ($session->session_rate * 2)) {
            DB::beginTransaction();
            $session->amount   = $session->amount + $session->session_rate;
            $session->duration = $session->duration + $request->duration;
            $session->status   = 'ended';
            $session->ended_at = now();
            $session->remark   = $request->reason;
            $session->save();
            $user->wallet = $user->wallet - $session->session_rate;
            $user->save();
            WalletTransaction::create([
                'user_id'            => $session->user_id,
                'type'               => 'debit',
                'amount'             => $session->amount,
                'previous_balance'   => $user->wallet + $session->amount,
                'updated_balance'    => $user->wallet,
                'remark'             => 'Session payment',
                'session_history_id' => $session->id,
                'currency'           => $this->currency,
            ]);

            $user->wallet = $user->wallet -  $session->session_rate;
            $user->save();
            
            DB::commit();

            $firebaseService = new SendNotification();
            $title           = "Insufficient Balance";
            $body            = 'Call ended due to insufficient balance.';
            $firebaseService->sendNotification($astro->device_id, $title, $body, [
                'call_type'   => 'call',
                'call_status' => 'ended',
            ]);

            $firebaseService->sendNotification($user->device_id, $title, $body, [
                'call_type'   => 'call',
                'call_status' => 'ended',
            ]);

            return response(['errors' => 'insufficient balance']);
        }
        DB::beginTransaction();
        $session->amount   = $session->amount + $session->session_rate;
        $session->duration = $session->duration + $request->duration;

        $session->save();

        $user->wallet = $user->wallet - $session->session_rate;

        $user->save();

        DB::commit();
        $response = [
            'session'        => $session,
            'wallet_balance' => $user->wallet,
        ];
        return $this->success($response, 'Session updated successfully');
    }

    public function endSession(Request $request)
    {
        $request->validate([
            'id'     => 'required|numeric',
            'reason' => 'required|string',
        ]);
        DB::beginTransaction();
        $session = InstantConsultationHistory::where('id', $request->id)
            ->where('status', 'ongoing')->first();
        if (! $session) {
            return $this->success(message: 'Call declined');
        }
        $user = User::findOrFail($session->user_id);

        if (! $session) {
            return response(['errors' => 'No ongoing session found'], 422);
        }

        WalletTransaction::create([
            'user_id'            => $session->user_id,
            'type'               => 'debit',
            'amount'             => $session->amount,
            'previous_balance'   => $user->wallet,
            'updated_balance'    => $user->wallet - $session->amount,
            'remark'             => 'Session payment',
            'session_history_id' => $session->id,
            'currency'           => $this->currency,
        ]);
        $session->status   = 'ended';
        $session->ended_at = now();
        $session->remark   = $request->reason;
        $session->save();

        $user->wallet = $user->wallet - $session->amount;
        $user->save();

        AstroLeadSuggestion::create([
            'user_id'  => $user->id,
            'astro_id' => $session->astro_id,
        ]);
        DB::commit();
        return $this->success($session, 'Session ended successfully');
    }

    public function getHistory()
    {
        $history = InstantConsultationHistory::with(['astro:id,first_name,middle_name,last_name,profile'])->where('user_id', auth('sanctum')->id())->latest()->simplePaginate(10);
        $history->items();
        $history = InstantConsultationHistoryResource::collection($history);
        return $this->success($history, 'Data fetched successfully!');
    }
    public function getDetail(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:instant_consultation_histories,id',
        ]);
        $data = InstantConsultationHistory::with(['astro:id,first_name,middle_name,last_name,profile', 'chats' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'media:id,media,instant_call_id'])->where('user_id', auth('sanctum')->id())->findOrFail($request->id);
        $data = new InstantConsultationHistoryDetailResource($data);
        return $this->success($data, 'Data fetched successfully!');
    }
}
