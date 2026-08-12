<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\DeleteGeneralNotificationRequest;
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
     * Public endpoint - No Auth required. Option to pass device_id via query/header to exclude deleted.
     */
    public function publicGeneral(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $deviceId = $request->get('device_id') ?? $request->header('X-Device-ID');

        $result = $this->notificationService->getPublicGeneralNotifications($deviceId, $perPage);

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
    public function destroy(Request $request, mixed $id = null): JsonResponse
    {
        $userId = $request->user()->id;

        if ($id === null || $id === 'clear-all') {
            return $this->clearAll($request);
        }

        $result = $this->notificationService->deleteNotification($userId, (int) $id);

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

    /**
     * Delete a specific public general notification by device_id.
     */
    public function destroyGeneral(DeleteGeneralNotificationRequest $request, mixed $id = null): JsonResponse
    {
        $deviceId = $request->validated('device_id');

        if ($id === null || $id === 'clear-all') {
            return $this->clearAllGeneral($request);
        }

        $result = $this->notificationService->deleteGeneralNotificationByDeviceId($deviceId, (int) $id);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Clear all public general notifications by device_id.
     */
    public function clearAllGeneral(DeleteGeneralNotificationRequest $request): JsonResponse
    {
        $deviceId = $request->validated('device_id');

        $result = $this->notificationService->clearAllGeneralNotificationsByDeviceId($deviceId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 500);
        }

        return $this->success([], $result['message']);
    }
}
