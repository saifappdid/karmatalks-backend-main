@extends('layouts/fullLayoutMaster')

@section('title', 'Astro Login')

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            position: relative;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0d1b2a;
            /* fallback color */
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('images/astrodoodle.jpg') }}') center center / cover no-repeat;
            opacity: 0.6;
            /* adjust this value (0 to 1) to control the opacity */
            z-index: 0;
        }



        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 70rem;
            display: flex;
            gap: 4rem;
            margin: 3rem 1rem;
            border-radius: 16px;
            backdrop-filter: blur(8px);
            margin-top: 15rem;
        }

        .left-section,
        .right-section {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .left-section {
            color: #fff;
            text-align: center;
        }

        .left-section img {
            width: 220px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .photographer-heading {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 32px;
            line-height: 1.3;
            color: #DA5917;
        }

        .right-section {
            width: 100%;
            padding: 2rem;
            border-radius: 16px;
            /* background: rgba(13, 27, 42, 0.6); */
            backdrop-filter: blur(10px);
        }

        .login-box h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 28px;
            text-align: center;
            color: #DA5917;
            margin-bottom: 10px;
        }

        .login-box .sub-text {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            text-align: center;
            color: #DA5917;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 5px;
            color: #ccc;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #888;
        }

        .input-box input {
            width: 100%;
            padding: 12px 40px 12px 40px;
            border: 1px solid #444;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #000000;
            font-size: 14px;
            transition: border 0.3s ease, background 0.3s ease;
        }

        .input-box input:focus {
            border-color: #DA5917;
            background: rgba(255, 255, 255, 0.1);
            outline: none;
        }

        .input-box i.bi-eye {
            left: auto;
            right: 12px;
            cursor: pointer;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 20px;
            color: #aaa;
        }

        .options a {
            color: ##DA5917;
            text-decoration: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #DA5917;
            border: none;
            border-radius: 8px;
            color: #1b263b;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #ffb733;
        }

        .register-text {
            text-align: center;
            font-size: 13px;
            color: #aaa;
        }

        .register-text a {
            color: #DA5917;
            font-weight: 600;
            text-decoration: none;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider span {
            background: rgba(13, 27, 42, 0.95);
            padding: 0 10px;
            position: relative;
            z-index: 1;
            color: #aaa;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: #444;
            z-index: 0;
        }

        .google-btn {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .google-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #DA5917;
        }

        .google-btn img {
            width: 20px;
            height: 20px;
        }
    </style>
@endsection

@section('content')
    <section class="auth-login d-flex justify-content-center align-items-center">
        <div class="container">
            <!-- Left section -->
            <div class="left-section">
                <img src="{{ asset('images/astroimg.jpg') }}" alt="Astro Image">
                <p class="photographer-heading">Explore the universe<br>within you</p>
            </div>

            <!-- Right section -->
            <div class="right-section">
                <div class="login-box">
                    <h2>Embark on your Astro Journey</h2>
                    <p class="sub-text">Login to explore cosmic possibilities.</p>

                    <form method="post" action="{{ route('login') }}">
                        @csrf

                        <label for="login-email">Email Address</label>
                        <div class="input-box">
                            <i class="bi bi-envelope"></i>
                            <input type="email" id="login-email" name="email" placeholder="john@example.com"
                                value="{{ old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <label for="login-password">Password</label>
                        <div class="input-box">
                            <i class="bi bi-lock"></i>
                            <input type="password" id="login-password" name="password" placeholder="••••••••" required>
                            <i class="bi bi-eye toggle-password"></i>
                        </div>

                        <div class="options">
                            <label><input type="checkbox" name="remember"> Remember me</label>
                            @if (Route::has('forgot-password'))
                                <a href="{{ route('forgot-password') }}">Forgot Password?</a>
                            @else
                                <a href="#">Forgot Password?</a>
                            @endif
                        </div>

                        <button type="submit" class="login-btn">Login</button>

                        <p class="register-text">Don't have an account? <a href="#">Contact Admin</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.toggle-password').on('click', function() {
                const passwordInput = $('#login-password');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).toggleClass('bi-eye bi-eye-slash');
            });
        });

        function snb(type, head, text) {
            toastr[type](text, head, {
                closeButton: true,
                tapToDismiss: false,
                showMethod: "slideDown",
                hideMethod: "slideUp",
                timeOut: 2000,
                progressBar: true,
            });
        }

        @if (session('status'))
            snb('error', 'Failed', `{{ session('status') }}`);
        @endif
    </script>
@endsection
