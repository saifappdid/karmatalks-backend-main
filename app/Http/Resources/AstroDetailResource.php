<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AstroDetailResource extends JsonResource
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
            'id'                                  => $this->id,
            'first_name'                          => $this->first_name,
            'middle_name'                         => $this->middle_name,
            'last_name'                           => $this->last_name,
            'email'                               => $this->email,
            'mobile'                              => $this->mobile,
            'profile'                             => $this->profile,
            'description'                         => $this->description,
            'gender'                              => $this->gender,
            'dob'                                 => $this->dob,
            'address'                             => $this->address,
            'experience'                          => $this->experience,
            'known_languages'                     => json_decode($this->known_languages),
            'skills' => json_decode($this->skills),
            // 'skills'                              => $this->whenLoaded('skills', function () {
            //     return $this->skills->map(function ($skill) {
            //         return [
            //             'id'   => $skill->id,
            //             'name' => $skill->name,
            //         ];
            //     });
            // }),
            'services'                            => json_decode($this->services),
            'is_online'                           => $this->is_online,
            'instant_voice_call_charge'           => $this->instant_voice_call_charge,
            'instant_video_call_charge'           => $this->instant_video_call_charge,
            'instant_chat_charge'                 => $this->instant_chat_charge,
            'instant_voice_call_charge_dollar' => $this->instant_voice_call_charge_dollar,
            'instant_video_call_charge_dollar' => $this->instant_video_call_charge_dollar,
            'instant_chat_charge_dollar' => $this->instant_chat_charge_dollar,
            'online_for_voice_call'               => $this->online_for_voice_call,
            'online_for_video_call'               => $this->online_for_video_call,
            'online_for_chat'                     => $this->online_for_chat,
            'available_for_schedule_consultation' => $this->available_for_schedule_consultation,
            'created_at'                          => $this->created_at,
            'updated_at'                          => $this->updated_at,
            'status'                              => $this->status,
            'price'                               => $this->when(
                $this->relationLoaded('price'),
                fn() => $this->price->map(function ($price) {
                    return [
                        'id'         => $price->id,
                        'astro_id'   => $price->astro_id,
                        'duration'   => $price->duration,
                        'questions'  => $price->questions,
                        'charge'     => $price->charge,
                        'charge_dollar' => $price->charge_dollar,
                        'created_at' => $price->created_at,
                        'updated_at' => $price->updated_at,
                        'status'     => $price->status,
                    ];
                })
            ),
            'review'                              => $this->whenLoaded('review'),
            'average_rating'                      => $this->review_count > 0
                ?  number_format($this->review_sum_rating / $this->review_count, 0)
                : 0.00,
            'working_days'                        => $this->whenLoaded('workingDays'),
        ];
    }
}
