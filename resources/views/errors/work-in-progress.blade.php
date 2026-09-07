@php
$configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Coming Soon')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-misc.css')) }}">
    <style>
        .content {
            padding: 0px !important;
            margin: 0px !important;
        }
    </style>
@endsection

@section('content')
    <!-- Coming soon page-->
    <div class="misc-wrapper">
        <a class="brand-logo align-items-center" href="javascript:void(0);">
            <img src="{{ asset('images/logo/logo.png') }}" alt="logo" height="50" width="50">
            <h2 class="brand-text text-primary ml-1">Curvicare</h2>
        </a>
        <div class="misc-inner p-2 p-sm-3">
            <div class="w-100 text-center">
                <h2 class="mb-1">Work in progress 🚀</h2>
                <p class="mb-3">We're creating something awesome. We will let you know when it's ready!</p>
                <a class="btn btn-primary mb-2 btn-sm-block" href="{{ url('admin/') }}">Back to home</a>
                @if ($configData['theme'] === 'dark')
                    <img class="img-fluid" src="{{ asset('images/pages/coming-soon-dark.svg') }}"
                        alt="Coming soon page" />
                @else
                    <img class="img-fluid" src="{{ asset('images/pages/coming-soon.svg') }}" alt="Coming soon page" />
                @endif
            </div>
        </div>
    </div>
    <!-- / Coming soon page-->
@endsection
