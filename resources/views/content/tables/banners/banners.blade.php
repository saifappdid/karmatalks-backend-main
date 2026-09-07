@extends('layouts/contentLayoutMaster')

@section('title', 'Banner')
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
                    <livewire:banner-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add banner" id="add-blade-modal">
        <x-form id="add-banner" method="POST" class="" :route="route('admin.banners.store')">
            <div class="col-md-12 col-12 ">
                <x-select name="type" :options="['global', 'service', 'wallet', 'product']" />
            </div>
            <div class="col-md-12 col-12 service-div">
                <x-select name="service" :required="false" :options="$services" />
            </div>
            <div class="col-md-12 col-12 product-div">
                <x-select name="product" :required="false" :options="$products" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input-file name="image" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input :required="false" name="title" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input type="textarea" :required="false" name="description" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update banner" id="edit-banner-modal">
        <x-form id="edit-banner" method="POST" class="" :route="route('admin.banners.update')">
            <div class="col-md-12 col-12 ">
                <x-select name="type" id="edit_type" :options="['global', 'service', 'wallet', 'product']" />
            </div>
            <div class="col-md-12 col-12 service-div">
                <x-select name="service" id="edit_service" :required="false" :options="$services" />
            </div>
            <div class="col-md-12 col-12 product-div">
                <x-select name="product" id="edit_product" :required="false" :options="$products" />
            </div>
            <div class="col-md-10 col-10 ">
                <x-input-file name="image" />
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
                <x-input type="textarea" :required="false" name="description" />
            </div>
            <x-input name="id" type="hidden" />


        </x-form>
    </x-side-modal>
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            $(document).on('click', '[data-show]', function () {
                const modal = $(this).data('show');
                $(`#${modal}`).modal('show');
                $('.service-div').hide();
                $('.product-div').hide();
            });
        });

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #image-preview`).attr('src', '/' + data.image);
            $(`${modal} #title`).val(data.title);
            $(`${modal} #description`).val(data.description);
            setTimeout(() => {
                $(`${modal} #edit_type`).val(data.type).trigger('change');
            }, 300);
            $(`${modal} #edit_service`).val(data.service_id).trigger('change');
            $(`${modal} #edit_product`).val(data.product_id).trigger('change');
            $(modal).modal('show');
        }
        $(document).on('change', '#type', function () {
            if ($(this).val() == 'service') {
                $('.service-div').show();
            } else {
                $('.service-div').hide();
                $('#edit_service').val('');
            }
        });
        $(document).on('change', '#edit_type', function () {
            if ($(this).val() == 'service') {
                $('.service-div').show();
            } else {
                $('.service-div').hide();
                $('#service').val('');
            }
        });

        $(document).on('change', '#type', function () {
            if ($(this).val() == 'product') {
                $('.product-div').show();
            } else {
                $('.product-div').hide();
                $('#edit_product').val('');
            }
        });
        $(document).on('change', '#edit_type', function () {
            if ($(this).val() == 'product') {
                $('.product-div').show();
            } else {
                $('.product-div').hide();
                $('#product').val('');
            }
        });
    </script>
@endsection