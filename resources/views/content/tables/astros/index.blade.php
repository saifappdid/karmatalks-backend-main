@extends('layouts/contentLayoutMaster')

@section('title', 'Astro')
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
                    <livewire:astro-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add astro" id="add-blade-modal">
        <x-form id="add-astro" method="POST" class="" :route="route('admin.astros.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update astro" id="edit-astro-modal">
        <x-form id="edit-astro" method="POST" class="" :route="route('admin.astros.update')">

            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-input name="id" type="hidden" />
            </div>

        </x-form>
    </x-side-modal>
    <x-modal id="manageTimeLine" title="Manage Consultation Availability" :footer="false">
        <x-form id="update-timeline" method="POST" class="" :route="route('admin.astros.update-timeline')">
            <x-input name="id" type="hidden" />
            <div class="timeLineContent"></div>
        </x-form>
    </x-modal>
    <x-modal id="managePrice" size="xl" title="Manage Consultation Pricing" :footer="false">
        <x-form id="update-price" method="POST" class="" :route="route('admin.astros.update-pricing')">
            <x-input name="id" type="hidden" />
            <div class="pricingContent"></div>
        </x-form>
    </x-modal>
    <x-modal id="manageInstantPrice" size="lg" title="Manage Consultation Instant Pricing" :footer="false">
        <x-form id="update-instant-price" method="POST" class="" :route="route('admin.astros.update-instant-pricing')">
            <x-input name="id" type="hidden" />
            <div class="col-lg-3 col-3">
                <x-input name="voice_call_charge" label="Voice Call Charge" />
            </div>
            <div class="col-lg-3 col-3">
                <x-input name="video_call_charge" label="Video Call Charge" />
            </div>
            <div class="col-lg-3 col-3">
                <x-input name="chat_charge" label="Chat Charge" />
            </div>
        </x-form>
    </x-modal>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                window.location.href = "{{ route('admin.astros.create') }}"
            });
        });
        $(document).on('click', '.manageTimeLine', function() {
            const modal = $('#manageTimeLine');
            const id = $(this).data('id');
            $(modal).find('#id').val(id);
            if (id) {
                $.ajax({
                    type: "post",
                    url: "{{ route('admin.astros.get-timeline') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(response) {
                        $('.timeLineContent').html(response);
                        $(modal).modal('show');
                    }
                });
            }
        });
        $(document).on('click', '.managePrice', function() {
            $('.pricingContent').html('');

            const modal = $('#managePrice');
            const id = $(this).data('id');
            $(modal).find('#id').val(id);
            if (id) {
                $.ajax({
                    type: "post",
                    url: "{{ route('admin.astros.get-price') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(response) {
                        $('.pricingContent').html(response);
                        $(modal).modal('show');
                    }
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {

            window.livewire.on('mailSent', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: 'The export file has been sent successfully.',
                    confirmButtonText: 'OK'
                });
                location.reload();
            });

        });

        $(document).on('click', '.manageInstantPrice', function() {
            const modal = $('#manageInstantPrice');
            const id = $(this).data('id');
            const chat = $(this).data('chat');
            const voice = $(this).data('voice');
            const video = $(this).data('video');

            $(modal).find('#id').val(id);
            $(modal).find('[name="voice_call_charge"]').val(voice);
            $(modal).find('[name="video_call_charge"]').val(video);
            $(modal).find('[name="chat_charge"]').val(chat);
            $(modal).modal('show');
        });
    </script>
@endsection
