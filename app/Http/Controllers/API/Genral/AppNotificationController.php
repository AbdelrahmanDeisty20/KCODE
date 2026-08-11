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
     * Get authenticated user notifications paginated (Personal + General).
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
     * Get public general notifications (where user_id IS NULL).
     * Public endpoint - No Auth required.
     */
    public function publicGeneral(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);

        $result = $this->notificationService->getPublicGeneralNotifications($perPage);

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
     * Mark notification as read (or mark all as read if id is 'all' or 'read-all').
     */
    public function markAsRead(Request $request, mixed $id = null): JsonResponse
    {
        $userId = $request->user()->id;

        if ($id === null || $id === 'all' || $id === 'read-all') {
            return $this->markAllAsRead($request);
        }

        $result = $this->notificationService->markAsRead($userId, (int) $id);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success(new AppNotificationResource($result['data']), $result['message']);
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $this->notificationService->markAllAsRead($userId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Delete a specific notification for the authenticated user.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $this->notificationService->deleteNotification($userId, $id);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Clear / Delete all notifications for the authenticated user.
     */
    public function clearAll(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $this->notificationService->clearAllNotifications($userId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success([], $result['message']);
    }
}
