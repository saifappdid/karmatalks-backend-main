@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Astro')
@section('page-style')
    <style>
    </style>
@endsection

@section('content')
    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card title="Edit Astro">
                    <x-form successCallback="redirect" id="edit-astro" method="POST" class="" :route="route('admin.astros.update')">
                        <div class="col-md-3 col-3">
                            <x-input name="first_name" :value="$data->first_name" />
                            <x-input name="id" :value="$data->id" type="hidden" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="middle_name" :value="$data->middle_name" :required="false" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="last_name" :value="$data->last_name" />
                        </div>
                        <div class="col-md-2 col-2">
                            <x-input-file name="profile" :required="false" />
                        </div>
                        <div class="col-md-1 col-1 mt-2">
                            <div class="avatar avatar-lg">
                                <img class="view-on-click" src="{{ $data->profile }}" alt="avatar">
                            </div>

                        </div>
                        <div class="col-md-3 col-3">
                            <x-select name="gender" :select="$data->gender" :options="['male', 'female', 'other']" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="mobile" :value="$data->mobile" maxLength="10" pattern="[1-9]{1}[0-9]{9}" />
                        </div>
                        <div class="col-md-3 col-3">
                            <x-input name="email" :required="false" :value="$data->email" />
                        </div>
                        <div class="col-lg-3 col-3 mt-2">
                            @php
                                $availableForServices = explode(',', $data->available_for_schedule_consultation);
                            @endphp
                            <div class="d-flex mx-2">
                                <label for="" class="mx-2">Available For : </label>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox"
                                        name="available_for_schedule_consultation[]" value="voice" id="voice_call"
                                        @if (in_array('voice', $availableForServices)) {{ 'checked' }} @endif>
                                    <label class="form-check-label" for="voice_call"> Voice Call </label>
                                </div>
                                <div class="form-check me-3 ml-2">
                                    <input class="form-check-input" type="checkbox"
                                        name="available_for_schedule_consultation[]" value="video" id="video_call"
                                        @if (in_array('video', $availableForServices)) {{ 'checked' }} @endif>
                                    <label class="form-check-label" for="video_call">
                                        Video Call </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-12">
                            <x-input type="textarea" name="description" value="{!! $data->description !!}" />
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
                            <x-select name="experience" :select="$data->experience" :options="[
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
                            <x-input name="voice_call_charge" label="Voice Call Charge (per minute)" :value="$data->instant_voice_call_charge"
                                type="number" />
                        </div>
                        <div class="col-md-4 col-12">
                            <x-input name="video_call_charge" label="Video Call Charge (per minute)" :value="$data->instant_video_call_charge"
                                type="number" />
                        </div>
                        <div class="col-md-4 col-12">
                            <x-input name="chat_charge" label="Chat Charge (per minute)" :value="$data->instant_chat_charge"
                                type="number" />
                        </div>

                        <x-divider text="Instant Consultant Charges in Dollar" />
                        <div class="col-md-4 col-12">
                            <x-input name="voice_call_charge_dollar" placeholder="Enter voice call charge" label="Voice Call Charge (per minute)" :value="$data->instant_voice_call_charge_dollar"
                                type="number" />
                        </div>
                        <div class="col-md-4 col-12">
                            <x-input name="video_call_charge_dollar" placeholder="Enter video call charge" label="Video Call Charge (per minute)" :value="$data->instant_video_call_charge_dollar"
                                type="number" />
                        </div>
                        <div class="col-md-4 col-12">
                            <x-input name="chat_charge_dollar" placeholder="Enter chat charge" label="Chat Charge (per minute)" :value="$data->instant_chat_charge_dollar"
                                type="number" />
                        </div>

                        <x-divider text="Service Charges" />
                        <div class="col-lg-12 col-12" id="main-container">
                            @php
                                $duration = [
                                    (object) ['id' => '15', 'name' => '15 Minutes'],
                                    (object) ['id' => '30', 'name' => '30 Minutes'],
                                    (object) ['id' => '60', 'name' => '1 Hour'],
                                ];
                            @endphp
                            <div data-repeater-list="prices">
                                @if (count($data->price) > 0)
                                    @forelse ($data->price as $price)
                                        <div class="row container-item" data-index="0">
                                            <input type="hidden" name="prices[0][price_id]" value="{{ $price->id ?? '' }}">

                                            <div class="col-lg-2 col-12">
                                                <label>Services</label>
                                                <select name="prices[0][price_services][]" class="select2 form-control">
                                                    <option value="" disabled>Select Services</option>
                                                    @foreach ($services as $service)
                                                        <option value="{{ $service->id }}"
                                                            @if ($service->id == $price->service_id) selected @endif>
                                                            {{ $service->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-lg-2 col-12">
                                                <x-select name="prices[0][duration]" :select="$price->duration ?? ''" label="Duration"
                                                    :options="$duration" placeholder="Select duration" />
                                            </div>

                                            <div class="col-lg-2 col-12">
                                                <x-input name="prices[0][question]" label="Questions"
                                                    value="{{ $price->questions ?? '' }}" placeholder="Enter questions"
                                                    :required="false" />
                                            </div>

                                            <div class="col-lg-2 col-12">
                                                <x-input name="prices[0][charge]" label="Charge"
                                                    value="{{ $price->charge ?? '' }}" placeholder="Enter charge"
                                                    type="number" />
                                            </div>

                                            <div class="col-lg-2 col-12">
                                                <x-input name="prices[0][charge_dollar]" label="Charge in Dollar"
                                                    value="{{ $price->charge_dollar ?? '' }}" placeholder="Enter charge in dollar"
                                                    type="number" />
                                            </div>

                                            <div class="col-md-2 col-12 d-flex align-items-center">
                                                <button type="button" data-option-delete="{{ $price->id }}"
                                                    class="btn btn-outline-danger delete-item text-nowrap px-1">
                                                    <i data-feather="x" class="mr-25"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                    @endforelse
                                @else
                                    <div class="row container-item" data-index="0">
                                        <input type="hidden" name="prices[0][price_id]" value="">
                                        <div class="col-lg-2 col-12">
                                            <label>Services</label>
                                            <select name="prices[0][price_services][]" class="select2 form-control">
                                                <option value="" disabled>Select Services</option>
                                                @foreach ($services as $service)
                                                    <option value="{{ $service->id }}">
                                                        {{ $service->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-2 col-12">
                                            <x-select name="prices[0][duration]" label="Duration" :options="$duration"
                                                placeholder="Select duration" />
                                        </div>

                                        <div class="col-lg-2 col-12">
                                            <x-input name="prices[0][question]" label="Questions" value=""
                                                placeholder="Enter questions" :required="false" />
                                        </div>

                                        <div class="col-lg-2 col-12">
                                            <x-input name="prices[0][charge]" label="Charge" value=""
                                                placeholder="Enter charge" type="number" />
                                        </div>

                                        <div class="col-lg-2 col-12">
                                            <x-input name="prices[0][charge_dollar]" label="Charge in Dollar" value=""
                                                placeholder="Enter charge in dollar" type="number" />
                                        </div>

                                        <div class="col-md-2 col-12 d-flex align-items-center">
                                            <button type="button"
                                                class="btn btn-outline-danger delete-item text-nowrap px-1">
                                                <i data-feather="x" class="mr-25"></i>
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- <x-repeater name="service_charges" :maxLimit="3" :fields="[
                                        [
                                            'col' => '3',
                                            'type' => 'select',
                                            'name' => 'services',
                                            // 'multiple' => true,
                                            'options' => $services,
                                        ],
                                        [
                                            'col' => '3',
                                            'type' => 'select',
                                            'name' => 'duration',
                                            'options' => $duration,
                                        ],
                                        [
                                            'col' => '3',
                                            'type' => 'number',
                                            'name' => 'question',
                                            'required' => false,
                                            'label' => 'Question',
                                        ],
                                        [
                                            'col' => '3',
                                            'type' => 'number',
                                            'name' => 'charge',
                                            'label' => 'Charge',
                                        ],
                                    ]" /> --}}
                                @endif
                            </div>
                            <div class="col-12 text-center mt-2">
                                <button id="add-more" type="button" class="btn btn-outline-primary ">
                                    Add
                                </button>
                            </div>
                        </div>
                    </x-form>
                </x-card>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>

    <script>
        function redirect() {
            location.href = "{{ route('admin.astros.index') }}"
        }

        let lang = [];
        let skill = [];
        let service = [];
        const languages = {!! $data->known_languages !!};
        languages.forEach(e => {
            lang.push(e.id);
        });
        $('#languages').val(lang).change();

        const skills = @json($data->skills ? $data->skills->pluck('id') : []);
        
        skills.forEach(e => {
            skill.push(e);
        });       

        $('#skills').val(skill).change();
        const services = @json($data->services->pluck('id'));
        $('#services').val(services).trigger('change');
    </script>
    <script>
        $('#add-more').cloneData({
            mainContainerId: 'main-container',
            cloneContainer: 'container-item',
            removeButtonClass: 'remove-item',
            removeConfirm: true,
            removeConfirmMessage: 'Are you sure want to delete?',
            append: '',
            minLimit: 0,
            defaultRender: 1,
            afterRender: function(newElem) {
                $(newElem).find('.select2').select2();
                if (feather) feather.replace();
                $('#main-container .container-item').each(function(i) {
                    $(this).attr('data-index', i);
                    $(this).find('input, select, textarea').each(function() {
                        let name = $(this).attr('name');
                        if (name) {
                            name = name.replace(/prices\[\d+\]/, `prices[${i}]`);
                            $(this).attr('name', name);
                        }
                    });
                });
            }
        });
        $('#add-more').on('click', function() {
            const newRow = $(this).parents('#main-container').find('.container-item').last();
            newRow.find('.delete-item').attr("data-option-delete", "");
        })
        $(document).on('click', '[data-option-delete]', function() {
            const optId = $(this).data('option-delete');
            if (confirm('Are you sure want to delete ?')) {
                $(this).closest('.container-item').remove();
                if (optId) {
                    deleteItem(optId);
                }
            };
        });

        function deleteItem(id) {
            Notiflix.Loading.standard('Updating detail...');
            $.ajax({
                method: "POST",
                url: "{{ route('admin.astros.delete-price') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                },
                success: function(response) {
                    Notiflix.Loading.remove();
                    console.log(response);
                    Notiflix.Notify.success(response.message);
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 1000);
                },
                error: function(response) {
                    Notiflix.Loading.remove();
                    Notiflix.Notify.failure('Something went wrong!');
                    console.log(response);
                }
            });
        }
    </script>
@endsection
