<?php
namespace App\Services;

use App\Models\Astro;
use App\Models\AstroPrice;
use App\Models\AstroWorkingDay;
use App\Models\Booking;
use App\Models\Country;
use App\Services\TimeConverterService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkService
{
    public $converter;
    public $timeZone;
    public function __construct()
    {
        $country        = Country::findOrFail(auth('sanctum')->user()->country_id);
        $timeZone       = json_decode($country->timezones);
        $this->timeZone = $timeZone[0]->zoneName;
        if ($timeZone) {
            $this->converter = new TimeConverterService($timeZone[0]->zoneName);
        }
    }

    public function geInternationalWorkTimes($request)
    {
        $astro      = Astro::find($request->astro_id);
        $astroPrice = AstroPrice::find($request->price_id);

        if (! $astro) {
            \Log::error('Astro not found with ID: ' . $request->astro_id);
            throw ValidationException::withMessages(["Astro not found"]);
        }

        if ($astro->is_online == '0') {
            \Log::error('Astro is not online with ID: ' . $request->astro_id);
            throw ValidationException::withMessages(["Astro is not available right now"]);
        }

        $astroTimezone = 'Asia/Kolkata';
        $userTimezone  = $this->timeZone;
        $bookingDate   = Carbon::createFromFormat(
            'Y-m-d',
            $request->date,
            $astroTimezone
        );

        $dayOfBooking = Str::lower($bookingDate->format('l'));

        $fetchAllBookings = Booking::where('status', '!=', 'cancelled')
            ->where('astro_id', $astro->id)
            ->whereDate('session_date', $bookingDate->format('Y-m-d'))
            ->get();

        $works = AstroWorkingDay::with(['workingHours'])
            ->where([
                'astro_id' => $astro->id,
                'day'      => $dayOfBooking,
                'status'   => 'active',
            ])
            ->first();

        if (! $works) {
            return response([
                'success' => true,
                'message' => 'No booking available on ' . ucfirst($dayOfBooking),
            ], 400);
        }

        $durationSeconds     = ((int) $astroPrice->duration) * 60;
        $chunkedWorkingHours = [];

        foreach ($works->workingHours as $workingHour) {
            $startIST = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $bookingDate->format('Y-m-d') . ' ' . $workingHour->start_time,
                $astroTimezone
            );

            $endIST = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $bookingDate->format('Y-m-d') . ' ' . $workingHour->end_time,
                $astroTimezone
            );

            $startUTC = $startIST->copy()->utc();
            $endUTC   = $endIST->copy()->utc();

            while ($startUTC < $endUTC) {

                $slotEndUTC = $startUTC->copy()->addSeconds($durationSeconds);
                $isBooked   = false;

                foreach ($fetchAllBookings as $booking) {

                    $bookingStartUTC = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $booking->session_date . ' ' . $booking->start_time,
                        'UTC'
                    );

                    $bookingEndUTC = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $booking->session_date . ' ' . $booking->end_time,
                        'UTC'
                    );

                    if ($startUTC < $bookingEndUTC && $slotEndUTC > $bookingStartUTC) {
                        $isBooked = true;
                        break;
                    }
                }

                $chunkedWorkingHours[] = [
                    'id'             => $workingHour->id,
                    'working_day_id' => $workingHour->astro_working_day_id,
                    'start_time'     => $startUTC->copy()
                        ->setTimezone($userTimezone)
                        ->format('H:i:s'),
                    'end_time'       => $slotEndUTC->copy()
                        ->setTimezone($userTimezone)
                        ->format('H:i:s'),
                    'booked'         => $isBooked,
                ];

                $startUTC = $slotEndUTC;
            }
        }

        usort(
            $chunkedWorkingHours,
            fn($a, $b) =>
            strcmp($a['start_time'], $b['start_time'])
        );

        foreach ($chunkedWorkingHours as &$hour) {
            $slotStartUTC = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $request->date . ' ' . $hour['start_time'],
                $this->timeZone,
            )->utc();

            $slotEndUTC = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $request->date . ' ' . $hour['end_time'],
                $this->timeZone,
            )->utc();

            $hour['booked'] = false;

            foreach ($fetchAllBookings as $booking) {

                $bookingStartUTC = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $booking->session_date . ' ' . $booking->start_time,
                    'Asia/Kolkata'
                )->utc();

                $bookingEndUTC = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $booking->session_date . ' ' . $booking->end_time,
                    'Asia/Kolkata'
                )->utc();

                if ($slotStartUTC->equalTo($bookingStartUTC) && $slotEndUTC->equalTo($bookingEndUTC)) {
                    $hour['booked'] = true;
                } elseif ($slotStartUTC < $bookingEndUTC && $slotEndUTC > $bookingStartUTC) {
                    $hour['booked'] = true;
                }
            }
        }

        $works['open_hours'] = $chunkedWorkingHours;
        return $works;
    }
    public function getWorkTimes($request)
    {
        $astro      = Astro::find($request->astro_id);
        $astroPrice = AstroPrice::find($request->price_id);
        if (! $astro) {
            if ($astro->is_online == '0') {
                throw new \Exception("Astro is not available right now");
            }
            throw new \Exception("Astro not found");
        }
        $bookingDate  = DateTime::createFromFormat('Y-m-d', $request->date);
        $dayOfBooking = Str::lower($bookingDate->format('l'));

        $fetchAllBookings = Booking::where('status', '!=', 'cancelled')
            ->where('astro_id', $astro->id)
            ->whereDate('session_date', $bookingDate)->get();

        $works = AstroWorkingDay::with(['workingHours'])->where(['astro_id' => $astro->id, 'day' => $dayOfBooking, 'status' => 'active'])->first();
        if (! $works) {
            $response = [
                'success' => true,
                'message' => 'No booking available on ' . Str::ucfirst($dayOfBooking),
            ];
            return response($response, 400);
        }
        $morningStartTime    = strtotime("00:00:00");
        $eveningEndTime      = strtotime("23:59:00");
        $chunkedWorkingHours = [];
        $previousEndTime     = null;
        $duration            = (int) $astroPrice->duration * 60;

        foreach ($works->workingHours as $workingHour) {
            $startTime = max(strtotime($workingHour["start_time"]), $morningStartTime);
            $endTime   = min(strtotime($workingHour["end_time"]), $eveningEndTime);
            // Calculate hourly chunks
            if ($previousEndTime !== null && $startTime > $previousEndTime) {
                $gapInHour  = (($startTime - $previousEndTime) - 3600) / 3600;
                $chunkCount = 0;
                while ($previousEndTime < $startTime) {
                    $chunkCount++;
                    $isBreak = false;
                    if ($chunkCount > 1) {
                        $isBreak = true;
                    } elseif ($gapInHour == $chunkCount && $chunkCount > 1) {
                        $isBreak = true;
                    }
                    $chunkedWorkingHours[]  = [
                        "id"             => null,
                        "working_day_id" => $workingHour["astro_working_day_id"],
                        "start_time"     => date("H:i:s", $previousEndTime),
                        "end_time"       => date("H:i:s", min($previousEndTime + $duration, $startTime)),
                        "booked"         => false,
                    ];
                    $previousEndTime += $duration;
                }
            }
            while ($startTime < $endTime) {
                $chunkedWorkingHours[] = [
                    "id"             => $workingHour["id"],
                    "working_day_id" => $workingHour["astro_working_day_id"],
                    "start_time"     => date("H:i:s", $startTime),
                    "end_time"       => date("H:i:s", min($startTime + $duration, $endTime)), // 3600 seconds = 1 hour
                    'booked'         => false,
                ];

                $startTime += $duration;
            }
            $previousEndTime = $endTime;
        }
        usort($chunkedWorkingHours, function ($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });

        foreach ($chunkedWorkingHours as &$hour) {
            $hourStartTime  = strtotime($hour['start_time']);
            $hourEndTime    = strtotime($hour['end_time']);
            $hour['booked'] = false;
            foreach ($fetchAllBookings as $booking) {
                $bookingStartTime = strtotime($booking->start_time);
                $bookingEndTime   = strtotime($booking->end_time);
                if ((($hourStartTime >= $bookingStartTime && $hourStartTime < $bookingEndTime) ||
                    ($hourEndTime > $bookingStartTime && $hourEndTime <= $bookingEndTime) ||
                    ($hourStartTime <= $bookingStartTime && $hourEndTime >= $bookingEndTime))) {
                    $hour['booked'] = true;
                    break;
                }
            }
        }

        $works['open_hours'] = $chunkedWorkingHours;
        return $works;
    }

    public function isTimeSlotAvailable($astroId, $date, $time, $priceId)
    {
        $astro      = Astro::find($astroId);
        $astroPrice = AstroPrice::find($priceId);
        if (! $astro) {
            throw new \Exception("Astro not found");
        }
        if ($astro->is_online == '0') {
            throw new \Exception("Astro is not available right now");
        }

        $bookingDate  = DateTime::createFromFormat('Y-m-d', $date);
        $dayOfBooking = Str::lower($bookingDate->format('l'));

        $fetchAllBookings = Booking::where('status', '!=', 'cancelled')
            ->where('astro_id', $astro->id)
            ->whereDate('session_date', $bookingDate)->get();

        $works = AstroWorkingDay::with(['workingHours'])->where(['astro_id' => $astro->id, 'day' => $dayOfBooking, 'status' => 'active'])->first();
        if (! $works) {
            throw new \Exception("No booking available on " . Str::ucfirst($dayOfBooking));
        }
        $morningStartTime    = strtotime("00:00:00");
        $eveningEndTime      = strtotime("23:59:00");
        $chunkedWorkingHours = [];
        $previousEndTime     = null;
        $duration            = (int) $astroPrice->duration * 60;

        foreach ($works->workingHours as $workingHour) {
            $startTime = max(strtotime($workingHour["start_time"]), $morningStartTime);
            $endTime   = min(strtotime($workingHour["end_time"]), $eveningEndTime);
            // Calculate hourly chunks
            if ($previousEndTime !== null && $startTime > $previousEndTime) {
                $gapInHour  = (($startTime - $previousEndTime) - 3600) / 3600;
                $chunkCount = 0;
                while ($previousEndTime < $startTime) {
                    $chunkCount++;
                    $isBreak = false;
                    if ($chunkCount > 1) {
                        $isBreak = true;
                    } elseif ($gapInHour == $chunkCount && $chunkCount > 1) {
                        $isBreak = true;
                    }
                    $chunkedWorkingHours[]  = [
                        "id"             => null,
                        "working_day_id" => $workingHour["astro_working_day_id"],
                        "start_time"     => date("H:i:s", $previousEndTime),
                        "end_time"       => date("H:i:s", min($previousEndTime + $duration, $startTime)),
                        "booked"         => false,
                    ];
                    $previousEndTime += $duration;
                }
            }
            while ($startTime < $endTime) {
                $chunkedWorkingHours[] = [
                    "id"             => $workingHour["id"],
                    "working_day_id" => $workingHour["astro_working_day_id"],
                    "start_time"     => date("H:i:s", $startTime),
                    "end_time"       => date("H:i:s", min($startTime + $duration, $endTime)), // 3600 seconds = 1 hour
                    'booked'         => false,
                ];

                $startTime += $duration;
            }
            $previousEndTime = $endTime;
        }
        usort($chunkedWorkingHours, function ($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });
        foreach ($chunkedWorkingHours as &$hour) {
            $hourStartTime  = strtotime($hour['start_time']);
            $hourEndTime    = strtotime($hour['end_time']);
            $hour['booked'] = false;
            foreach ($fetchAllBookings as $booking) {
                $bookingStartTime = strtotime($booking->start_time);
                $bookingEndTime   = strtotime($booking->end_time);
                if ((($hourStartTime >= $bookingStartTime && $hourStartTime < $bookingEndTime) ||
                    ($hourEndTime > $bookingStartTime && $hourEndTime <= $bookingEndTime) ||
                    ($hourStartTime <= $bookingStartTime && $hourEndTime >= $bookingEndTime))) {
                    $hour['booked'] = true;
                    break;
                }
            }
        }
        foreach ($chunkedWorkingHours as $hour) {
            if ($hour['start_time'] == $time) {
                return ! $hour['booked'];
            }
        }
        return false;
    }
}
