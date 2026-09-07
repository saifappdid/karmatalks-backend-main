@extends('layouts/contentLayoutMaster')

@section('title', 'AstroBusinessSetting')
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
                    <x-form id="add-astrobusinesssetting" method="POST" class=""
                        :route="route('admin.astro-business-settings.store')">
                        <x-divider text="Android App Setting" />
                        <div class="col-md-3">
                            <x-input value="{{ $businessSettings['full_app_version_android_for_astro'] ?? '' }}"
                                name="full_app_version_android_for_astro" label="Full Astro App Version Android" />
                        </div>
                        <div class="col-md-3">
                            <x-input value="{{ $businessSettings['upcoming_build_number_astro_android'] ?? '' }}"
                                name="upcoming_build_number_astro_android" label="Upcoming Build Number Astro Android" />
                        </div>
                        <div class="col-md-3 col-6 ">
                            <x-input name="app_version" value="{{ $businessSettings['app_version'] ?? '' }}" />
                        </div>
                        <div class="col-md-3 col-6 ">
                            <x-input name="app_download" value="{{ $businessSettings['app_download'] ?? '' }}" />
                        </div>
                        <div class="col-lg-3 col-6">
                            <x-input type="hidden" :required="false" name="force_update" id="off" value="off" />
                            <x-custom-switch name="force_update" id="force_update" label="Force Update" />
                        </div>
                        <div class="col-lg-3 col-6">
                            <x-input type="hidden" :required="false" name="maintainance_mode" id="off" value="off" />
                            <x-custom-switch name="maintainance_mode" id="maintainance_mode" label="Maintainance Update" />
                        </div>

                        <x-divider text="IOS App Setting" />
                        <div class="col-md-3">
                            <x-input :required="false" value="{{ $businessSettings['full_app_version_ios_for_astro'] ?? '' }}"
                                name="full_app_version_ios_for_astro" label="Full Astro App Version IOS" />
                        </div>
                        <div class="col-md-3">
                            <x-input value="{{ $businessSettings['upcoming_build_number_astro_ios'] ?? '' }}"
                                name="upcoming_build_number_astro_ios" label="Upcoming Build Number Astro IOS" />
                        </div>
                        <div class="col-md-3 col-6 ">
                            <x-input name="ios_app_version" value="{{ $businessSettings['ios_app_version'] ?? '' }}" />
                        </div>
                        <div class="col-md-3 col-6 ">
                            <x-input name="ios_app_download" value="{{ $businessSettings['ios_app_download'] ?? '' }}" />
                        </div>
                        <div class="col-lg-3 col-6">
                            <x-input type="hidden" name="ios_force_update" id="off" value="off" />
                            <x-custom-switch name="ios_force_update" id="ios_force_update" label="Force Update" />
                        </div>
                        <div class="col-lg-3 col-6">
                            <x-input type="hidden" name="ios_maintainance_mode" id="off" value="off" />
                            <x-custom-switch name="ios_maintainance_mode" id="ios_maintainance_mode"
                                label="Maintainance Update" />
                        </div>
                        <x-divider text="Contact Setting" />
                        <div class="col-md-6 col-6 ">
                            <x-input name="email" value="{{ $businessSettings['email'] ?? '' }}" />
                        </div>
                        <div class="col-md-6 col-6 ">
                            <x-input name="mobile" value="{{ $businessSettings['mobile'] ?? '' }}" />
                        </div>
                        <x-divider text="Terms & Policy" />
                        <div class="col-md-6 col-6 ">
                            <x-input name="privacy_policy" value="{{ $businessSettings['privacy_policy'] ?? '' }}" />
                        </div>
                        <div class="col-md-6 col-6 ">
                            <x-input name="terms_condition" value="{{ $businessSettings['terms_condition'] ?? '' }}" />
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
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        const data = @json($businessSettings);

        fullEditor_about_us.root.innerHTML = data.about_us;
        fullEditor_contact_us.root.innerHTML = data.contact_us;

        if (data.force_update == 'on') {
            $('#force_update').prop('checked', true);
        }
        if (data.maintainance_mode == 'on') {
            $('#maintainance_mode').prop('checked', true);
        }
        if (data.ios_force_update == 'on') {
            $('#ios_force_update').prop('checked', true);
        }
        if (data.ios_maintainance_mode == 'on') {
            $('#ios_maintainance_mode').prop('checked', true);
        }
    </script>
@endsection