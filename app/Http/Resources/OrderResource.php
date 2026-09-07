<?php
namespace App\Http\Resources;

use App\Http\Resources\OrderDetailResource;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'name'               => $this->name,
            'email'              => $this->email,
            'mobile'             => $this->mobile,
            'promocode'          => $this->promocode,
            'promocode_discount' => $this->promocode_discount,
            'sub_total'          => $this->sub_total,
            'total'              => $this->total,
            'order_uid'          => $this->order_uid,
            'address'            => json_decode($this->address),
            'recommended_by'     => json_decode($this->recommended_by),
            'status'             => $this->status,
            'payment_mode'       => $this->payment_mode,
            'confirmed_at'       => $this->confirmed_at ? $this->convertTimestampToLocale($this->confirmed_at) : null,
            'shipped_at'         => $this->shipped_at ? $this->convertTimestampToLocale($this->shipped_at) : null,
            'delivered_at'       => $this->delivered_at ? $this->convertTimestampToLocale($this->delivered_at) : null,
            'canceled_at'        => $this->canceled_at ? $this->convertTimestampToLocale($this->canceled_at) : null,
            'created_at'         => $this->created_at ? $this->convertTimestampToLocale($this->created_at) : null,
            'updated_at'         => $this->updated_at ? $this->convertTimestampToLocale($this->updated_at) : null,
            'order_details'      => OrderDetailResource::collection($this->whenLoaded('orderDetails')),
            'transaction'        => $this->whenLoaded('transaction'),
            'currency'           => $this->currency,
        ];
    }
    public function convertTimeToLocale($time)
    {
        $user     = auth('sanctum')->user();
        $astro    = auth('astro')->user();
        $country  = Country::find($user->country_id ?? $astro->country_id);
        $timeZone = json_decode($country->timezones);
        $timeZone = $timeZone[0]->zoneName;
        $time     = Carbon::parse(
            $time,
            'Asia/Kolkata'
        )->setTimezone($timeZone)->format('H:i:s');
        return $time;
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
}
