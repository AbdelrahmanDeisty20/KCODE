<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Hash;
use Mail;
use Str;

class ForgetPassword
{
    public function SendToEmail($email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.user_not_found'),
            ], 422);
        }

        $otp = Otp::where('email', $email)
            ->where('type', 'reset_password')
            ->latest()
            ->first();

        if ($otp && $otp->created_at->addMinute() > now()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_wait_resend'),
            ], 429);
        }

        Otp::where('email', $email)
            ->where('type', 'reset_password')
            ->delete();

        $code = random_int(100000, 999999);
        $codeHash = Hash::make((string) $code);
        $expiresAt = now()->addMinutes(5);
        Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name, 'reset_password'));
        Otp::create([
            'email' => $user->email,
            'code' => $codeHash,
            'type' => 'reset_password',
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
        ]);
        return response()->json([
            'status' => true,
            'message' => __('messages.otpSentSuccessfully'),
        ]);
    }

    public function VerifyOtp($email, $code)
    {
        $otpRecord = Otp::where('email', $email)
            ->where('type', 'reset_password')
            ->first();
        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_invalid'),
            ], 422);
        }
        if ($otpRecord->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_expired'),
            ], 422);
        }
        if (!Hash::check((string) $code, $otpRecord->code)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_invalid'),
            ], 422);
        }
        $token = Str::random(60);
        $otpRecord->update([
            'reset_token' => $token,
            'verified_at' => now(),
        ]);
        return response()->json([
            'status' => true,
            'message' => __('messages.otpVerifiedSuccessfully'),
            'token' => $token,
        ]);
    }

    public function ResetPassword(array $data)
    {
        $otpRecord = Otp::where('email', $data['email'])
            ->where('reset_token', $data['token'])
            ->where('type', 'reset_password')
            ->whereNotNull('verified_at')
            ->first();
        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalid_token'),
            ], 422);
        }
        $otpRecord->user->update([
            'password' => Hash::make($data['password']),
        ]);
        $otpRecord->delete();
        return response()->json([
            'status' => true,
            'message' => __('messages.passwordResetSuccessfully'),
        ]);
    }

    public function resendOtp($email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.user_not_found'),
            ], 422);
        }

        $otp = Otp::where('email', $email)
            ->where('type', 'reset_password')
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'status' => false,
                'message' => __('messages.no_otp_request_found'),
            ], 422);
        }

        if ($otp->created_at->addMinute() > now()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_wait_resend'),
            ], 429);
        }

        Otp::where('email', $email)
            ->where('type', 'reset_password')
            ->delete();

        $code = random_int(100000, 999999);
        $codeHash = Hash::make((string) $code);
        $expiresAt = now()->addMinutes(5);
        Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->name, 'reset_password'));
        Otp::create([
            'email' => $user->email,
            'code' => $codeHash,
            'type' => 'reset_password',
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
        ]);
        return response()->json([
            'status' => true,
            'message' => __('messages.otpResentSuccessfully'),
        ]);
    }
}
