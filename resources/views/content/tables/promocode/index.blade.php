@extends('layouts/contentLayoutMaster')

@section('title', 'Promocode')
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
                    <livewire:promocode-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add promocode" id="add-blade-modal">
        <x-form id="add-promocode" method="POST" class="" :route="route('admin.promocodes.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="promocode" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="message" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="start_date" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="end_date" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="minimum_order_amount" type="number" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="max_discount_amount" type="number" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="discount" type="number" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-select name="discount_type" :options="['percentage', 'flat']" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update promocode" id="edit-promocode-modal">
        <x-form id="edit-promocode" method="POST" class="" :route="route('admin.promocodes.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="promocode" />
                <x-input name="id" type="hidden" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="message" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="start_date" id="edit_start_date" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="end_date" id="edit_end_date" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="minimum_order_amount" type="number" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="max_discount_amount" type="number" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="discount" type="number" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-select name="discount_type" id="edit_discount_type" :options="['percentage', 'flat']" />
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

            $('#start_date,#end_date,#edit_start_date,#edit_end_date').flatpickr({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                minDate: 'today',
            });

        });

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #promocode`).val(data.code);
            $(`${modal} #message`).val(data.message);
            $(`${modal} #edit_start_date`).val(data.start);
            $(`${modal} #edit_end_date`).val(data.end);
            $(`${modal} #use_limit`).val(data.use_limit);
            $(`${modal} #minimum_order_amount`).val(data.minimum_order_amount);
            $(`${modal} #max_discount_amount`).val(data.max_discount_amount);
            $(`${modal} #discount`).val(data.discount);
            $(`${modal} #edit_discount_type`).val(data.discount_type).trigger('change');
            $(modal).modal('show');
        }
    </script>
@endsection
