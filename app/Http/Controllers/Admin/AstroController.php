<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Models\AstroPrice;
use App\Models\AstroWorkingDay;
use App\Models\AstroWorkingHour;
use App\Models\Language;
use App\Models\Service;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AstroController extends Controller
{
    public function index()
    {
        $pageConfigs = ['has_icon' => true];
        return view('content.tables.astros.index', compact('pageConfigs'));
    }
    public function create()
    {
        $skills    = Skill::active()->get();
        $languages = Language::active()->get();
        $services  = Service::active()->get();
        return view('content.tables.astros.add', compact('skills', 'languages', 'services'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name'                            => 'required|string|max:255',
            'middle_name'                           => 'nullable|string|max:255',
            'last_name'                             => 'nullable|string|max:255',
            'profile'                               => 'nullable|mimes:jpeg,png,jpg|max:250',
            'gender'                                => 'required|in:male,female,other',
            'email'                                 => 'nullable|email',
            'mobile'                                => 'required|string|unique:astros,mobile',
            'skills'                                => 'nullable|array',
            'skills.*'                              => 'numeric|exists:skills,id',
            'languages'                             => 'nullable|array',
            'languages.*'                           => 'numeric|exists:languages,id',
            'services'                              => 'nullable|array',
            'services.*'                            => 'numeric|exists:services,id',
            'experience'                            => 'nullable|string|max:255',
            'voice_call_charge'                     => 'nullable|numeric',
            'video_call_charge'                     => 'nullable|numeric',
            'chat_charge'                           => 'nullable|numeric',
            'voice_call_charge_dollar'              => 'nullable|numeric',
            'video_call_charge_dollar'              => 'nullable|numeric',
            'chat_charge_dollar'                    => 'nullable|numeric',
            'service_charges'                       => 'nullable|array',
            'service_charges.*.charge_dollar'       => 'required|numeric',
            'service_charges.*.charge'              => 'required|numeric',
            'service_charges.*.duration'            => 'required',
            'service_charges.*.question'            => 'required',
            'days'                                  => 'required|array',
            'available_for_schedule_consultation'   => 'nullable|array',
            'available_for_schedule_consultation.*' => 'in:voice,video',
        ]);
        // return '';
        DB::beginTransaction();
        // $skills =  Skill::whereIn('id', $request->skills)->get(['id', 'name', 'icon']);
        // $skills =   $skills->toJson();

        $known_languages = Language::whereIn('id', $request->languages)->get(['id', 'name']);
        $known_languages = $known_languages->toJson();

        $data = Astro::create([
            'first_name'                          => $request->first_name,
            'middle_name'                         => $request->middle_name,
            'last_name'                           => $request->last_name,
            'email'                               => $request->email,
            'gender'                              => $request->gender,
            'mobile'                              => $request->mobile,
            'profile'                             => $request->hasFile('profile') ? FileUploader::optimizeUploadFile($request->file('profile'), 'images/astros') : null,
            // 'skills' => $skills,
            'known_languages'                     => $known_languages,
            'experience'                          => $request->experience,
            'description'                         => $request->description,
            'instant_voice_call_charge'           => $request->voice_call_charge,
            'instant_video_call_charge'           => $request->video_call_charge,
            'instant_chat_charge'                 => $request->chat_charge,
            'instant_voice_call_charge_dollar'    => $request->voice_call_charge_dollar,
            'instant_video_call_charge_dollar'    => $request->video_call_charge_dollar,
            'instant_chat_charge_dollar'          => $request->chat_charge_dollar,
            'available_for_schedule_consultation' => isset($request->available_for_schedule_consultation) ? implode(',', $request->available_for_schedule_consultation) : '',
            'country_id'                          => 101,
        ]);
        $data->services()->attach($request->services);
        $data->skills()->attach($request->skills);

        $insertData = [];

        foreach ($request->service_charges as $service) {
            // foreach ($service['services'] as $value) {
            $insertData[] = [
                'service_id'    => $service['services'],
                'astro_id'      => $data->id,
                'duration'      => $service['duration'],
                'questions'     => $service['question'],
                'charge'        => $service['charge'],
                'charge_dollar' => $service['charge_dollar'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
            // }
        }

        if (! empty($insertData)) {
            AstroPrice::insert($insertData);
        }
        foreach ($request->days as $key => $day) {
            $workDay = AstroWorkingDay::firstOrCreate(
                [
                    'day'      => $key,
                    'astro_id' => $data->id,
                    'status'   => $day[$key . '_status'] == 'on' ? 'active' : 'blocked',
                ],
            );
            AstroWorkingHour::create([
                'astro_id'             => $data->id,
                'astro_working_day_id' => $workDay->id,
                'start_time'           => date('H:i:s', strtotime($day[$key . '_start_time'])),
                'end_time'             => date('H:i:s', strtotime($day[$key . '_end_time'])),
            ]);
        }
        DB::commit();
        return response([
            'header'  => 'Success!',
            'message' => 'Astro Added successfully',
            'table'   => 'astro-table',
        ]);
    }
    public function edit($id)
    {
        $data = Astro::with(['services', 'skills', 'price' => function ($query) {
            $query->whereNotNull('service_id');
        }])->findOrFail($id);
        $skills    = Skill::active()->get();
        $languages = Language::active()->get();
        $services  = Service::active()->get();

        return view('content.tables.astros.edit', compact('data', 'skills', 'languages', 'services'));
    }

    public function update(Request $request)
    {
        // \Log::info($request->all());
        $request->validate([
            'id'                                    => 'required|numeric',
            'mobile'                                => 'required|string|unique:astros,mobile,' . $request->id,
            'first_name'                            => 'required|string|max:255',
            'middle_name'                           => 'nullable|string|max:255',
            'last_name'                             => 'nullable|string|max:255',
            'profile'                               => 'nullable|mimes:jpeg,png,jpg|max:250',
            'gender'                                => 'required|in:male,female,other',
            'email'                                 => 'nullable|email',
            'skills'                                => 'nullable|array',
            'skills.*'                              => 'numeric|exists:skills,id',
            'languages'                             => 'nullable|array',
            'languages.*'                           => 'numeric|exists:languages,id',
            'services'                              => 'nullable|array',
            'services.*'                            => 'numeric|exists:services,id',
            'experience'                            => 'nullable|string|max:255',
            'voice_call_charge'                     => 'nullable|numeric',
            'video_call_charge'                     => 'nullable|numeric',
            'chat_charge'                           => 'nullable|numeric',
            'voice_call_charge_dollar'              => 'nullable|numeric',
            'video_call_charge_dollar'              => 'nullable|numeric',
            'chat_charge_dollar'                    => 'nullable|numeric',
            'service_charges'                       => 'nullable|array',
            'service_charges.*.charge_dollar'       => 'required|numeric',
            'service_charges.*.charge'              => 'required|numeric',
            'service_charges.*.duration'            => 'required',
            'service_charges.*.question'            => 'required|numeric',
            'available_for_schedule_consultation'   => 'nullable|array',
            'available_for_schedule_consultation.*' => 'in:voice,video',
        ]);
        // return '';
        DB::beginTransaction();
        // $skills =  Skill::whereIn('id', $request->skills)->get(['id', 'name', 'icon']);
        // $skills =   $skills->toJson();

        $known_languages = Language::whereIn('id', $request->languages)->get(['id', 'name']);
        $known_languages = $known_languages->toJson();
        $data            = Astro::findOrFail($request->id);
        $data->update([
            'first_name'                          => $request->first_name,
            'middle_name'                         => $request->middle_name,
            'last_name'                           => $request->last_name,
            'email'                               => $request->email,
            'gender'                              => $request->gender,
            'mobile'                              => $request->mobile,
            'profile'                             => $request->hasFile('profile') ? FileUploader::optimizeUploadFile($request->file('profile'), 'images/astros') : $data->profile,
            // 'skills' => $skills,
            'known_languages'                     => $known_languages,
            'experience'                          => $request->experience,
            'description'                         => $request->description,
            'instant_voice_call_charge'           => $request->voice_call_charge,
            'instant_video_call_charge'           => $request->video_call_charge,
            'instant_chat_charge'                 => $request->chat_charge,
            'instant_voice_call_charge_dollar'    => $request->voice_call_charge_dollar,
            'instant_video_call_charge_dollar'    => $request->video_call_charge_dollar,
            'instant_chat_charge_dollar'          => $request->chat_charge_dollar,
            'available_for_schedule_consultation' => count($request->available_for_schedule_consultation ?? []) > 0 ? implode(',', $request->available_for_schedule_consultation) : null,
        ]);
        $data->services()->sync($request->services);
        $data->skills()->sync($request->skills);
        foreach ($request->prices as $key => $price) {
            if (! $price['price_id'] && $price['price_id'] == null) {
                foreach ($price['price_services'] as $service_id) {
                    AstroPrice::create([
                        'astro_id'      => $data->id,
                        'service_id'    => $service_id,
                        'duration'      => $price['duration'],
                        'questions'     => $price['question'],
                        'charge'        => $price['charge'],
                        'charge_dollar' => $price['charge_dollar'],
                    ]);
                }
                continue;
            }
            if (isset($price['price_id']) && $price['price_id']) {
                foreach ($price['price_services'] as $service_id) {
                    // AstroPrice::where(['astro_id' => $data->id, 'duration' => $price['duration']])
                    //     ->whereNotIn('service_id', $price['price_services'])
                    //     ->delete();
                    AstroPrice::updateOrCreate(
                        [
                            'astro_id'   => $data->id,
                            'service_id' => $service_id,
                            'duration'   => $price['duration'],
                        ],
                        [
                            'questions'     => $price['question'],
                            'charge'        => $price['charge'],
                            'charge_dollar' => $price['charge_dollar'],
                        ]
                    );
                }
            }
        }
        DB::commit();
        return response([
            'header'  => 'Success!',
            'message' => 'Astro updated successfully',
            'table'   => 'astro-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id'     => 'required|numeric|exists:astros,id',
            'status' => 'required|in:active,blocked',
        ]);

        Astro::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'astro status updated successfully',
            'table'   => 'astro-table',
        ]);
    }

    public function destroy($id)
    {
        Astro::findOrFail($id)->delete();
        return response([
            'header'  => 'Deleted!',
            'message' => 'astro deleted successfully',
            'table'   => 'astro-table',
        ]);
    }

    public function details($id)
    {
        $data = Astro::with('workingDays', 'workingDays.workingHours')->findOrFail($id);
        return view('content.tables.astros.details', compact('data'));
    }

    public function deletePrice(Request $request)
    {
        if ($request->id) {
            $request->validate([
                'id' => 'required|numeric|exists:astro_prices,id',
            ]);
            AstroPrice::findOrFail($request->id)->delete();
            return response([
                'message' => 'price deleted successfully!',
            ]);
        }
    }

    public function getAstroTimeLine(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        $getTimeLine  = AstroWorkingDay::with(['workingHours'])->where('astro_id', $request->id)->get();
        $html         = '';
        $html        .= view('components.astro.manageTimeLine', ['data' => $getTimeLine])->render();
        return $html;
    }

    public function updateTimeLine(Request $request)
    {
        $request->validate([
            'id'   => 'required|numeric',
            'days' => 'required|array',
        ]);
        $astro = Astro::findOrFail($request->id);
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

        return response([
            'header'  => 'Success!',
            'message' => 'Astro Added successfully',
            'table'   => 'astro-table',
        ]);
    }

    public function getAstroPrices(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        $services  = Service::active()->get();
        $prices    = AstroPrice::where('astro_id', $request->id)
            ->whereNotNull('service_id')
            ->get();
        $html  = '';
        $html .= view('components.astro.prices', ['prices' => $prices, 'services' => $services])->render();
        return $html;
    }

    public function updatePricing(Request $request)
    {
        $request->validate([
            'id'        => 'required|numeric',
            'duration'  => 'nullable|array',
            'question'  => 'nullable|array',
            'charge'    => 'nullable|array',
            'price_ids' => 'nullable|array',
        ]);
        $astro = Astro::findOrFail($request->id);
        foreach ($request->prices as $key => $price) {
            if ($price['price_id'] == null) {
                foreach ($price['price_services'] as $service_id) {
                    AstroPrice::create([
                        'astro_id'   => $astro->id,
                        'service_id' => $service_id,
                        'duration'   => $price['duration'],
                        'questions'  => $price['question'],
                        'charge'     => $price['charge'],
                    ]);
                }
                continue;
            }
            if ($price['price_id']) {
                foreach ($price['price_services'] as $service_id) {
                    AstroPrice::where(['astro_id' => $astro->id, 'duration' => $price['duration']])
                        ->whereNotIn('service_id', $price['price_services'])
                        ->delete();
                    AstroPrice::updateOrCreate(
                        [
                            'astro_id'   => $astro->id,
                            'service_id' => $service_id,
                            'duration'   => $price['duration'],
                        ],
                        [
                            'questions' => $price['question'],
                            'charge'    => $price['charge'],
                        ]
                    );
                }
            }
        }
        return response([
            'header'  => 'Success!',
            'message' => 'Astro price updated successfully',
            'table'   => 'astro-table',
        ]);
    }
    public function updateInstantPricing(Request $request)
    {
        $request->validate([
            'id'                => 'required|numeric',
            'voice_call_charge' => 'nullable|numeric',
            'video_call_charge' => 'nullable|numeric',
            'chat_charge'       => 'nullable|numeric',
        ]);
        $astro = Astro::findOrFail($request->id);
        $astro->update([
            'instant_voice_call_charge' => $request->voice_call_charge,
            'instant_video_call_charge' => $request->video_call_charge,
            'instant_chat_charge'       => $request->chat_charge,
        ]);
        return response([
            'header'  => 'Success!',
            'message' => 'Astro instant price updated successfully',
            'table'   => 'astro-table',
        ]);
    }
}
