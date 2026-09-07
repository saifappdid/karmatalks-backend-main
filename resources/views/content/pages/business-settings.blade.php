@extends('layouts/contentLayoutMaster')

@section('title', 'Business Settings')
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
    <x-card>
        <x-form id="add-ground" method="POST" :reset="0" class="" :route="route('admin.business-settings.store')">
            <x-divider text="Android App Setting" />
            <div class="col-md-3">
                <x-input value="{{ $data['full_app_version_android_for_user'] ?? '' }}"
                    name="full_app_version_android_for_user" label="Full App Version Android" />
            </div>
            <div class="col-md-3">
                <x-input value="{{ $data['upcoming_build_number_android'] ?? '' }}" name="upcoming_build_number_android"
                    label="Upcoming Build Number Android" />
            </div>
            <div class="col-md-3 col-6 ">
                <x-input name="app_version" value="{{ $data['app_version'] ?? '' }}" />
            </div>
            <div class="col-md-3 col-6 ">
                <x-input name="app_download" value="{{ $data['app_download'] ?? '' }}" />
            </div>
            <div class="col-lg-3 col-6">
                <x-input type="hidden" :required="false" name="force_update_android" id="off" value="off" />
                <x-custom-switch name="force_update_android" id="force_update_android" label="Force Update" />
            </div>
            <div class="col-lg-3 col-6">
                <x-input type="hidden" :required="false" name="maintainance_mode_android" id="off" value="off" />
                <x-custom-switch name="maintainance_mode_android" id="maintainance_mode_android"
                    label="Maintainance Update" />
            </div>

            <x-divider text="IOS App Setting" />
            <div class="col-md-3">
                <x-input value="{{ $data['full_app_version_ios_for_user'] ?? '' }}" name="full_app_version_ios_for_user"
                    label="Full App Version IOS" />
            </div>
            <div class="col-md-3">
                <x-input value="{{ $data['upcoming_build_number_ios'] ?? '' }}" name="upcoming_build_number_ios"
                    label="Upcoming Build Number IOS" />
            </div>
            <div class="col-md-3 col-6 ">
                <x-input name="ios_app_version" value="{{ $data['ios_app_version'] ?? '' }}" />
            </div>
            <div class="col-md-3 col-6 ">
                <x-input name="ios_app_download" value="{{ $data['ios_app_download'] ?? '' }}" />
            </div>
            <div class="col-lg-3 col-6">
                <x-input type="hidden" name="force_update_ios" id="off" value="off" />
                <x-custom-switch name="force_update_ios" id="force_update_ios" label="Force Update" />
            </div>
            <div class="col-lg-3 col-6">
                <x-input type="hidden" name="maintainance_mode_ios" id="off" value="off" />
                <x-custom-switch name="maintainance_mode_ios" id="maintainance_mode_ios" label="Maintainance Update" />
            </div>

            <x-divider text="Contact Setting" />
            <div class="col-md-4 col-4 ">
                <x-input name="email" value="{{ $data['email'] ?? '' }}" />
            </div>
            <div class="col-md-4 col-4 ">
                <x-input name="mobile" value="{{ $data['mobile'] ?? '' }}" />
            </div>
            <div class="col-md-4 col-4 ">
                <x-input name="whatsapp_number" value="{{ $data['whatsapp_number'] ?? '' }}" />
            </div>
            <x-divider text="Terms & Policy" />
            <div class="col-md-6 col-6 ">
                <x-input name="privacy_policy" value="{{ $data['privacy_policy'] ?? '' }}" />
            </div>
            <div class="col-md-6 col-6 ">
                <x-input name="terms_condition" value="{{ $data['terms_condition'] ?? '' }}" />
            </div>
            <x-divider text="Contact Us" />
            <div class="col-md-12 col-12 ">
                <x-editor name="contact_us" id="contact_us" />
            </div>
            <x-divider text="About Us" />
            <div class="col-md-12 col-12 ">
                <x-editor name="about_us" id="about_us" />
            </div>
        </x-form>
    </x-card>
@endsection
@section('page-script')
    <script>
        $(function () {
            const data = @json($data);
            fullEditor_about_us.root.innerHTML = data.about_us;
            fullEditor_contact_us.root.innerHTML = data.contact_us;
            if (data.force_update_android == 'on') {
                $('#force_update_android').prop('checked', true);
            }
            if (data.maintainance_mode_android == 'on') {
                $('#maintainance_mode_android').prop('checked', true);
            }
            if (data.force_update_ios == 'on') {
                $('#force_update_ios').prop('checked', true);
            }
            if (data.maintainance_mode_ios == 'on') {
                $('#maintainance_mode_ios').prop('checked', true);
            }
        })
    </script>
@endsection