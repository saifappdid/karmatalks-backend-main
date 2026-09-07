@extends('layouts/contentLayoutMaster')

@section('title', 'Booking Details')

@section('page-style')
    <style>
        .booking-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        .status-box {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #f0fff4;
        }

        .status-box .icon {
            font-size: 26px;
            color: #38a169;
            margin-right: 12px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .profile-info img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 12px;
        }

        .summary-card {
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
        }

        .summary-card h6 {
            font-weight: 600;
            margin-bottom: 15px;
            color: #000000 !important;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .summary-item.total {
            font-weight: 600;
            font-size: 16px;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }

        .action-btn {
            display: block;
            width: 100%;
            margin-top: 15px;
            background: #2563eb;
            color: #fff;
            font-weight: 500;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
        }

        .help-box {
            padding: 12px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }

        .help-box small {
            display: block;
            margin-top: 8px;
            color: #666;
        }
    </style>
    <style>
        .time-slot {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #007bff;
            transition: 0.2s;
            margin: 5px;
        }

        .time-slot input:checked+span,
        .time-slot.active {
            background-color: #007bff;
            color: #fff;
        }

        .btn-secondary {
            background-color: #ccc !important;
            color: #666 !important;
            border: none !important;
            cursor: not-allowed;
        }
    </style>

@endsection

@section('content')
    <section>
        <div class="row">
            <!-- Left Section -->
            <div class="col-lg-8 col-md-7">
                <div class="booking-container card">

                    <!-- Status -->
                    <div class="status-box">
                        <div class="icon">
                            @if ($booking->status == 'confirmed')
                                ✅
                            @elseif ($booking->status == 'started')
                                ⏳
                            @elseif ($booking->status == 'ended')
                                🎉
                            @else
                                📌
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-1" style="color: #000000 !important;">Booking {{ ucfirst($booking->status) }}</h6>

                            @if ($booking->status == 'confirmed')
                                <small>
                                    Booking confirmed for {{ $booking->user->name ?? '-' }}
                                </small>
                            @elseif ($booking->status == 'started')
                                <small>
                                    Session with {{ $booking->user->name ?? '-' }} is in progress.
                                    Started at
                                    {{ $booking->started_at ? \Carbon\Carbon::parse($booking->started_at)->format('M d, Y h:i A') : '-' }}
                                </small>
                            @elseif ($booking->status == 'ended')
                                <small>
                                    Session with {{ $booking->user->name ?? '-' }} has ended.
                                    Ended at
                                    {{ $booking->ended_at ? \Carbon\Carbon::parse($booking->ended_at)->format('M d, Y h:i A') : '-' }}
                                </small>
                            @else
                                <small>
                                    Status update for {{ $booking->user->name ?? '-' }}
                                </small>
                            @endif
                        </div>
                    </div>



                    <!-- User & Astro -->
                    <div class="profile-info">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($booking->user->name ?? 'U') }}"
                            alt="User">
                        <div>
                            <strong>{{ $booking->user->name ?? '-' }}</strong><br>
                            <small>{{ $booking->mobile ?? ($booking->user->mobile ?? '-') }}</small>
                        </div>
                    </div>

                    <div class="profile-info">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($booking->astro->first_name ?? 'A') }}"
                            alt="Astro">
                        <div>
                            <strong>{{ $booking->astro ? $booking->astro->first_name . ' ' . $booking->astro->middle_name . ' ' . $booking->astro->last_name : '-' }}</strong><br>
                            <small>Astrologer ({{ ucfirst($booking->type) }})</small>
                        </div>
                    </div>

                    <!-- Session Info -->
                    <div class="mt-3">
                        <p>Session scheduled on <strong>{{ date('M d, Y', strtotime($booking->session_date)) }}</strong>
                        </p>
                        <p>⏰ Time: <strong>{{ date('h:i A', strtotime($booking->start_time)) }} -
                                {{ date('h:i A', strtotime($booking->end_time)) }}</strong></p>
                        <p>⌛ Duration: <strong>{{ $booking->duration ?? '-' }} min</strong></p>
                        <p>❓ Questions: <strong>{{ $booking->questions ?? '-' }}</strong></p>
                        <p>❓ Name: <strong>{{ $booking->user->name ?? '-' }}</strong></p>
                        <p>❓ Dob:
                            <strong>{{ $booking->user->dob ? \Carbon\Carbon::parse($booking->user->dob)->format('d-m-Y') : '-' }}</strong>
                        </p>
                        <p>❓ Place Leaving: <strong>{{ $booking->user?->country->name ?? '-' }}</strong></p>
                    </div>

                    @php
                        $data = json_decode($booking->booking_data, true);
                        $serviceTitle = $data['service']['title'] ?? '-';
                    @endphp

                    <div class="mt-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar bg-light-primary mr-1">
                                    📌
                                </div>
                                <div>
                                    <h6 class="mb-0 text-dark">Service</h6>
                                    <small class="text-muted">{{ $serviceTitle }}</small>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Reschedule Option -->
                    @if ($booking->status == 'confirmed')
                        <button data-toggle="modal" data-target="#rescheduleModal" class="btn btn-sm ">
                            🔁 Reschedule Booking
                        </button>

                        <div class="help-box">
                            <strong>Need Help?</strong>
                            <small>If you wish to modify the date or time, click "Reschedule Booking".</small>
                        </div>
                    @endif



                </div>
            </div>

            <!-- Right Section (Payment Summary) -->
            <div class="col-lg-4 col-md-5">
                <div class="summary-card card">
                    <h6>💰 Payment Summary</h6>
                    <div class="summary-item"><span>Subtotal:</span> <span>{{ config('app.currency') }}
                            {{ number_format($booking->sub_total, 2) }}</span></div>
                    @if ($booking->promocode)
                        <div class="summary-item"><span>Promocode ({{ $booking->promocode }}):</span>
                            <span>-{{ config('app.currency') }}
                                {{ number_format($booking->promocode_discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="summary-item total"><span>Final Total:</span>
                        <span>{{ config('app.currency') }} {{ number_format($booking->final_total, 2) }}</span>
                    </div>

                </div>
                <div class="summary-card card">
                    <h6> Reviews Summary</h6>
                    @if (count($booking->review) >= 1)
                        @foreach ($booking->review as $item)
                            <!-- User Reviews -->
                            <div class="review-section">
                                <h6 class="mt-2">👤 Reviews</h6>
                                <div class="summary-item">
                                    <span>{{ $item->review ?? '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span>no reviews avilable</span>
                    @endif


                </div>

            </div>
        </div>
    </section>
    @php
        $data = json_decode($booking->booking_data, true);
        $priceId = $data['data']['price_id'] ?? null;
        // dd($priceId);
    @endphp

    <x-side-modal id="rescheduleModal" title="Reschedule Booking" modalWidth="w-50">
        <x-form method="POST" id="rescheduleForm" :route="route('admin.bookings.reschedule')">
            {{-- <div class="mb-3"> --}}
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

            {{-- </div> --}}
            <div class="col-md-12">
                <x-input name="new_date" type="text" label="New Date" />
            </div>

            <div class="col-md-12 get-times">
                <!-- Available time slots will be loaded here via AJAX -->
            </div>

        </x-form>
    </x-side-modal>
@endsection
@section('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            // Initialize datepicker for the new_date input
            $('#new_date').flatpickr({
                format: 'yyyy-mm-dd',
                startDate: new Date(), // Disable past dates
                autoclose: true
            });
            // Fetch available time slots when a new date is selected
            $('#new_date').on('change', function() {
                var selectedDate = $(this).val();
                var astroId = '{{ $booking->astro_id }}';
                var bookingId = '{{ $booking->id }}';
                var priceId = '{{ $priceId }}';

                if (selectedDate) {
                    $.ajax({
                        url: '{{ route('admin.bookings.getAvailableTimes') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            date: selectedDate,
                            astro_id: astroId,
                            booking_id: bookingId,
                            price_id: priceId
                        },
                        success: function(response) {
                            console.log(response);

                            if (response.open_hours && response.open_hours.length > 0) {
                                let html = `
        <div class="mb-3">
            <label class="form-label">Available Time Slots</label>
            <div class="d-flex flex-wrap gap-2">
    `;

                                response.open_hours.forEach(function(slot) {
                                    let start = slot.start_time.substring(0, 5);
                                    let end = slot.end_time.substring(0, 5);
                                    let disabled = slot.booked ?
                                        'disabled btn-secondary' :
                                        'btn-outline-primary';

                                    html += `
            <label class="time-slot btn ${disabled}" data-start="${slot.start_time}" data-end="${slot.end_time}">
                ${start} - ${end}
            </label>
            <input type="hidden" name="start_time" value="${slot.start_time}" />
            <input type="hidden" name="end_time" value="${slot.end_time}" />
        `;
                                });

                                html += '</div></div>';
                                $('.get-times').html(html);
                            } else {
                                $('.get-times').html(
                                    '<div class="alert alert-warning">No available time slots for this date.</div>'
                                );
                            }
                        },
                        error: function() {
                            $('.get-times').html(
                                '<div class="alert alert-danger">Failed to load available times. Please try again.</div>'
                            );
                        }
                    });
                } else {
                    $('.get-times').empty();
                }
            });

            $(document).on('click', '.time-slot', function() {
                if ($(this).hasClass('btn-secondary')) return; // skip booked ones

                $('.time-slot').removeClass('btn-primary').addClass('btn-outline-primary');
                $(this).removeClass('btn-outline-primary').addClass('btn-primary');

                // Store selected slot times in hidden inputs
                const start = $(this).data('start');
                const end = $(this).data('end');

                $('input[name="start_time"]').val(start);
                $('input[name="end_time"]').val(end);
            });

            // // Handle form submission for rescheduling
            // $('#rescheduleForm').on('submit', function(e) {
            //     e.preventDefault();

            //     var formData = $(this).serialize();

            //     $.ajax({
            //         url: $(this).attr('action'),
            //         method: 'POST',
            //         data: formData,
            //         success: function(response) {
            //             if (response.success) {
            //                 alert('Booking rescheduled successfully!');
            //                 location.reload();
            //             } else {
            //                 alert('Error: ' + response.message);
            //             }
            //         },
            //         error: function() {
            //             alert('An error occurred while rescheduling. Please try again.');
            //         }
            //     });
            // });
        });
    </script>
