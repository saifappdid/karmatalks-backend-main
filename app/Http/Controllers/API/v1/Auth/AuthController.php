<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ResponseTrait;
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function generateOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required',
            'signature' => 'required',
            'type' => 'required|in:user,astro'
        ]);
        $response = $this->authService->generateOtp($validated['phone'], $request->signature, $validated['type']);
        return $this->success(message: $response['message'] ?? 'OTP generated successfully', code: $response['code'] ?? 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required',
            'device_id' => 'required',
            'country_id' => 'nullable|numeric',
        ]);

        $response = $this->authService->login($request->phone, $request->otp, $request->device_id, ($request->country ??  ($request->country_id == 0 ? 101 : $request->country_id)));
        return $this->success($response);
    }

    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);
    }

    public function astroLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required',
            'device_id' => 'required',
        ]);
        $response = $this->authService->astroLogin($request->phone, $request->otp, $request->device_id);
        return $this->success($response, 'Astro logged in successfully');
    }

    public function astroLogout()
    {
        $this->authService->astroLogout(auth('astro')->user());
        return $this->success(message: 'Astro logged out successfully');
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'token'     => 'required|string',
            'device_id' => 'required|string',
            'country_id' => 'required|numeric',
        ]);
        $data = $this->authService->googleLogin($request);
        return $data;
    }
}
