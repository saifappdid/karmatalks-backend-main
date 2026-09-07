@php
    $astroDays = collect($data)->keyBy('day');
@endphp
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
            @php
                $dayData = $astroDays[$day] ?? null;
                $workingHour = $dayData['workingHours'][0] ?? null;
            @endphp
            <tr>
                <td>
                    {{ ucfirst($day) }}
                </td>
                <td>
                    <x-input type="hidden" :required="false" name="days[{{ $day }}][{{ $day }}_status]"
                        value="off" />
                    <x-custom-switch name="days[{{ $day }}][{{ $day }}_status]"
                        id="{{ $day }}_status" label=" " :checked="isset($dayData) && $dayData['status'] === 'active'" />
                </td>
                <td>
                    <x-input name="days[{{ $day }}][{{ $day }}_start_time]"
                        id="{{ $day }}_start_time" label="Start time" placeholder="Enter start time"
                        :value="$workingHour['start_time'] ?? ''" />
                </td>
                <td>
                    <x-input name="days[{{ $day }}][{{ $day }}_end_time]"
                        id="{{ $day }}_end_time" label="End time" placeholder="Enter end time"
                        :value="$workingHour['end_time'] ?? ''" />
                </td>
            </tr>
        @endforeach
    </tbody>

</table>
<script>
    $("#sunday_start_time,#sunday_end_time,#monday_start_time,#monday_end_time,#tuesday_start_time,#tuesday_end_time,#wednesday_start_time,#wednesday_end_time,#thursday_start_time,#thursday_end_time,#friday_start_time,#friday_end_time,#saturday_start_time,#saturday_end_time")
        .flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: true,
            minuteIncrement: 60
        });
</script>
