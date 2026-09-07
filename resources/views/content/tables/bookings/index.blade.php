@extends('layouts/contentLayoutMaster')

@section('title', 'Bookings')
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
                    <livewire:booking-table />
                </x-card>
            </div>
        </div>
    </section>
@endsection
@section('page-script')

    <script></script>
@endsection
