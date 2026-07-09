<?php

use App\Http\Controllers\API\AUHT\AuthController;
use App\Http\Controllers\API\AUHT\ForgetPasswordController;
use App\Http\Middleware\SetLang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Auth Routes
Route::middleware([SetLang::class])->group(function (){
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/verify-otp', 'verifyOtp');
        Route::post('/resend-otp', 'resendOtp');
        Route::post('/login', 'login');
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/profile', 'profile');
            Route::post('/update-profile', 'updateProfile');
            Route::post('/logout', 'logout');
            Route::post('/logout-all-devices', 'logoutAllDevice');
        });
    });
    Route::controller(ForgetPasswordController::class)->group(function () {
        Route::post('/forget-password', 'SendEmail');
        Route::post('/forget-password/verify-otp', 'VerifyOtp');
        Route::post('/forget-password/reset', 'ResetPassword');
        Route::post('/forget-password/resend-otp', 'resendOtp');
    });
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
});