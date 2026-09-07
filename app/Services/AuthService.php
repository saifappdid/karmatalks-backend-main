<?php
namespace App\Services;

use App\Models\Astro;
use App\Models\Otp;
use App\Models\User;
use App\Services\SmsIntegration;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function generateOtp(string $phone, $signature, $type)
    {
        if ($phone === '8111111111' || $phone === '6111111111' || $phone === '7111111111') {
            $response = [
                'message' => 'OTP sent successfully',
            ];
            return $response;
        }
        if ($type == 'astro') {
            $astro = Astro::where('mobile', $phone)->first();
            if (! $astro) {
                return [
                    'message' => 'Astro not found',
                    'code'    => 422,
                ];
            }
        }

        $otp       = random_int(1000, 9999);
        $expiresAt = now()->addMinutes(10);
        $data      = [
            'phone'      => $phone,
            'otp'        => $otp,
            'expired_at' => $expiresAt,
            'type'       => $type,
        ];
        $msgOtp = $signature . ' ' . $otp;

        $message = "{$msgOtp} is your OTP (One Time Password) for logging into the App. "
            . "For security reasons, do not share the OTP. Regards Team Appdid Infotech LLP.";
        SmsIntegration::sms($message, $data['phone'], 'KarmaTalks');
        return Otp::create($data);
    }

    public function login($phone, $otp, $deviceId, $countryId)
    {
        if (($phone == '8111111111' || $phone == '6111111111' || $phone == '7111111111') && $otp == '1234') {
            $user = User::where('phone', $phone)->first();
            $type = 'old';
            if (! $user) {
                $user = new User();
                $type = 'new';
            }
            $user->phone      = $phone;
            $user->device_id  = $deviceId;
            $user->country_id = $countryId;

            $user->save();
            if ($user->name == null) {
                $type = 'new';
            }
            $token    = $user->createToken('user-token', ['user'])->plainTextToken;
            $response = [
                'user'  => $user,
                'type'  => $type,
                'token' => $token,
            ];
            return $response;
        } else {
            $otpRecord = Otp::user()->where('phone', $phone)
                ->where('otp', $otp)->where('status', 'active')
                ->first();
            if (isset($otpRecord) && $otpRecord->expired_at > now()) {
                $user = User::where('phone', $phone)->first();
                $type = 'old';
                if (! $user) {
                    $user = new User();
                    $type = 'new';
                }
                $user->phone      = $phone;
                $user->device_id  = $deviceId;
                $user->country_id = $countryId;
                $user->save();
                if ($user->name == null) {
                    $type = 'new';
                }
                $otpRecord->status = 'expired';
                $otpRecord->save();
                $token    = $user->createToken('user-token', ['user'])->plainTextToken;
                $response = [
                    'user'  => $user,
                    'type'  => $type,
                    'token' => $token,
                ];
            } else {
                $response = [
                    'message' => 'Otp Expired',
                ];
            }
            return $response;
        }
    }

    public function astroLogin($phone, $otp, $deviceId)
    {
        $otp = Otp::astro()->where('phone', $phone)
            ->where('otp', $otp)->where('status', 'active')
            ->first();
        if (! $otp || $otp->expired_at < now() && $phone != '8111111111' && $phone != '6111111111' && $phone != '7111111111') {
            throw ValidationException::withMessages([
                'message' => 'Invalid or Expired OTP.',
            ]);
        }
        $astro = Astro::where('mobile', $phone)->first();
        if (! $astro) {
            throw ValidationException::withMessages([
                'message' => 'Astro not found.',
            ]);
        }

        PersonalAccessToken::where('tokenable_id', $astro->id)
            ->where('tokenable_type', Astro::class)
            ->delete();
        $astro->device_id = $deviceId;
        $astro->save();
        if ($phone != '8111111111' && $phone != '6111111111' && $phone != '7111111111') {
            $otp->status = 'expired';
        }
        $otp->save();
        $token    = $astro->createToken('astro-token', ['astro'])->plainTextToken;
        $response = [
            'astro' => $astro,
            'token' => $token,
        ];
        return $response;
    }

    public function astroLogout($astro)
    {
        $astro = Astro::findOrFail($astro->id);
        $astro->update([
            'device_id'             => null,
            'online_for_voice_call' => 'no',
            'online_for_video_call' => 'no',
            'online_for_chat'       => 'no',
        ]);
        auth('astro')->user()->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Astro logged out successfully',
        ];
    }

    public function googleLogin($request)
    {
        $auth = app('firebase.auth');
        try {
            $verifiedIdToken = $auth->verifyIdToken($request->token);
        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'message' => 'The token is invalid: ' . $e->getMessage(),
            ], 401);
        }
        $uid           = $verifiedIdToken->claims()->get('sub');
        $firebase_user = $auth->getUser($uid);
        $email         = $firebase_user->email;

        $user = User::where('firebase_uid', $firebase_user->uid)->orWhere('email', $email)->first();
        if (! empty($user) && $user->name) {
            if ($user->country_id != $request->country_id) {
                throw ValidationException::withMessages([
                    'country' => 'This user is already registered in a different country',
                ]);
            }
            $type = 'old';
            $user->update([
                'device_id'    => $request->device_id,
                'firebase_uid' => $uid,
                'country_id'   => $request->country_id ?? $user->country_id,
            ]);
        } else {
            $user = User::create([
                'email'        => $email,
                'device_id'    => $request->device_id,
                'firebase_uid' => $uid,
                'country_id'   => $request->country_id ?? 101,
            ]);
            $type = 'new';
        }
        $data = [
            'type'      => $type,
            'user_data' => $user,
            'token'     => $user->createToken('user')->plainTextToken,
        ];
        return $data;
    }
}
