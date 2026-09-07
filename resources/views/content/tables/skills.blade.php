@extends('layouts/contentLayoutMaster')

@section('title', 'Skill')
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
                    <livewire:skill-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add skill" id="add-blade-modal">
        <x-form id="add-skill" method="POST" class="" :route="route('admin.skills.store')">
            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="name" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input-file name="icon" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update skill" id="edit-skill-modal">
        <x-form id="edit-skill" method="POST" class="" :route="route('admin.skills.update')">

            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="name" />
                <x-input name="id" type="hidden" />
            </div>
            <div class="col-md-10 col-10 ">
                <x-input-file name="icon" />
            </div>
            <div class="col-md-2 col-2 mt-2">
                <div class="avatar avatar-lg">
                    <img class="view-on-click" id="image-preview" src="" alt="avatar">
                </div>
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

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #name`).val(data.name);

            $(modal).modal('show');
        }
    </script>
@endsection
