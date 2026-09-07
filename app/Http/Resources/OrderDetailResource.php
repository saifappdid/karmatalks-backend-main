<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product' => json_decode($this->product),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'created_at' => $this->convertTimestampToLocale($this->created_at),
            'updated_at' => $this->convertTimestampToLocale($this->updated_at),
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
