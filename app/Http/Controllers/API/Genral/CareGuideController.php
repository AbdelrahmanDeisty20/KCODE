<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\CareGuide\CareGuideResource;
use App\Services\CareGuideService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CareGuideController extends Controller
{
    use ApiResponse;

    public function __construct(private CareGuideService $careGuideService) {}

    /**
     * Get Care Guide modal content with guidance items and actions.
     */
    public function index(): JsonResponse
    {
        $result = $this->careGuideService->getCareGuide();

        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }

        return $this->success(
            new CareGuideResource($result['data']),
            $result['message']
        );
    }
}
