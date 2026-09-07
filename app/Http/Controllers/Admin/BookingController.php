<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return view('content.tables.bookings.index');
    }

    public function show($id)
    {
        $pageConfigs = ['has_icons' => true];
        $booking = Booking::with(['user', 'astro', 'transaction','review'])->where('id', $id)->first();
        return view('content.tables.bookings.show', compact('booking', 'pageConfigs'));
    }

    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'astro_id' => 'required|exists:astros,id',
            'date' => 'required|date',
            'price_id' => 'nullable|exists:astro_prices,id',
        ]);
        $workService = app('App\Services\WorkService');
        $data = $workService->getWorkTimes($request);
        return response($data);
    }

    public function rescheduleBooking(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'new_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        $booking = Booking::find($request->booking_id);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $booking->session_date = $request->new_date;
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        // $booking->status = 'rescheduled'; 
        $booking->save();


        return response([
            'header' => 'Success!',
            'message' => 'Booking rescheduled successfully.',
            'table' => 'booking-table',
        ]);
    }
}
