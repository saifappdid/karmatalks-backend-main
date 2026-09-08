<?php
namespace App\Http\Controllers\API\v1;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Models\AstroLeadSuggestion;
use App\Models\Booking;
use App\Models\Chat;
use App\Models\Country;
use App\Models\InstantConnectionRequest;
use App\Models\InstantConsultationHistory;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\SendNotification;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use DB;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    use ResponseTrait;
    public $currency;
    public function __construct()
    {
        $country = Country::find(auth('sanctum')->user()->country_id);
        if ($country) {
            $this->currency = ($country->iso3 === 'IND')
                ? 'INR'
                : 'USD';
        }
    }
    public function initiateChat(Request $request)
    {
        $request->validate([
            'astro_id' => 'required',
        ]);
        $astro = Astro::findOrFail($request->astro_id);
        if ($astro->online_for_chat == 'no' || $astro->status == 'blocked') {
            throw ValidationException::withMessages(['Astro is not available for instant consultation']);
        }
        $isAstroBusy   = InstantConsultationHistory::where('astro_id', $request->astro_id)->where('status', 'ongoing')->first();
        $astroBookings = Booking::where('astro_id', $request->astro_id)
            ->whereDate('session_date', now())
            ->whereTime('start_time', '<=', now())->whereTime('end_time', '>=', now())->whereIn('status', ['started', 'confirmed'])->first();
        if ($isAstroBusy || $astroBookings) {
            throw ValidationException::withMessages(['Astro Is Busy please try after sometime']);
        }

        $user        = User::findOrFail(auth()->user()->id);
        $sessionRate = $this->currency != 'INR' ? $astro->instant_chat_charge_dollar : $astro->instant_chat_charge;
        if ($user->wallet < 5 * $sessionRate) {
            throw ValidationException::withMessages(['Insufficient wallet balance to start chat.']);
        }
        DB::beginTransaction();
        $channelName = 'astro_' . $request->astro_id;
        $initiate    = InstantConnectionRequest::create([
            'user_id'      => $user->id,
            'astro_id'     => $request->astro_id,
            'type'         => 'chat',
            'channel_id'   => $channelName,
            'initiated_by' => 'user',
            'status'       => 'initiated',
            'currency'     => $this->currency,
        ]);
        $receiverData = [
            'id'    => $user->id,
            'name'  => $user->name,
            'image' => $user->image,
        ];
        $deviceToken     = $astro->device_id;
        $title           = 'Chat Request';
        $body            = 'You have a new chat request from ' . $user->name;
        $data            = ['call_type' => 'chat', 'call_log' => json_encode($initiate), 'receiver_data' => json_encode($receiverData)];
        $firebaseService = new SendNotification();
        $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        DB::commit();
        $response = [
            'caller_type' => 'user',
            'call_type'   => 'chat',
            'call_log'    => $initiate,
        ];

        return $this->success($response, 'Chat request Initiated successfully');
    }

    public function startChat(Request $request)
    {
        $request->validate([
            'instant_connection_request_id' => 'required',
            'caller_type'                   => 'required|in:user,astro',
        ]);
        $connectionRequest = InstantConnectionRequest::findOrFail($request->instant_connection_request_id);
        if ($request->caller_type === 'user') {
            $connectionRequest->update([
                'status' => 'failed',
            ]);
            return $this->error(message: 'Chat ended.');
        }
        $user        = User::findOrFail($connectionRequest->user_id);
        $astro       = Astro::findOrFail($connectionRequest->astro_id);
        $sessionRate = $connectionRequest->currency != 'INR' ? $astro->instant_chat_charge_dollar : $astro->instant_chat_charge;

        DB::beginTransaction();
        $conversation = InstantConsultationHistory::create([
            'connection_request_id' => $connectionRequest->id,
            'user_id'               => $user->id,
            'astro_id'              => $connectionRequest->astro_id,
            'session_type'          => 'chat',
            // 'channel_name' => $channelName,
            'session_rate'          => $sessionRate,
            'duration'              => 1,
            'amount'                => $sessionRate,
            'started_at'            => now(),
            'status'                => 'ongoing',
            'currency'              => $connectionRequest->currency,
        ]);
        $user->decrement('wallet', $sessionRate);
        $connectionRequest->status = 'ongoing';
        $connectionRequest->save();
        $deviceToken     = $user->device_id;
        $title           = 'Chat started';
        $body            = 'Chat started';
        $data            = ['call_type' => 'chat', 'status' => 'started', 'call_log' => json_encode($conversation)];
        $firebaseService = new SendNotification();
        $firebaseService->sendNotification($deviceToken, $title, $body, $data ?? []);
        DB::commit();
        $response = [
            'call_type'    => 'chat',
            'status'       => 'started',
            'conversation' => $conversation,
        ];
        return $this->success($response, 'Chat Initiated successfully');
    }

    public function updateChat(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'duration'        => 'required|integer|min:1',
        ]);
        $conversation = InstantConsultationHistory::findOrFail($request->conversation_id);
        if ($conversation->status == 'ended') {
            return $this->success([
                'message' => 'Chat Ended, Please leave the chat',
            ]);
        }
        $astro        = Astro::findOrFail($conversation->astro_id);
        $user         = User::findOrFail($conversation->user_id);

        if ($user->wallet <= ($conversation->session_rate * 2)) {
            DB::beginTransaction();
            $conversation->amount   = $conversation->amount + $conversation->session_rate;
            $conversation->duration = $conversation->duration + $request->duration;
            $conversation->status   = 'ended';
            $conversation->ended_at = now();
            $conversation->save();
            $user->wallet = $user->wallet - $conversation->session_rate;
            $user->save();
            WalletTransaction::create([
                'user_id'          => $user->id,
                'type'             => 'debit',
                'amount'           => $conversation->amount,
                'previous_balance' => $user->wallet + $conversation->amount,
                'updated_balance'  => $user->wallet,
                'remark'           => 'Chat ended due to insufficient balance',
                'currency'         => $this->currency,
            ]);
            AstroLeadSuggestion::create([
                'user_id'  => $user->id,
                'astro_id' => $conversation->astro_id,
            ]);

            DB::commit();

            $firebaseService = new SendNotification();
            $title           = "Chat ended";
            $body            = '';

            $firebaseService->sendNotification($astro->device_id, $title, $body, [
                'call_type' => 'chat',
                'status'    => 'ended',
            ]);

            $firebaseService->sendNotification($user->device_id, $title, $body, [
                'call_type' => 'chat',
                'status'    => 'ended',
            ]);
            return $this->error('Chat Ended Due to insufficient balance');
        }
        DB::beginTransaction();

        $user->decrement('wallet', $conversation->session_rate);
        $conversation->duration += $request->duration;
        $conversation->amount   += $conversation->session_rate;
        $conversation->status   = 'ongoing';
        $conversation->save();
        DB::commit();

        return $this->success($conversation, 'Chat updated successfully');
    }

    public function endChat(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
        ]);
        DB::beginTransaction();
        $conversation = InstantConsultationHistory::where('id', $request->conversation_id)->first();
        if (! $conversation) {
            return $this->success(message: 'Chat ended');
        }
        $user                   = User::findOrFail($conversation->user_id);
        $astro                  = Astro::findOrFail($conversation->astro_id);
        $conversation->status   = 'ended';
        $conversation->ended_at = now();
        $conversation->save();

        WalletTransaction::create([
            'user_id'          => $user->id,
            'type'             => 'debit',
            'amount'           => $conversation->amount,
            'previous_balance' => $user->wallet,
            'updated_balance'  => $user->wallet - $conversation->amount,
            'remark'           => 'Chat ended Successfully',
            'currency'         => $this->currency,
        ]);
        AstroLeadSuggestion::create([
            'user_id'  => $user->id,
            'astro_id' => $conversation->astro_id,
        ]);
        $firebaseService = new SendNotification();
        $title           = "Chat ended";
        $body            = '';

        if (str_contains($request->fullUrl(), 'user/chat')) {
            $firebaseService->sendNotification($astro->device_id, $title, $body, [
                'call_type' => 'chat',
                'status'    => 'ended',
            ]);
        } else {

            $firebaseService->sendNotification($user->device_id, $title, $body, [
                'call_type' => 'chat',
                'status'    => 'ended',
            ]);
        }

        DB::commit();
        return $this->success($conversation, 'Chat Ended Successfully');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:instant_consultation_histories,id',
            'message'         => 'nullable|string',
            'file'            => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,mp4|max:10240',
            'sender_type'     => 'required|in:user,astro',
        ]);
        $conversation = InstantConsultationHistory::findOrFail($request->conversation_id);
        if ($conversation->status == 'ended') {
            throw ValidationException::withMessages([
                'error' => 'Chat is already ended',
            ]);
        }
        if ($conversation->status == 'ended') {
            throw new \Exception("Can't chat due to chat ended");
        }
        DB::beginTransaction();
        $data = [
            'conversation_id' => $request->conversation_id,
            'message'         => $request->message,
            'sender_type'     => $request->sender_type,
            'sender_id'       => auth('sanctum')->user()->id,
        ];

        if ($request->hasFile('file')) {
            $file         = $request->file('file');
            $data['file'] = FileUploader::uploadFile($file, 'files/instantChats');
        }
        $chat = Chat::create($data);
        if (auth('sanctum')->check()) {
            $chat = Chat::findOrFail($chat->id);
            $chat->setAttribute(
                'created_at',
                $this->convertTimestampToLocale($chat->created_at)
            );

            $chat->setAttribute(
                'updated_at',
                $this->convertTimestampToLocale($chat->updated_at)
            );
        }
        $conversation             = InstantConsultationHistory::with(['astro:id,first_name,last_name,device_id,profile', 'user:id,device_id,name,image'])->find($request->conversation_id);
        $conversation->updated_at = now();
        $conversation->save();
        $recipient = $request->sender_type === 'user'
            ? $conversation->astro
            : $conversation->user;
        $userData = $request->sender_type === 'user' ? ['name' => $conversation->user->name, 'id' => $conversation->user->id, 'profile' => $conversation->user->image] : ['name' => $conversation->astro->first_name . ' ' . $conversation->astro->last_name, 'id' => $conversation->astro->id, 'profile' => $conversation->astro->profile];
        if ($recipient && $recipient->device_id) {
            $firebaseService = new SendNotification();
            $title           = "New Message";
            $body            = $request->message ?: 'You received a new file';
            $firebaseService->sendNotification($conversation->user->device_id, $title, $body, [
                'chat'            => $chat,
                'conversation_id' => $chat->conversation_id,
                'sender_type'     => $chat->sender_type,
                'sender_id'       => $chat->sender_id,
                'call_type'       => 'chat',
                'status'          => 'chatting',
                'user'            => json_encode($userData),
            ]);
            $firebaseService->sendNotification($conversation->astro->device_id, $title, $body, [
                'chat'            => $chat,
                'conversation_id' => $chat->conversation_id,
                'sender_type'     => $chat->sender_type,
                'sender_id'       => $chat->sender_id,
                'call_type'       => 'chat',
                'status'          => 'chatting',
                'user'            => json_encode($userData),
            ]);
        }
        DB::commit();
        return $this->success($chat, 'Message Sent Successfully');
    }
    public function convertTimestampToLocale(
        $timestamp,
        string $format = 'Y-m-d H:i:s'
    ) {
        $user     = auth('sanctum')->user();
        $astro    = auth('astro')->user();
        $country  = Country::find($user->country_id ?? $astro->country_id);
        $timeZone = json_decode($country->timezones);
        $timeZone = $timeZone[0]->zoneName;
        return Carbon::parse($timestamp, 'Asia/Kolkata')
            ->setTimezone($timeZone)
            ->format($format);
    }

    public function declineChat(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:instant_connection_requests,id',
        ]);
        $conversation = InstantConnectionRequest::findOrFail($request->id);
        $user         = User::findOrFail($conversation->user_id);
        $astro        = Astro::findOrFail($conversation->astro_id);
        DB::beginTransaction();
        $conversation->status = 'cancelled';
        $conversation->save();
        $firebaseService = new SendNotification();
        $title           = "Chat ended";
        $body            = '';

        if (str_contains($request->fullUrl(), 'user/chat')) {
            $firebaseService->sendNotification($astro->device_id, $title, $body, [
                'call_type' => 'chat',
                'status'    => 'ended',
            ]);
        } else {
            $firebaseService->sendNotification($user->device_id, $title, $body, [
                'call_type' => 'chat',
                'status'    => 'ended',
            ]);
        }
        DB::commit();
        return $this->success(null, 'Chat Declined Successfully');
    }
}
