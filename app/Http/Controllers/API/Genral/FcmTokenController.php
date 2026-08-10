<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\FcmTokenRequest;
use App\Services\FcmTokenService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FcmTokenController extends Controller
{
    use ApiResponse;

    public function __construct(protected FcmTokenService $fcmTokenService) {}

    /**
     * Store FCM Token without user_id (Guest token).
     */
    public function storeGuestToken(FcmTokenRequest $request): JsonResponse
    {
        $result = $this->fcmTokenService->storeToken(null, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message'], 500);
        }

        return $this->success($result['message'], $result['data']);
    }

    /**
     * Store FCM Token for authenticated user.
     */
    public function storeUserToken(FcmTokenRequest $request): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $this->fcmTokenService->storeToken($userId, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message'], 500);
        }

        return $this->success($result['message'], $result['data']);
    }
}
