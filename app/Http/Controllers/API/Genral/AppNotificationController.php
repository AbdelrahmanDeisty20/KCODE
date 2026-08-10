<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\NOTIFICATION\AppNotificationResource;
use App\Services\AppNotificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    use ApiResponse;

    public function __construct(protected AppNotificationService $notificationService) {}

    /**
     * Get authenticated user notifications paginated.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 10);

        $result = $this->notificationService->getUserNotifications($userId, $perPage);

        if (!$result['status']) {
            return $this->error($result['message'], 500);
        }

        return $this->paginated(
            AppNotificationResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $this->notificationService->markAsRead($userId, $id);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success(new AppNotificationResource($result['data']), $result['message']);
    }
}
