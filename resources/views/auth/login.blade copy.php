@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection

@section('content')



    @if (session('status'))
        <div class="alert alert-danger">
            {{ session('status') }}
        </div>
    @endif
    <div class="auth-wrapper auth-v1 px-2">
        <div class="auth-inner py-2">
            <!-- Login v1 -->
            <div class="card mb-0">
                <div class="card-body">
                    <a href="javascript:void(0);" class="brand-logo align-items-center flex-column">

                        <img style="max-width: 5em" src="{{ asset('images/logo/logo.png') }}" alt="">

                        <h2 class="brand-text text-primary ">{{ Str::ucfirst(env('APP_NAME')) }}</h2>
                    </a>

                    <h4 class="card-title mb-1">Welcome to {{ Str::ucfirst(env('APP_NAME')) }}! 👋</h4>
                    <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>

                    <form class="auth-login-form mt-2" method="post" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="login-email" class="form-label">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="login-email"
                                name="email" placeholder="john@example.com" aria-describedby="login-email" tabindex="1"
                                autofocus value="{{ old('email') }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label for="login-password">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}">
                                        <small>Forgot Password?</small>
                                    </a>
                                @endif
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control   form-control-merge" id="login-password"
                                    name="password" tabindex="2"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="login-password" />
                                <div class="input-group-append">
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="remember-me" name="remember"
                                    tabindex="3" {{ old('remember-me') ? 'checked' : '' }} />
                                <label class="custom-control-label" for="remember-me"> Remember Me </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary auth-btn btn-block" tabindex="4">Sign in</button>
                    </form>


                </div>
            </div>
            <!-- /Login v1 -->
        </div>
    </div>
@endsection
@section('vendor-script')
    <!-- vendor files -->

    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
@endsection
@section('page-script')
    <script>
        var isRtl = $("html").attr("data-textdirection") === "rtl";

        function snb(type, head, text) {
            toastr[type](text, head, {
                closeButton: true,
                tapToDismiss: false,
                showMethod: "slideDown",
                hideMethod: "slideUp",
                timeOut: 2000,
                rtl: isRtl,
                progressBar: true,
            });
        }
        @if (session('status'))
            snb('error', 'Failed', 'username or password is incorrect');
        @endif
    </script>

@endsection
