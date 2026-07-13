<?php

namespace App\Services;

use App\Http\Resources\API\AUHT\UserResource;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Socialite\Socialite;
use Auth;

class AuthService
{
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if ($user->status != 'active') {
            return response()->json([
                'status' => false,
                'message' => __('messages.userNotActive'),
            ], 401);
        }
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalidPassword'),
            ], 401);
        }
        $refreshToken = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $refreshToken),
            'expires_at' => Carbon::now()->addDays(7),
        ]);
        return response()->json([
            'status' => true,
            'message' => __('messages.loginSuccessFully'),
            'data' => [
                'user' => new UserResource($user->load('skin_type')),
                'token' => $user->createToken('auth_token')->plainTextToken,
                'refresh_token' => $refreshToken,
            ],
        ]);
    }

    public function register(array $data)
    {
        $image = null;
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $image = time() . '.' . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('users', $image, 'public');
        }
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'image' => $image,
            'status' => 'inactive',
            'skin_type_id' => $data['skin_type_id'] ?? null,
        ]);

        $user->refresh();  // تحديث الموديل لجلب القيم الافتراضية من قاعدة البيانات
        if ($user) {
            $code = random_int(100000, 999999);
            $codeHash = Hash::make((string) $code);
            Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name));
            Otp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'code' => $codeHash,
                'type' => 'register',
                'expires_at' => now()->addMinutes(5),
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => __('messages.accountCreatedSuccessfully'),
        ]);
    }

    public function verifyOtp(array $data)
    {
        // 1. البحث عن أحدث كود تم طلبه لهذا البريد الإلكتروني
        $otp = Otp::where('email', $data['email'])
            ->where('type', 'register')
            ->latest()
            ->first();

        // 2. التحقق من وجود السجل
        if (!$otp) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_invalid'),
            ], 400);
        }

        // 3. التحقق مما إذا كان الكود منتهي الصلاحية
        if ($otp->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_expired'),
            ], 400);
        }

        // 4. التحقق من مطابقة الكود المدخل مع الكود المشفر بالـ Hash
        if (!Hash::check($data['code'], $otp->code)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_invalid'),
            ], 400);
        }

        // 5. تفعيل حساب المستخدم
        $user = User::where('email', $otp->email)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->status = 'active';
            $user->save();
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.otpVerifiedSuccessfully'),
            'data' => [new UserResource($user->load('skin_type'))],
        ]);
    }

    public function resendOtp(array $data)
    {
        // 1. البحث عن أحدث كود تم إرساله لهذا البريد الإلكتروني ومن نوع register
        $otp = Otp::where('email', $data['email'])
            ->where('type', 'register')
            ->latest()
            ->first();

        // 2. التحقق من مرور دقيقة واحدة على الأقل قبل طلب رمز جديد
        if ($otp && $otp->created_at->addMinute() > now()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_wait_resend'),
            ], 429);
        }

        // 3. جلب بيانات المستخدم
        $user = User::where('email', $data['email'])->first();
        if ($user->email_verified_at) {
            return response()->json([
                'status' => false,
                'message' => __('messages.userAlreadyActive'),
            ], 401);
        }
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('validation.user_not_found'),
            ], 404);
        }

        // 4. حذف الرموز السابقة لنفس المستخدم لضمان بقاء رمز واحد صالح فقط
        Otp::where('email', $data['email'])
            ->where('type', 'register')
            ->delete();

        // 5. إنشاء رمز جديد وإرساله
        $code = random_int(100000, 999999);
        $codeHash = Hash::make((string) $code);

        Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name));

        Otp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $codeHash,
            'type' => 'register',
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.otpResentSuccessfully'),
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'message' => __('messages.profileSuccessFully'),
            'data' => [new UserResource($user->load('skin_type'))],
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => __('messages.logoutSuccessFully'),
        ]);
    }

    public function logoutAllDevice()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => __('messages.logoutSuccessFully'),
        ]);
    }

    public function updateProfile(array $data)
    {
        $user = Auth::user();
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $image = time() . '.' . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('users', $image, 'public');
            $data['image'] = $image;
        }
        // Handle Password Update
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);
        $user->update($data);
        return response()->json([
            'status' => true,
            'message' => __('messages.profileUpdatedSuccessfully'),
            'data' => [new UserResource($user->load('skin_type'))],
        ]);
    }

    public function refresh($refresh_token)
    {
        $refreshToken = RefreshToken::where('token', hash('sha256', $refresh_token))->first();
        if (!$refreshToken) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalid_token'),
            ], 422);
        }
        if ($refreshToken->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalid_token'),
            ], 422);
        }
        $user = $refreshToken->user;
        $user->tokens()->delete();
        $refreshToken->delete();
        $newRefreshToken = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $newRefreshToken),
            'expires_at' => Carbon::now()->addDays(7),
        ]);
        return response()->json([
            'status' => true,
            'message' => __('messages.TokenRefreshedSuccessfully'),
            'data' => [
                'user' => new UserResource($user->load('skin_type')),
                'token' => $user->createToken('auth_token')->plainTextToken,
            ],
        ]);
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
        $callbackPath = '/auth/google-callback';

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('messages.social_login_failed'),
                'data' => [
                    'redirect_url' => $frontendUrl . $callbackPath . '?error=' . urlencode(__('messages.social_login_failed')),
                ],
            ];
        }

        $existingUser = User::where('email', $socialUser->email)->first();

        if ($existingUser) {
            $existingUser->status = 'active';
            $existingUser->email_verified_at = $existingUser->email_verified_at ?? now();
            $existingUser->save();

            $token = $existingUser->createToken('auth_token')->plainTextToken;
            $userResource = new UserResource($existingUser);
        } else {
            $newUser = User::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => $socialUser->id,
                'image' => $socialUser->avatar,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $token = $newUser->createToken('auth_token')->plainTextToken;
            $userResource = new UserResource($newUser);
        }

        $query = http_build_query([
            'token' => $token,
            'id' => $userResource->id,
            'full_name' => $userResource->name,
            'email' => $userResource->email,
            'avatar_path' => $userResource->image_path ?? '',
        ]);

        return [
            'status' => true,
            'message' => __('messages.user_logged_in_successfully'),
            'data' => [
                'redirect_url' => $frontendUrl . $callbackPath . '?' . $query,
            ],
        ];
    }
}
