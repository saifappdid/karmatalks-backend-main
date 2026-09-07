<link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}" />
<link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}" />
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link href="
https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css
" rel="stylesheet">

<style>
    .material-symbols-outlined {
        font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 48
    }
</style>
@livewireStyles()
@if (!empty($pageConfigs['has_table']))
    @if ($pageConfigs['has_table'])
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}" />
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}" />
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap4.min.css')) }}" />
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')) }}" />
    @endif
@endif
@if (!empty($pageConfigs['has_editor']))
    @if ($pageConfigs['has_editor'])
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
    @endif
@endif
@if (!empty($pageConfigs['has_icon']))
    @if ($pageConfigs['has_icon'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    @endif
@endif
<link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@if (!empty($pageConfigs['has_animation']))
    @if ($pageConfigs['has_animation'])
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    @endif
@endif
@if (!empty($pageConfigs['has_player']))
    @if ($pageConfigs['has_player'])
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/plyr.min.css')) }}">
    @endif
@endif

@if (!empty($pageConfigs['has_chart']))
    @if ($pageConfigs['has_chart'])
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
    @endif
@endif
<link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">


{{-- Vendor Styles --}}
@yield('vendor-style')
{{-- Theme Styles --}}

<link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}" />

{{-- {!! Helper::applClasses() !!} --}}
@php $configData = Helper::applClasses(); @endphp

{{-- Page Styles --}}
@if ($configData['mainLayoutType'] === 'horizontal')
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/menu/menu-types/horizontal-menu.css')) }}" />
@endif
<link rel="stylesheet" href="{{ asset(mix('css/base/core/menu/menu-types/vertical-menu.css')) }}" />
<!-- <link rel="stylesheet" href="{{ asset(mix('css/base/core/colors/palette-gradient.css')) }}"> -->

{{-- Page Styles --}}
@yield('page-style')

@if (!empty($pageConfigs['has_editor']))
    @if ($pageConfigs['has_editor'])
        <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
    @endif
@endif

<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">

@if (!empty($pageConfigs['has_player']))
    @if ($pageConfigs['has_player'])
        <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-media-player.css')) }}">
    @endif
@endif

@if (!empty($pageConfigs['has_chart']))
    @if ($pageConfigs['has_chart'])
        <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
    @endif
@endif
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">




{{-- Custom RTL Styles --}}

@if ($configData['direction'] === 'rtl' && isset($configData['direction']))
    <link rel="stylesheet" href="{{ asset(mix('css/custom-rtl.css')) }}" />
    <link rel="stylesheet" href="{{ asset(mix('css/style-rtl.css')) }}" />
@endif
@stack('component-style')
{{-- user custom styles --}}
<link rel="stylesheet" href="{{ asset(mix('css/style.css')) }}" />
{{-- Laravel Style --}}
<link rel="stylesheet" href="{{ asset(mix('css/overrides.css')) }}" />
<link rel="stylesheet" href="{{ asset(mix('css/color-overrides.css')) }}" />
<style>
    [x-cloak] {
        display: none !important;
    }

    body {
        font-family: 'Inter', sans-serif !important;
    }

    .table th,
    .table td {
        padding: 0.72rem 0.7rem !important;
    }

    .table td img {
        width: 45px !important;
    }
</style>
