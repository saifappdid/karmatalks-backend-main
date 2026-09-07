<?php
namespace App\Http\Controllers\API\v1;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Http\Resources\AstroDetailResource;
use App\Http\Resources\AstroResource;
use App\Http\Resources\BookingResource;
use App\Jobs\SendNotificationJob;
use App\Models\Astro;
use App\Models\AstroWorkingDay;
use App\Models\AstroWorkingHour;
use App\Models\BlockUser;
use App\Models\Booking;
use App\Models\InstantConsultationHistory;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class AstroController extends Controller
{
    use ResponseTrait;

    public function updateProfile(Request $request)
    {
        $request->validate([
            'profile'     => 'nullable|mimes:png,jpg,jpeg',
            'first_name'  => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name'   => 'required|string|max:50',
            'email'       => 'required|email',
        ]);
        $astro = Astro::find(auth('astro')->id());
        $astro->update([
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
            'profile'     => $request->file('profile') ? FileUploader::optimizeUploadFile($request->profile, 'astros') : $astro->profile,
        ]);
        return $this->success(data: $request->all(), message: 'Profile updated successfully');
    }
    public function updateConsultationTimeLine(Request $request)
    {
        $request->validate([
            'days' => 'required',
        ]);
        $astro = Astro::find(auth('astro')->id());
        foreach ($request->days as $key => $day) {
            $workDay = AstroWorkingDay::updateOrCreate(
                [
                    'day'      => $key,
                    'astro_id' => $astro->id,
                ],
                [
                    'status' => $day[$key . '_status'] == 'on' ? 'active' : 'blocked',
                ]
            );
            AstroWorkingHour::updateOrCreate(
                [
                    'astro_id'             => $astro->id,
                    'astro_working_day_id' => $workDay->id,
                ],
                [
                    'astro_id'             => $astro->id,
                    'astro_working_day_id' => $workDay->id,
                    'start_time'           => date('H:i:s', strtotime($day[$key . '_start_time'])),
                    'end_time'             => date('H:i:s', strtotime($day[$key . '_end_time'])),
                ]
            );
        }
        return $this->success(message: 'Consultation time updated successfully');
    }

    public function getProfile()
    {
        $astro = Astro::with(['price', 'skills' => function ($query) {
            $query->where('skills.status', 'active');
        }, 'workingDays' => ['workingHours']])->withCount('review')->withSum('review', 'rating')->find(auth('astro')->id());
        $astro = new AstroDetailResource($astro);
        return $this->success(data: $astro, message: 'Profile fetched successfully');
    }
    public function updateInstantConsultationStatus(Request $request)
    {
        $request->validate([
            'type'   => 'required|in:voice,video,chat',
            'status' => 'required|in:yes,no',
        ]);
        $astro              = Astro::find(auth('sanctum')->id());
        $followers          = $astro->followers()->pluck('user_id')->toArray();
        $followersDeviceIds = User::whereIn('id', $followers)->whereNotNull('device_id')->pluck('device_id')->toArray();

        if (count($followersDeviceIds) > 0 && $request->status == 'yes') {
            foreach ($followersDeviceIds as $deviceId) {
                dispatch(new SendNotificationJob(
                    title: 'Now Available',
                    message: 'Astro ' . $astro->first_name . ' ' . $astro->last_name . ' is now ' . ($request->status == 'yes' ? 'online' : 'offline') . ' for ' . $request->type . ' consultation.',
                    device_id: $deviceId,
                ))->onQueue('low');
            }
        }

        $astro->update([
            'online_for_voice_call' => $request->type === 'voice' ? $request->status : $astro->online_for_voice_call,
            'online_for_video_call' => $request->type === 'video' ? $request->status : $astro->online_for_video_call,
            'online_for_chat'       => $request->type === 'chat' ? $request->status : $astro->online_for_chat,
        ]);
        return $this->success(data: $astro, message: 'Instant Consultation status updated successfully');
    }
    public function getAppointments(Request $request)
    {
        $request->validate([
            'type' => 'required|in:all,history,upcoming',
        ]);
        $appointments = Booking::with(['user'])->where(['astro_id' => auth('sanctum')->id()])
            ->where(function ($query) {
                if (request('type') == 'history') {
                    $query->where(function ($q) {
                        $q->whereDate('session_date', '<', now())
                            ->orWhere(function ($q2) {
                                $q2->whereDate('session_date', now())
                                    ->whereTime('end_time', '<', now());
                            });
                    })->where('status', 'ended');
                } elseif (request('type') == 'upcoming') {
                    $query->where(function ($q) {
                        $q->whereDate('session_date', '>', now())
                            ->orWhere(function ($q2) {
                                $q2->whereDate('session_date', now())
                                    ->whereTime('end_time', '>=', now());
                            });
                    })->where('status', 'confirmed');
                }
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(10);
        $appointments = $appointments->items();
        $appointments = BookingResource::collection($appointments);
        return $this->success(data: $appointments, message: 'Appointments fetched successfully');
    }
    public function getTodaysAppointments(Request $request)
    {
        $astro        = Astro::find(auth('sanctum')->id());
        $appointments = Booking::with(['user:id,name'])->where(['astro_id' => $astro->id])
            ->whereIn('status', ['confirmed', 'started'])
            ->whereDate('session_date', now())
            ->whereTime('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();
        $appointments = BookingResource::collection($appointments);
        return $this->success(data: $appointments, message: 'Todays appointments fetched successfully');
    }

    public function getUpcomingAppointments(Request $request)
    {
        $astro        = Astro::find(auth('sanctum')->id());
        $appointments = Booking::with(['user:id,name'])->where(['astro_id' => $astro->id, 'status' => 'confirmed'])
            ->whereDate('session_date', '>', now())
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)->get();
        $appointments = BookingResource::collection($appointments);
        return $this->success(data: $appointments, message: 'Upcoming appointments fetched successfully');
    }
    public function appointmentDetails(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:bookings,id',
        ]);
        $booking = Booking::with(['user:id,name', 'transaction', 'scheduleConsultationLog', 'media'])->where(['id' => $request->id, 'astro_id' => auth('sanctum')->id()])->first();
        $booking = new BookingResource($booking);
        if (! $booking) {
            return response(['success' => false, 'message' => 'No booking found'], 400);
        }
        return $this->success($booking, 'Booking fetched successfully');
    }
    public function updateIsOnlineStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:0,1',
        ]);
        $astro = Astro::find(auth('astro')->id());
        $astro->update(['is_online' => $request->status]);
        return $this->success($astro, 'Astro online status updated successfully');
    }

    public function getInstantConsultationHistory()
    {
        $history = InstantConsultationHistory::with(['user:id,name,image'])->where('astro_id', auth('sanctum')->id())->latest()->simplePaginate(10);
        $history = $history->items();
        return $this->success($history, 'Data fetched successfully!');
    }
    public function getInstantConsultationDetail(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:instant_consultation_histories,id',
        ]);
        $data = InstantConsultationHistory::with(['user:id,name,image', 'chats' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'media:id,media,instant_call_id'])->where('astro_id', auth('sanctum')->id())->findOrFail($request->id);
        return $this->success($data, 'Data fetched successfully!');
    }

    public function updateLastActive()
    {
        $astro = Astro::find(auth('sanctum')->id());
        $astro->update(['last_active' => now()]);
        return $this->success($astro, 'Astro last active time updated successfully');
    }

    /*==============================================================>
    /==========Below Functions are for user application=============>
    /==============================================================*/
    // Instant Online Astro List
    // ->where(function ($query) {
    //             $query->where('online_for_voice_call', 'yes')
    //                 ->orWhere('online_for_video_call', 'yes')
    //                 ->orWhere('online_for_chat', 'yes');
    //         })
    public function getAstro(Request $request)
    {
        $request->validate([
            'search'       => 'nullable|string',
            'rating'       => 'nullable',
            'languages'    => 'nullable|array',
            'services'     => 'nullable',
            'experience'   => 'nullable',
            'availability' => 'nullable|in:1,0',
        ]);
        // $astros = Astro::select('id', 'first_name', 'middle_name', 'last_name', 'profile', 'skills', 'instant_voice_call_charge', 'instant_video_call_charge', 'instant_chat_charge', 'online_for_voice_call', 'online_for_video_call', 'online_for_chat', 'available_for_schedule_consultation')
        //     ->active()->withCount('review')->withSum('review', 'rating')
        //     ->search(request(['search']))->get();

        $query = Astro::select(
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'profile',
            'experience',
            'instant_voice_call_charge',
            'instant_video_call_charge',
            'instant_chat_charge',
            'online_for_voice_call',
            'online_for_video_call',
            'online_for_chat',
            'available_for_schedule_consultation'
        )
            ->where(['status' => 'active'])
            ->with(['services', 'skills' => function ($query) {
                $query->where('skills.status', 'active');
            }])
            ->withCount('review')
            ->withSum('review', 'rating')
            ->search(request(['search']));

        if ($request->filled('rating')) {
            $query->having('review_sum_rating', '>=', $request->rating);
        }

        if ($request->filled('languages')) {
            $languages = $request->languages;
            $query->where(function ($q) use ($languages) {
                foreach ($languages as $lang) {
                    $q->orWhereJsonContains('known_languages', [['id' => (int) $lang]]);
                }
            });
        }

        if ($request->filled('services')) {
            $services = $request->services;
            $query->whereHas('services', function ($q) use ($services) {
                $q->whereIn('service_id', is_array($services) ? $services : explode(',', $services));
            });
        }

        if ($request->filled('experience')) {
            $query->where('experience', '>=', $request->experience);
        }
        if ($request->filled('availability') && $request->availability == 1) {
            $query->where(function ($q) {
                $q->where('online_for_voice_call', 'yes')
                    ->orWhere('online_for_video_call', 'yes')
                    ->orWhere('online_for_chat', 'yes');
            });
        }
        $query->orderByRaw("
                    CASE
                        WHEN online_for_voice_call = 'yes' OR online_for_video_call = 'yes' OR online_for_chat = 'yes'
                        THEN 0
                        ELSE 1
                    END
                ")
            ->orderBy('id', 'asc');
        $astros = $query->simplePaginate(10);
        $astros = $astros->items();
        $astros = AstroResource::collection($astros);
        return $this->success($astros, 'Astros fetched successfully');
    }
    // Instant Online Astro Detail List
    public function getAstroDetail(Request $request)
    {
        $request->validate([
            'astro_id'   => 'required|exists:astros,id',
            'service_id' => 'nullable',
        ]);
        $astro = Astro::active()->with(['price' => function ($query) {
            if (request('service_id')) {
                $query->where(['service_id' => request('service_id')]);
            }
        }, 'review', 'skills' => function ($query) {
            $query->where('skills.status', 'active');
        }, 'services' => function ($query) {
            $query->where('services.status', 'active');
        }])
            ->withExists(['followers as is_following' => function ($q) {
                $q->where('user_id', auth()->id());
            }])
            ->withCount('review')->withSum('review', 'rating')
            ->where('id', $request->astro_id)->first();
        $astro = new AstroResource($astro);
        return $this->success($astro, 'Astro detail fetched successfuly');
    }

    // Service astro list
    public function getServiceAstro(Request $request)
    {
        $request->validate([
            'service_id'   => 'required|exists:services,id',
            'search'       => 'nullable|string',
            'rating'       => 'nullable',
            'languages'    => 'nullable|array',
            'experience'   => 'nullable',
            'availability' => 'nullable|in:1,0',
        ]);
        $query = Astro::select('id', 'first_name', 'middle_name', 'last_name', 'profile', 'known_languages', 'experience', 'available_for_schedule_consultation')
            ->active()->where('is_online', '1')->withCount('review')->withSum('review', 'rating')
            ->withWhereHas('services', function ($query) use ($request) {
                $query->where('service_id', $request->service_id);
            })
            ->search(request(['search']));

        if ($request->filled('rating')) {
            $query->having('review_sum_rating', '>=', $request->rating);
        }

        if ($request->filled('languages')) {
            $languages = $request->languages;
            $query->where(function ($q) use ($languages) {
                foreach ($languages as $lang) {
                    $q->orWhereJsonContains('known_languages', [['id' => (int) $lang]]);
                }
            });
        }

        if ($request->filled('services')) {
            $services = $request->services;
            $query->whereHas('services', function ($q) use ($services) {
                $q->whereIn('service_id', is_array($services) ? $services : explode(',', $services));
            });
        }

        if ($request->filled('experience')) {
            $query->where('experience', '>=', $request->experience);
        }
        if ($request->filled('availability') && $request->availability == 1) {
            $query->where(function ($q) {
                $q->orWhere('online_for_voice_call', 'yes')
                    ->orWhere('online_for_video_call', 'yes')
                    ->orWhere('online_for_chat', 'yes');
            });
        }

        $astros = $query->inRandomOrder()->get();
        $astros = AstroResource::collection($astros);
        return $this->success($astros, 'Astros fetched successfully');
    }

    public function getFollowers(Request $request)
    {
        $astro     = Astro::findOrFail(auth('sanctum')->id());
        $followers = $astro->followers()->with('user')->get();
        return $this->success($followers, 'Followers retrieved successfully');
    }

    public function blockUser(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'astro_id' => 'required|exists:astros,id',
            'remark'   => 'required|string',
            'status'   => 'required|in:blocked,active',
        ]);

        $blockUser = BlockUser::updateOrCreate(
            [
                'user_id'  => $request->user_id,
                'astro_id' => $request->astro_id,
            ],
            [
                'remark' => $request->remark,
                'status' => $request->status,
            ]
        );

        return $this->success($blockUser, $request->status === 'blocked'
                ? 'User blocked successfully'
                : 'User unblocked successfully');

    }

}
