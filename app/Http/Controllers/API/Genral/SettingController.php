<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\SETTINGS\PhilosophyResource;
use App\Services\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    use ApiResponse;

    public function __construct(private SettingService $settingService) {}

    /**
     * Get KCODE Philosophy section settings.
     * GET /settings/philosophy
     */
    public function getPhilosophy(): JsonResponse
    {
        $result = $this->settingService->getPhilosophy();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new PhilosophyResource($result['data']), $result['message']);
    }

    /**
     * Get all store settings.
     * GET /settings
     */
    public function index(): JsonResponse
    {
        $result = $this->settingService->getAllSettings();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }
}
