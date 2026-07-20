<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\LOYALTY\LoyaltyResource;
use App\Http\Resources\API\LOYALTY\LoyaltyLevelResource;
use App\Services\LoyaltyService;
use App\Traits\ApiResponse;

class LoyaltyController extends Controller
{
    use ApiResponse;

    public function __construct(private LoyaltyService $loyaltyService) {}

    /**
     * Get the authenticated user's loyalty profile.
     */
    public function getLoyaltyProfile()
    {
        $userId = auth('sanctum')->id();
        $result = $this->loyaltyService->getUserLoyaltyData($userId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            new LoyaltyResource($result['data']),
            $result['message']
        );
    }

    /**
     * Get all active loyalty levels.
     */
    public function getLoyaltyLevels()
    {
        $result = $this->loyaltyService->getAllLevels();

        return $this->success(
            LoyaltyLevelResource::collection($result['data']),
            $result['message']
        );
    }
}
