@extends('layouts/contentLayoutMaster')

@section('title', 'WalletPackage')
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
                    <livewire:wallet-package-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add walletpackage" id="add-blade-modal">
        <x-form id="add-walletpackage" method="POST" class="" :route="route('admin.walletpackages.store')">
            <div class="col-md-12 col-12 ">
                <x-select name="type" :options="['national', 'international']" />
                <x-input type="number" name="amount" label="Amount in INR" placeholder="Amount in INR" :required="false" />
                <x-input type="number" name="bonus" label="Bonus in INR" placeholder="Bonus in INR" :required="false" />
                <x-input type="number" name="amount_dollar" label="Amount in Dollar" placeholder="Amount in Dollar"
                    :required="false" />
                <x-input type="number" name="bonus_dollar" label="Bonus in Dollar" placeholder="Bonus in Dollar"
                    :required="false" />
                <x-input name="remark" />
                <x-select name="for_new_user" :options="['yes', 'no']" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update walletpackage" id="edit-walletpackage-modal">
        <x-form id="edit-walletpackage" method="POST" class="" :route="route('admin.walletpackages.update')">
            <div class="col-md-12 col-12 ">
                <x-select name="type" id="edit_type" :options="['national', 'international']" />

                <x-input type="number" name="amount" label="Amount in INR" placeholder="Amount in INR"
                    :required="false" />
                <x-input type="number" name="bonus" label="Bonus in INR" placeholder="Bonus in INR" :required="false" />
                <x-input type="number" name="amount_dollar" label="Amount in Dollar" placeholder="Amount in Dollar"
                    :required="false" />
                <x-input type="number" name="bonus_dollar" label="Bonus in Dollar" placeholder="Bonus in Dollar"
                    :required="false" />
                <x-input name="id" type="hidden" />
                <x-input name="remark" />
                <x-select name="for_new_user" id="edit_for_new_user" :options="['yes', 'no']" />
            </div>

        </x-form>
    </x-side-modal>
    <x-modal id="remark-modal" :footer="false" />
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
            $(`${modal} #amount`).val(data.amount);
            $(`${modal} #bonus`).val(data.bonus);
            $(`${modal} #amount_dollar`).val(data.amount_dollar);
            $(`${modal} #bonus_dollar`).val(data.bonus_dollar);
            $(`${modal} #remark`).val(data.remark);
            $(`${modal} #edit_for_new_user`).val(data.for_new_user).trigger('change');
            $(`${modal} #edit_type`).val(data.type).trigger('change');
            $(modal).modal('show');
        }

        $(document).on('click', '[data-remark]', function() {
            const data = $(this).closest('tr').find('[data-hidden]').html();
            const modal = $('#remark-modal');
            $(modal).find('.modal-body').html(data);
            $(modal).find('.modal-title').text('Remark');
            $(modal).modal('show');
        });
    </script>
@endsection
