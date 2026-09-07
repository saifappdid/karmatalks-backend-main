@extends('layouts/contentLayoutMaster')

@section('title', 'Astro Daily Time Report')

@section('content')
    <section class="section">
        <x-card>
            <div class="col-lg-3 col-3 px-0">
                <x-input name="date" />
            </div>
            <livewire:astro-time-report-table />
        </x-card>
    </section>
    @php
        $startDate = request()->start_date ?? \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $todayDate = request()->end_date ?? \Carbon\Carbon::now()->toDateString();
        $route = route('admin.astro-time-report.index');
    @endphp
@endsection
@section('page-script')
    <script>
        const startOfMonth = "{{ $startDate }}";
        const today = "{{ $todayDate }}";

        $('#date').flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'M j, Y',
            maxDate: "today",
            mode: "range",
            defaultDate: [startOfMonth, today],
            onClose: applyDateFilter

        });

        function applyDateFilter(selectedDates, dateStr) {
            const start_date = dateStr.split(" to ")[0];
            let end_date = dateStr.split(" to ")[1];
                
            if (!end_date) {
                end_date = start_date;
            }

            window.location = "{{ $route }}?start_date=" + start_date + '&end_date=' + end_date;
        }
    </script>
@endsection
