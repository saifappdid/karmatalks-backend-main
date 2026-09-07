@extends('layouts/contentLayoutMaster')

@section('title', 'Category')
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
                    <livewire:category-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add category" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.categorys.store')">
            <div class="col-md-12 col-12 ">
                <x-select name="type" :options="['product', 'service', 'healing']" />
                <x-input name="title" />
                <x-input-file name="image" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update category" id="edit-category-modal">
        <x-form id="edit-category" method="POST" class="" :route="route('admin.categorys.update')">
            <div class="col-md-12">
                <x-select name="type" id="edit_type" :options="['product', 'service', 'healing']" />
                <x-input name="id" type="hidden" />
            </div>
            <div class="col-md-12">
                <x-input name="title" />
            </div>
            <div class="col-md-10 col-10 ">
                <x-input-file name="image" />
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
            $(`${modal} #edit_type`).val(data.type).trigger('change');
            $(`${modal} #title`).val(data.title);
            $(`${modal} #image-preview`).attr('src', data.image);

            $(modal).modal('show');
        }
    </script>
@endsection
