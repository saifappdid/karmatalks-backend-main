@extends('layouts/contentLayoutMaster')

@section('title', 'Details Astro')
@section('page-style')
    {{-- <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 5px solid #007bff;
        }

        .badge-skill,
        .lang-badge {
            margin: 3px;
            font-size: 0.9rem;
        }

        .charge-box {
            background: #e9f5ff;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
        }

        .working-days {
            display: flex;
            flex-wrap: wrap;
            justify-content: start;
            gap: 8px;
        }

        .working-day {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 0.9rem;
            text-transform: capitalize;
        }

        .section-title {
            color: #007bff;
            font-weight: 600;
        }
    </style> --}}

    <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 30px 15px;
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .skills img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .working-hours {
            background-color: #f1f1f1;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <div class="profile-card bg-white shadow-lg rounded">
                        {{-- HEADER --}}
                        <div class="profile-header bg-primary text-white text-center py-4">
                            <img src="{{ $data->profile ?? asset('images/default-user.png') }}" alt="{{ $data->first_name }}"
                                class="rounded-circle border border-white mb-3" width="120" height="120"
                                style="object-fit: cover;">
                            <h4 class="mb-0">{{ $data->first_name }} {{ $data->middle_name }} {{ $data->last_name }}</h4>
                            <p class="mb-1">Gender: {{ ucfirst($data->gender) }}</p>
                            <span class="badge badge-dark">Experience: {{ $data->experience }} Years</span>
                        </div>

                        <div class="p-4">
                            {{-- Contact Details --}}
                            <h5 class="text-primary">Contact Details</h5>
                            <p><strong>Email:</strong> {{ $data->email }}</p>
                            <p><strong>Mobile:</strong> {{ $data->mobile }}</p>

                            {{-- Known Languages --}}
                            @php
                                $languages = json_decode($data->known_languages, true);
                            @endphp
                            @if (!empty($languages))
                                <h5 class="text-primary mt-4">Known Languages</h5>
                                @foreach ($languages as $lang)
                                    <span class="badge badge-primary">{{ $lang['name'] }}</span>
                                @endforeach
                            @endif

                            {{-- Skills --}}
                            @php
                                $skills = json_decode($data->skills, true);
                            @endphp
                            @if (!empty($skills))
                                <h5 class="text-primary mt-4">Skills</h5>
                                <div class="skills d-flex flex-wrap">
                                    @foreach ($skills as $skill)
                                        <div class="mr-3 d-flex align-items-center mb-2">
                                            <span>{{ $skill['name'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Charges --}}
                            <h5 class="text-primary mt-4">Consultation Charges</h5>
                            <ul class="list-unstyled">
                                <li>Chat:
                                    <strong>
                                        @if ($data->instant_chat_charge)
                                            ₹{{ $data->instant_chat_charge }}/min
                                        @else
                                            Not Available
                                        @endif
                                    </strong>
                                </li>
                                <li>Voice Call: <strong>₹{{ $data->instant_voice_call_charge }}/min</strong></li>
                                <li>Video Call: <strong>₹{{ $data->instant_video_call_charge }}/min</strong></li>
                            </ul>

                            {{-- Working Schedule --}}
                            @if (!empty($data->workingDays))
                                <h5 class="text-primary mt-4">Working Schedule</h5>
                                <div class="row">
                                    @foreach ($data->workingDays as $day)
                                        <div class="col-md-6">
                                            <div class="working-hours bg-light p-2 rounded mb-2">
                                                <strong>{{ ucfirst($day['day']) }}:</strong>
                                                @foreach ($day['workingHours'] as $time)
                                                    {{ \Carbon\Carbon::parse($time['start_time'])->format('g:i A') }} -
                                                    {{ \Carbon\Carbon::parse($time['end_time'])->format('g:i A') }}
                                                @endforeach
                                                @if ($day->status == 'blocked')
                                                    <span class="badge badge-primary mx-1">Off</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Status --}}
                            <h5 class="text-primary mt-4">Status</h5>
                            <p>
                                <strong>Online:</strong>
                                @if ($data->is_online == '1')
                                    <span class="text-success">Yes</span>
                                @else
                                    <span class="text-danger">No</span>
                                @endif
                                <br>
                                <strong>Last Active:</strong> {{ $data->last_active }}
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>

@endsection
