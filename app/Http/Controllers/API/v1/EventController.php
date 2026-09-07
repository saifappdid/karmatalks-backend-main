<?php
namespace App\Http\Controllers\API\v1;

use App\Helpers\Agora\RtcTokenBuilder;
use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Models\Booking;
use App\Models\InstantConsultationHistory;
use App\Models\ScheduleConsultationCallHistory;
use App\Models\ScheduleConsultationSharedMedia;
use App\Models\User;
use App\Notifications\SendNotification;
use App\Services\AgoraService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    use ResponseTrait;
    public function initiateCall(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:voice,video',
            'caller_type' => 'required|in:user,astro',
            'astro_id'    => 'required_if:caller_type,user|exists:astros,id',
            'user_id'     => 'required_if:caller_type,astro|exists:users,id',
            'booking_id'  => 'required|exists:bookings,id',
        ]);
        $booking        = Booking::findOrFail($request->booking_id);
        $appID          = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $caller         = $request->caller_type == 'user' ? User::findOrFail(auth('sanctum')->id()) : Astro::findOrFail(auth('sanctum')->id());
        $astro          = Astro::where('id', $request->caller_type == 'user' ? $request->astro_id : $caller->id)->first();
        $user           = User::where('id', $request->user_id)->first();

        $channelName = 'astro_' . $astro->id;
        $uid         = rand(1, 999999);

        $expireTimeInSeconds = 3600;
        $privilegeExpiredTs  = now()->timestamp + $expireTimeInSeconds;
        DB::beginTransaction();
        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            1,
            $privilegeExpiredTs
        );

        $callHistory = ScheduleConsultationCallHistory::create([
            'initiated_by' => $request->caller_type,
            'user_id'      => $request->caller_type == 'user' ? $caller->id : $user->id,
            'astro_id'     => $request->caller_type == 'astro' ? $caller->id : $astro->id,
            'channel_id'   => $channelName,
            'status'       => 'initiated',
            'booking_id'   => $booking->id,
            'type'         => 'schedule',
        ]);
        $booking->status = 'started';
        $booking->save();

        $receiverData = $request->caller_type == 'user' ? [
            'id'   => $caller->id,
            'name' => $caller->name,
        ] : [
            'id'   => $caller->id,
            'name' => $caller->first_name . ' ' . $caller->middle_name . ' ' . $caller->last_name,
        ];
        $deviceToken     = $user->device_id ?? $astro->device_id;
        $title           = 'Incoming Call';
        $body            = $caller->name . ' is calling you.';
        $data            = ['token' => $token, 'call_type' => $request->type, 'channel_name' => $channelName, 'uid' => $uid, 'caller_type' => $request->caller_type, 'receiver_data' => json_encode($receiverData), 'call_log' => json_encode($callHistory)];
        $firebaseService = new SendNotification();
        $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        DB::commit();
        $response = [
            'token'        => $token,
            'channel_name' => $channelName,
            'uid'          => $uid,
            'caller_type'  => $request->caller_type,
            'call_log'     => $callHistory,
        ];
        return $this->success($response, 'Call initiated successfully');
    }

    public function getAgoraToken(Request $request, AgoraService $agoraService)
    {
        $request->validate([
            'channel_name' => 'required|string',
        ]);
        $data     = $agoraService->getAgoraToken($request->channel_name);
        $response = [
            'token'        => $data['token'],
            'channel_name' => $data['channel_name'],
            'uid'          => $data['uid'],
        ];
        return $this->success($response, 'Token retrieved successfully');
    }

    public function updateCallLogForUser(Request $request)
    {
        $request->validate([
            'caller_type' => 'required|in:user,astro',
            'id'          => 'required',
            'status'      => 'required|in:initiated,ringing,ongoing,completed,failed,busy,no-answer,cancelled',
            'remark'      => 'nullable|string',
            'type'        => 'nullable|in:call_decline_by_astro',
        ]);
        // return $request->all();
        if ($request->type === 'call_decline_by_astro') {
            $callHistory = ScheduleConsultationCallHistory::findOrFail($request->id);
            $astro       = Astro::findOrFail($callHistory->astro_id);
            $user        = User::findOrFail($callHistory->user_id);
            $callHistory->update([
                'status' => 'cancelled',
            ]);
            $deviceToken     = $user->device_id;
            $title           = $request->status;
            $body            = '';
            $data            = ['call_status' => $request->status, 'channel_name' => $callHistory->channel_id, 'call_log' => json_encode($callHistory)];
            $firebaseService = new SendNotification();
            $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
            return $this->success($callHistory, 'Call decline by astro successfully');

        }
        $callHistory = ScheduleConsultationCallHistory::findOrFail($request->id);
        $astro       = Astro::findOrFail($callHistory->astro_id);
        $user        = User::findOrFail($callHistory->user_id);
        DB::beginTransaction();
        $booking = Booking::findOrFail($callHistory->booking_id);
        $callHistory->update([
            'status'          => $request->status,
            'remark'          => $request->remark ?? $callHistory->remark,
            'call_start_time' => $request->status == 'ongoing' ? now() : $callHistory->call_start_time,
            'call_end_time'   => in_array($request->status, ['completed', 'failed', 'busy', 'no-answer', 'cancelled']) ? now() : $callHistory->call_end_time,
        ]);
        if ($request->status === 'completed' && \Carbon\Carbon::parse($booking->end_time)->lte(now())) {
            $booking->status = 'ended';
            $booking->save();

            $firebaseService = new SendNotification();
            $title           = "Time's Up";
            $body            = 'Your booked consultation time has ended.';
            // $firebaseService->sendNotification($astro->device_id, $title, $body, [
            //     'call_type'   => 'call',
            //     'call_status' => 'ended',
            // ]);

            $firebaseService->sendNotification($user->device_id, $title, $body, [
                'call_type'   => 'call',
                'call_status' => 'ended',
            ]);
        }
        $caller = $request->caller_type == 'user' ? Astro::findOrFail($callHistory->astro_id) : User::findOrFail($callHistory->user_id);
        if (in_array($request->status, ['completed', 'no-answer'])) {
            $deviceToken     = $caller->device_id;
            $title           = $request->status;
            $body            = '';
            $data            = ['call_status' => $request->status, 'channel_name' => $callHistory->channel_id, 'call_log' => json_encode($callHistory)];
            $firebaseService = new SendNotification();
            $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        }
        DB::commit();

        return $this->success($callHistory, 'Call history updated successfully');
    }

    public function updateCallLogForAstro(Request $request)
    {
        $request->validate([
            'caller_type' => 'required|in:user,astro',
            'id'          => 'required',
            'status'      => 'required|in:initiated,ringing,ongoing,completed,failed,busy,no-answer,cancelled',
            'remark'      => 'nullable|string',
            'type'        => 'nullable|in:call_decline_by_astro',
        ]);
        // return $request->all();
        if ($request->type === 'call_decline_by_astro') {
            $callHistory = ScheduleConsultationCallHistory::findOrFail($request->id);
            $astro       = Astro::findOrFail($callHistory->astro_id);
            $user        = User::findOrFail($callHistory->user_id);
            $callHistory->update([
                'status' => 'cancelled',
            ]);
            $deviceToken     = $user->device_id;
            $title           = $request->status;
            $body            = '';
            $data            = ['call_status' => $request->status, 'channel_name' => $callHistory->channel_id, 'call_log' => json_encode($callHistory)];
            $firebaseService = new SendNotification();
            $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
            return $this->success($callHistory, 'Call decline by astro successfully');

        }
        $callHistory = ScheduleConsultationCallHistory::findOrFail($request->id);
        $astro       = Astro::findOrFail($callHistory->astro_id);
        $user        = User::findOrFail($callHistory->user_id);
        DB::beginTransaction();
        $booking = Booking::findOrFail($callHistory->booking_id);
        $callHistory->update([
            'status'          => $request->status,
            'remark'          => $request->remark ?? $callHistory->remark,
            'call_start_time' => $request->status == 'ongoing' ? now() : $callHistory->call_start_time,
            'call_end_time'   => in_array($request->status, ['completed', 'failed', 'busy', 'no-answer', 'cancelled']) ? now() : $callHistory->call_end_time,
        ]);
        if ($request->status === 'completed' && \Carbon\Carbon::parse($booking->end_time)->lte(now())) {
            $booking->status = 'ended';
            $booking->save();

            $firebaseService = new SendNotification();
            $title           = "Time's Up";
            $body            = 'Your booked consultation time has ended.';
            $firebaseService->sendNotification($astro->device_id, $title, $body, [
                'call_type'   => 'call',
                'call_status' => 'ended',
            ]);

            // $firebaseService->sendNotification($user->device_id, $title, $body, [
            //     'call_type'   => 'call',
            //     'call_status' => 'ended',
            // ]);
        }
        $caller = $request->caller_type == 'user' ? Astro::findOrFail($callHistory->astro_id) : User::findOrFail($callHistory->user_id);
        if (in_array($request->status, ['completed', 'no-answer'])) {
            $deviceToken     = $caller->device_id;
            $title           = $request->status;
            $body            = '';
            $data            = ['call_status' => $request->status, 'channel_name' => $callHistory->channel_id, 'call_log' => json_encode($callHistory)];
            $firebaseService = new SendNotification();
            $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        }
        DB::commit();

        return $this->success($callHistory, 'Call history updated successfully');
    }

    public function mediaShare(Request $request)
    {
        $request->validate([
            'type'            => 'nullable|in:instant,schedule',
            'call_history_id' => 'required',
            'shared_by'       => 'required|in:user,astro',
            'media'           => 'required',
        ]);
        DB::beginTransaction();
        if ($request->type == 'instant') {
            $instantCallHistory = InstantConsultationHistory::findOrFail($request->call_history_id);
        } else {
            $callHistory = ScheduleConsultationCallHistory::findOrFail($request->call_history_id);
        }
        $media = ScheduleConsultationSharedMedia::create([
            'booking_id'      => $callHistory->booking_id ?? null,
            'call_history_id' => $callHistory->id ?? null,
            'instant_call_id' => $instantCallHistory->id ?? null,
            'shared_by'       => $request->shared_by,
            'user_id'         => $callHistory->user_id ?? $instantCallHistory->user_id,
            'astro_id'        => $callHistory->astro_id ?? $instantCallHistory->astro_id,
            'media'           => FileUploader::uploadFile($request->media, 'call_history_shared_media'),
        ]);
        $caller          = $request->shared_by == 'user' ? Astro::findOrFail($callHistory->astro_id ?? $instantCallHistory->astro_id) : User::findOrFail($callHistory->user_id ?? $instantCallHistory->user_id);
        $deviceToken     = $caller->device_id;
        $title           = 'Media Shared';
        $body            = ($request->shared_by == 'user' ? 'User' : 'Astro') . ' has shared a media file with you.';
        $data            = ['channel_name' => $callHistory->channel_id ?? $instantCallHistory->channel_id, 'media' => json_encode($media)];
        $firebaseService = new SendNotification();
        $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        DB::commit();
        return $this->success($media, 'Media shared successfully');
    }
}
