@extends('layouts/contentLayoutMaster')

@section('title', 'Service')
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
                    <div class="col-lg-12 col-12 text-center">
                        <a href="{{ route('admin.services.rank') }}" class="btn btn-primary btn-sm">Rank Services</a>
                    </div>
                    <livewire:service-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add service" id="add-blade-modal">
        <x-form id="add-service" method="POST" class="" :route="route('admin.services.store')">
            <div class="col-md-12 col-12 ">
                <x-input-file :required="false" name="image" />
                <x-input :required="false" name="title" />
                <x-input :required="false" name="description" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update service" id="edit-service-modal">
        <x-form id="edit-service" method="POST" class="" :route="route('admin.services.update')">

            <div class="col-md-10 col-12 ">
                <x-input-file :required="false" name="image" />
            </div>
            <div class="col-md-2 col-2 mt-2">
                <div class="avatar avatar-lg">
                    <img class="view-on-click" id="image-preview" src="" alt="avatar">
                </div>
            </div>
            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="title" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="description" />
            </div>
            <x-input name="id" type="hidden" />


        </x-form>
    </x-side-modal>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                $(`#${modal}`).modal('show');
            });
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


        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #image-preview`).attr('src', '/' + data.image);
            $(`${modal} #title`).val(data.title);
            $(`${modal} #description`).val(data.description);

            $(modal).modal('show');
        }
    </script>
@endsection
