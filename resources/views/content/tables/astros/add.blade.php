@extends('layouts/contentLayoutMaster')

@section('title', 'Add Astro')
@section('page-style')
    <style>
    </style>
@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card title="Add Astro">
                    <x-form id="add-astro" method="POST" class="" :route="route('admin.astros.store')">
                        <div class="col-md-3 col-3">
                            <x-input name="first_name" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="middle_name" :required="false" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="last_name" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input-file name="profile" :required="false" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-select name="gender" :options="['male', 'female', 'other']" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="mobile" maxLength="10" pattern="[1-9]{1}[0-9]{9}" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="email" :required="false" />
                        </div>
                        <div class="col-lg-3 col-3 mt-2">
                            <div class="d-flex mx-2">
                                <label for="" class="mx-2">Available For : </label>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox"
                                        name="available_for_schedule_consultation[]" value="voice" id="voice_call">
                                    <label class="form-check-label" for="voice_call"> Voice Call </label>
                                </div>
                                <div class="form-check me-3 ml-2">
                                    <input class="form-check-input" type="checkbox"
                                        name="available_for_schedule_consultation[]" value="video" id="video_call">
                                    <label class="form-check-label" for="video_call">
                                        Video Call </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-12">
                            <x-input type="textarea" name="description" />
                        </div>
                        <x-divider text="Skills & Experience" />
                        <div class="col-md-3 col-3">
                            <x-select name="skills" :multiple="true" :options="$skills" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-select name="languages" :multiple="true" :options="$languages" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-select name="services" :multiple="true" :options="$services" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-select name="experience" :options="[
                                1,
                                2,
                                3,
                                4,
                                5,
                                6,
                                7,
                                8,
                                9,
                                10,
                                11,
                                12,
                                13,
                                14,
                                15,
                                16,
                                17,
                                18,
                                19,
                                20,
                                21,
                                22,
                                23,
                                24,
                                25,
                                26,
                                27,
                                28,
                                29,
                                30,
                            ]" />
                        </div>
                        <x-divider text="Instant Consultant Charges in INR" />
                        <div class="col-md-4 col-12">
                            <x-input name="voice_call_charge" label="Voice Call Charge (per minute)" type="number"
                                :attrs="'step=any'" />
                        </div>

                        <div class="col-md-4 col-12">
                            <x-input name="video_call_charge" label="Video Call Charge (per minute)" type="number"
                                :attrs="'step=any'" />
                        </div>

                        <div class="col-md-4 col-12">
                            <x-input name="chat_charge" label="Chat Charge (per minute)" type="number"
                                :attrs="'step=any'" />
                        </div>

                        <x-divider text="Instant Consultant Charges in Dollar" />
                        <div class="col-md-4 col-12">
                            <x-input name="voice_call_charge_dollar" placeholder="Enter voice call charge"
                                label="Voice Call Charge (per minute)" type="number" :attrs="'step=any'" />
                        </div>
                        <div class="col-md-4 col-12">
                            <x-input name="video_call_charge_dollar" placeholder="Enter video call charge"
                                label="Video Call Charge (per minute)" type="number" :attrs="'step=any'" />
                        </div>
                        <div class="col-md-4 col-12">
                            <x-input name="chat_charge_dollar" placeholder="Enter chat charge"
                                label="Chat Charge (per minute)" type="number" :attrs="'step=any'" />
                        </div>
                        <x-divider text="Service Charges" />

                        <div class="col-lg-12 col-12">
                            @php
                                $duration = [
                                    (object) ['id' => '15', 'name' => '15 Minutes'],
                                    (object) ['id' => '30', 'name' => '30 Minutes'],
                                    (object) ['id' => '60', 'name' => '1 Hour'],
                                ];
                            @endphp
                            <x-repeater name="service_charges" :fields="[
                                [
                                    'col' => '2',
                                    'type' => 'select',
                                    'name' => 'services',
                                    // 'multiple' => true,
                                    'options' => $services,
                                ],
                                [
                                    'col' => '2',
                                    'type' => 'select',
                                    'name' => 'duration',
                                    'options' => $duration,
                                ],
                                [
                                    'col' => '2',
                                    'name' => 'question',
                                    'required' => 'false',
                                    'label' => 'Question',
                                ],
                                [
                                    'col' => '2',
                                    'type' => 'number',
                                    'name' => 'charge',
                                    'label' => 'Charge in INR',
                                ],
                                [
                                    'col' => '2',
                                    'type' => 'number',
                                    'name' => 'charge_dollar',
                                    'label' => 'Charge in Dollar',
                                ],
                            ]" />
                        </div>
                        <x-divider text="Consultation availability" />
                        <div class="col-lg-12 col-12">
                            <table class="table table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tbody>
                                    @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                        <tr>
                                            <td>
                                                {{ ucfirst($day) }}
                                            </td>
                                            <td>
                                                <x-input type="hidden" :required="false"
                                                    name="days[{{ $day }}][{{ $day }}_status]"
                                                    value="off" />
                                                <x-custom-switch
                                                    name="days[{{ $day }}][{{ $day }}_status]"
                                                    id="{{ $day }}_status" label=" " />
                                            </td>
                                            <td>
                                                <x-input name="days[{{ $day }}][{{ $day }}_start_time]"
                                                    id="{{ $day }}_start_time" label="Start time"
                                                    placeholder="Enter start time" />
                                            </td>
                                            <td>
                                                <x-input name="days[{{ $day }}][{{ $day }}_end_time]"
                                                    id="{{ $day }}_end_time" label="End time"
                                                    placeholder="Enter end time" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </x-form>
                </x-card>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        const goodTime = `${new Date().getHours()}:${new Date().getMinutes()}`;
        $("#sunday_start_time,#sunday_end_time,#monday_start_time,#monday_end_time,#tuesday_start_time,#tuesday_end_time,#wednesday_start_time,#wednesday_end_time,#thursday_start_time,#thursday_end_time,#friday_start_time,#friday_end_time,#saturday_start_time,#saturday_end_time")
            .flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                time_24hr: true,
                minuteIncrement: 60
            });
    </script>
@endsection
