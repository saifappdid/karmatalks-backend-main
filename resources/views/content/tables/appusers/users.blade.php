@extends('layouts/contentLayoutMaster')

@section('title', 'Users')
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
                    <livewire:user-table />
                </x-card>
            </div>
        </div>
    </section>
    <x-side-modal title="Add Employee" id="add-blade-modal">
        <x-form id="add-users" method="POST" class="" :route="route('admin.users.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-input name="phone" type="number" />
                <x-input type="email" name="email" />
            </div>
        </x-form>
    </x-side-modal>

    <x-side-modal title="Update Employee" id="edit-user-modal">
        <x-form id="edit-users" method="POST" class="" :route="route('admin.users.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-input name="phone" type="number" />
                <x-input type="email" name="email" />
                <x-input name="id" type="hidden" />
            </div>
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


        document.addEventListener('livewire:load', function() {
            window.livewire.on('mailSent', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: 'The export file has been sent successfully.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            });
        });



        function setValue(data, modal) {
            console.log(data);
            $(modal + ' #id').val(data.id);
            $(modal + ' #name').val(data.name);
            $(modal + ' #phone').val(data.phone);
            $(modal + ' #email').val(data.email);
            $(modal).modal('show');
        }
    </script>
@endsection
