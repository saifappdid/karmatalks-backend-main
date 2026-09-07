<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'astro_id' => $this->astro_id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'session_date' => $this->session_date,
            'start_time' => $this->convertTimeToLocale($this->start_time),
            'end_time' => $this->convertTimeToLocale($this->end_time),
            'duration' => $this->duration,
            'questions' => $this->questions,
            'promocode' => $this->promocode,
            'promocode_discount' => $this->promocode_discount,
            'wallet' => $this->wallet,
            'sub_total' => $this->sub_total,
            'final_total' => $this->final_total,
            'booking_uid' => $this->booking_uid,
            'booking_data' => json_decode($this->booking_data),
            'status' => $this->status,
            'pending_at' => $this->pending_at,
            'started_at' => $this->started_at ? $this->convertTimestampToLocale($this->started_at) : null,
            'ended_at' =>   $this->started_at ? $this->convertTimestampToLocale($this->ended_at) : null,
            'created_at' => $this->convertTimestampToLocale($this->created_at),
            'updated_at' => $this->convertTimestampToLocale($this->updated_at),
            'transaction' => $this->whenLoaded('transaction'),
            'schedule_consultation_log' => $this->whenLoaded('scheduleConsultationLog'),
            'schedule_consultation_media' => $this->whenLoaded('media'),
            'review' => $this->whenLoaded('review'),
            'user' => $this->whenLoaded('user'),
            'currency' => $this->currency,
            'review' => $this->review,
            'name' => $this->name,
            'date_of_birth' => $this->date_of_birth,
            'place' => $this->place,
        ];
    }

    public function convertTimeToLocale($time)
    {
        $user = auth('sanctum')->user();
        $astro = auth('astro')->user();
        $country = Country::find($user->country_id ?? $astro->country_id);
        $timeZone = json_decode($country->timezones);
        $timeZone = $timeZone[0]->zoneName;
        $time = Carbon::parse(
            $time,
            'Asia/Kolkata'
        )->setTimezone($timeZone)->format('H:i:s');
        return $time;
    }

    function convertTimestampToLocale(
        $timestamp,
        string $format = 'Y-m-d H:i:s'
    ) {
        $user = auth('sanctum')->user();
        $astro = auth('astro')->user();
        $country = Country::find($user->country_id ?? $astro->country_id);
        $timeZone = json_decode($country->timezones);
        $timeZone = $timeZone[0]->zoneName;
        return Carbon::parse($timestamp, 'Asia/Kolkata')
            ->setTimezone($timeZone)
            ->format($format);
    }
}
