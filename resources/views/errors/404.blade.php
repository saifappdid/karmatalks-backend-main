@php
$configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Error 404')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-misc.css')) }}">
    <style>
        html .content {
            margin-left: 0px;
        }

        html .content.app-content {
            padding: 0px;
        }
    </style>
@endsection
@section('content')
    <!-- Error page-->
    <div class="misc-wrapper">
        <a class="brand-logo align-items-center" href="javascript:void(0);">
            <img src="{{ asset('images/logo/logo.png') }}" alt="logo" height="50" width="50">
            <h2 class="brand-text text-primary ml-1 mb-0">{{ env('APP_NAME') }}</h2>
        </a>
        <div class="misc-inner p-2 p-sm-3">
            <div class="w-100 text-center">
                <h2 class="mb-1">Page Not Found 🕵🏻‍♀️</h2>
                <p class="mb-2">Oops! 😖 The requested URL was not found on this server.</p>
                <a class="btn btn-primary mb-2 btn-sm-block" href="{{ url('/') }}">Back to home</a>

                @if ($configData['theme'] === 'dark')
                    <img class="img-fluid" src="{{ asset('images/pages/error-dark.svg') }}" alt="Error page" />
                @else
                    <img class="img-fluid" src="{{ asset('images/pages/error.svg') }}" alt="Error page" />
                @endif
            </div>
        </div>
    </div>
    <!-- / Error page-->
@endsection
