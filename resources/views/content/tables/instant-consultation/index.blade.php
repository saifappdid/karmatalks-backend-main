@extends('layouts/contentLayoutMaster')

@section('title', 'InstantConsultationHistory')
@section('page-style')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .dropdown-menu {
            transform: scale(1) !important;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <div class="col-lg-6 col-6">
                        <x-input name="date" />
                    </div>
                    <livewire:instant-consultation-history-table />
                </x-card>
            </div>
        </div>
    </section>

    {{-- 
    <x-side-modal title="Add instantconsultationhistory" id="add-blade-modal">
        <x-form id="add-instantconsultationhistory" method="POST" class="" :route="route('admin.instant-consultation.store')">
            <div class="col-md-12 col-12 "><x-input :required="false" name="name" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update instantconsultationhistory" id="edit-instantconsultationhistory-modal">
        <x-form id="edit-instantconsultationhistory" method="POST" class="" :route="route('admin.instant-consultation.update')">

            <div class="col-md-12 col-12 "><x-input :required="false" name="name" />
                <x-input name="id" type="hidden" />
            </div>

        </x-form>
    </x-side-modal> --}}
    @php
        $startDate = request()->start_date ?? \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $todayDate = request()->end_date ?? \Carbon\Carbon::now()->toDateString();
        $route = route('admin.instant-consultation.index');

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
            onClose: function(selectedDates, dateStr, instance) {
                applyDateFilter(dateStr);
            }
        });

        function applyDateFilter(dateStr, employee = 'all') {
            const start_date = dateStr.split(" to ")[0];
            let end_date = dateStr.split(" to ")[1];            
            if (!end_date) {
                end_date = start_date;
            }
            window.location =
                "{{ $route }}?start_date=" +
                start_date + '&end_date=' + end_date + '&employee=' + employee;
        }
    </script>
@endsection
