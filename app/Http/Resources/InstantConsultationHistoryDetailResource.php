<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Country;
use App\Http\Resources\AstroResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InstantConsultationHistoryDetailResource extends JsonResource
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
            'id'                     => $this->id,
            'user_id'                => $this->user_id,
            'astro_id'               => $this->astro_id,
            'connection_request_id'  => $this->connection_request_id,
            'amount'                 => $this->amount,
            'currency'               => $this->currency,
            'session_type'           => $this->session_type,
            'type'                   => $this->type,
            'status'                 => $this->status,
            'started_at'             => $this->convertTimestampToLocale($this->started_at),
            'ended_at'               => $this->convertTimestampToLocale($this->ended_at),
            'duration'               => $this->duration,
            'session_rate'           => $this->session_rate,
            'remark'                 => $this->remark,
            'created_at'             => $this->convertTimestampToLocale($this->created_at),
            'updated_at'             => $this->convertTimestampToLocale($this->updated_at),
            'astro'  => new AstroResource($this->whenLoaded('astro')),
            'chats'  => ChatResource::collection($this->whenLoaded('chats')),
            'media'  => $this->whenLoaded('media'),
            'review' => $this->review,
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
