<?php

namespace App\Http\Controllers\API\v1;

use App\Models\User;
use App\Models\Booking;
use App\Models\Razorpay;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\BookingService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    use ResponseTrait;
    public function createBooking(Request $request, BookingService $booking)
    {
        $request->validate([
            'type' => 'required|in:voice,video',
            'price_id' => 'required',
            'session_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'service_id' => 'required',
            'astro_id' => 'required',
            'duration' => 'required',
            'wallet_use' => 'nullable|in:yes,no',
            'name' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'place' => 'nullable|string'
        ]);
        $user = auth('sanctum')->user();
        $data = $booking->createBooking($user->id, $request);
        return $this->success(data: $data, message: 'Booking placed successfully');
    }

    public function fetchBooking(Request $request, BookingService $booking)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);
        // return $request->order_id;
        $user = User::findOrFail(auth('sanctum')->user()->id);
        $razorpayOrder = Razorpay::where('order_id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('status', 'created')->first();
        if (!$razorpayOrder) {
            return response([
                'message' => 'Oops! No Order found!',
            ], 404);
        }
        $fetchPayment = $booking->fetchBooking($razorpayOrder);
        return $fetchPayment;
    }

    public function getMyBooking()
    {
        $user = auth('sanctum')->user();
        $bookings = Booking::with(['transaction'])->where(['user_id' => $user->id])->orderBy('start_time', 'asc')->get();
        $bookings = BookingResource::collection($bookings);
        return $this->success($bookings, 'Booking fetched successfully');
    }
    public function getBookingDetail(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:bookings,id',
        ]);
        $booking = Booking::with(['transaction', 'scheduleConsultationLog', 'media:id,booking_id,media,shared_by', 'review'])->where(['id' => $request->id, 'user_id' => auth('sanctum')->id()])->first();
        $booking = new BookingResource($booking);
        if (!$booking) {
            return response(['success' => false, 'message' => 'No booking found'], 400);
        }
        return $this->success($booking, 'Booking fetched successfully');
    }
}
