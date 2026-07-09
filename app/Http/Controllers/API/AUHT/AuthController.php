<?php

namespace App\Http\Controllers\API\AUHT;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\AUHT\LoginRequest;
use App\Http\Requests\API\AUHT\RegisterRequest;
use App\Http\Requests\API\AUHT\UpdateProfileRequest;
use App\Http\Requests\API\AUHT\VerifyOtpRequest;
use App\Http\Requests\API\AUHT\ResendOtpRequest;
use App\Http\Requests\API\FORGETPASSWORD\RefreshTokenRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;
class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        return $this->authService->register($data);
    }
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();
        return $this->authService->verifyOtp($data);
    }
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        return $this->authService->login($data);
    } 
    public function resendOtp(ResendOtpRequest $request)
    {
        $data = $request->validated();
        return $this->authService->resendOtp($data);
    }
    public function profile()
    {
        return $this->authService->profile();
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        return $this->authService->updateProfile($data);
    }
    public function logout()
    {
        return $this->authService->logout();
    }
    public function logoutAllDevice()
    {
        return $this->authService->logoutAllDevice();
    }
    public function refresh(RefreshTokenRequest $request)
    {
        $data = $request->validated();
        return $this->authService->refresh($data['refresh_token']);
    }
    public function redirectToProvider($provider)
    {
        return $this->authService->redirectToProvider($provider);
    }

    public function handleProviderCallback($provider)
    {
        $result = $this->authService->handleProviderCallback($provider);

        return redirect($result['data']['redirect_url']);
    }
}
