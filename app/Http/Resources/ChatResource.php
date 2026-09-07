<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'conversation_id' => $this->conversation_id,
            'message' => $this->message,
            'file' => $this->file,
            'sender_type' => $this->sender_type,
            'sender_id' => $this->sender_id,
            'created_at' => $this->convertTimestampToLocale($this->created_at),
            'updated_at' => $this->convertTimestampToLocale($this->updated_at),
        ];
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
