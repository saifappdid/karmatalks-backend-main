@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
{{-- {!! Helper::applClasses() !!} --}}
@php
    $configData = Helper::applClasses();

@endphp

<html
    lang="@if (session()->has('locale')) {{ session()->get('locale') }}@else{{ $configData['defaultLanguage'] }} @endif"
    data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}"
    class="{{ $configData['theme'] === 'light' ? '' : $configData['layoutTheme'] }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ Str::ucfirst(env('APP_NAME')) }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/custom/loader.css')) }}" />
    <script src="{{ asset(mix('js/core/scripts.js')) }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/inter-ui/4.1.1/inter.css"
        integrity="sha512-PkJQDETCHOcbWeb8KvCs4lcn0eYrRRrlXdEWdml4HUHUXgAcdCRhv5YBJ4kGFYSg/ae2vBoTtcKsVXlLSBAc7w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    {{-- Include core + vendor Styles --}}
    @include('panels/styles')

</head>



@isset($configData['mainLayoutType'])
    @extends($configData['mainLayoutType'] === 'horizontal' ? 'layouts.horizontalLayoutMaster' : 'layouts.verticalLayoutMaster')
@endisset
