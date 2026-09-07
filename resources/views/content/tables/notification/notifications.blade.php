@extends('layouts/contentLayoutMaster')

@section('title', 'Notification')
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
                    <livewire:notification-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add notification" id="add-blade-modal">
        <x-form id="add-notification" method="POST" class="" :route="route('admin.notifications.store')">
            @csrf
            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="title" />

            </div>
            <div class="col-md-12 col-12 ">
                <x-input :required="false" type="textarea" name="description" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-select :required="false" name="type" :options="['user', 'astro']" />
            </div>

            {{-- it work for single user,astro --}}
            {{-- <div class="col-md-12 col-12 user" style="display: none;">
                <x-select :required="false" name="user_id" :options="$users" />
            </div> --}}

            {{-- new --}}
            <div class="col-md-12 col-12 user" style="display: none;">
                {{-- <x-select name="user_id" :options="array_merge(['all' => 'all'], $users->pluck('name', 'id')->toArray())" />
                     --}}
                <label for="">User</label>
                <select name="user_id" class="form-control select2" id="user_id">
                    <option value="all">All</option>
                    @forelse ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @empty
                    @endforelse
                </select>

            </div>

            {{-- <x-input :required="false" name="image" /> --}}
        </x-form>
    </x-side-modal>

    <x-side-modal title="Update notification" id="edit-notification-modal">
        <x-form id="edit-notification" method="POST" class="" :route="route('admin.notifications.update')">
            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="edit_title" />

            </div>
            <div class="col-md-12 col-12 ">
                <x-input :required="false" type="textarea" name="edit_description" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-select :required="false" name="edit_type" :options="['user', 'astro']" />
            </div>
            <div class="col-md-12 col-12 edit_user" style="display: none;">
                <x-select :required="false" name="edit_user_id" :options="$users" />
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
            $('.user').hide();
            $(document).on('change', 'select[name="type"]', function() {
                if ($(this).val() === 'user') {
                    $('.user').show();
                } else {
                    $('.user').hide();
                }
            });


            //original
            //  $(document).on('change', '#type', function() {
            //      const type = $(this).val();
            //     if (type == 'user') {
            //        $('.user').show();
            //   } else {
            //       $('.user').hide();
            //   }
            // });


            // $('.edit_user').hide();
            // $(document).on('change', '#edit_type', function() {
            //     const type = $(this).val();
            //     if (type == 'user') {
            //         $('.edit_user').show();
            //     } else {
            //         $('.edit_user').hide();
            //     }
            // });


            $(document).on('change', 'select[name="edit_type"]', function() {
                const type = $(this).val();
                if (type === 'user') {
                    $('.edit_user').show();
                } else {
                    $('.edit_user').hide();
                }
            });
        });

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #title`).val(data.title);
            $(`${modal} #image`).val(data.image);

            $(modal).modal('show');
        }
    </script>
@endsection
