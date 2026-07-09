<?php

namespace App\Http\Controllers\API\AUHT;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\FORGETPASSWORD\ResetPasswordRequest;
use App\Http\Requests\API\FORGETPASSWORD\SendEmailRequest;
use App\Http\Requests\API\FORGETPASSWORD\VerifyOtpRequest;
use App\Services\ForgetPassword;

class ForgetPasswordController extends Controller
{
    protected ForgetPassword $forgetPassword;
    public function __construct(ForgetPassword $forgetPassword)
    {
        $this->forgetPassword = $forgetPassword;
    }
    public function sendEmail(SendEmailRequest $request)
    {
        $data = $request->validated();
        return $this->forgetPassword->SendToEmail($data['email']);
    }

    public function VerifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();
        return $this->forgetPassword->VerifyOtp($data['email'], $data['code']);
    }

    public function ResetPassword(ResetPasswordRequest $request)
    {
        return $this->forgetPassword->ResetPassword($request->validated());
    }

    public function resendOtp(SendEmailRequest $request)
    {
        $data = $request->validated();
        return $this->forgetPassword->resendOtp($data['email']);
    }
}
