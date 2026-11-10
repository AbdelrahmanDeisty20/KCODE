<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an action to activity_logs table.
     */
    public static function log(
        string $event,
        ?string $description = null,
        ?string $subjectType = null,
        mixed $subjectId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        ?string $userName = null
    ): ?ActivityLog {
        try {
            $user = Auth::user() ?? auth('sanctum')->user();

            $finalUserId   = $userId ?? $user?->id;
            $finalUserName = $userName ?? ($user?->name ?? 'زائر / نظام');

            return ActivityLog::create([
                'user_id'      => $finalUserId,
                'user_name'    => $finalUserName,
                'event'        => $event,
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId ? (string) $subjectId : null,
                'description'  => $description,
                'old_values'   => $oldValues,
                'new_values'   => $newValues,
                'ip_address'   => Request::ip() ?? '127.0.0.1',
                'user_agent'   => Request::header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            Log::warning("ActivityLogger failed: " . $e->getMessage(), [
                'event' => $event,
                'description' => $description,
            ]);
            return null;
        }
    }
}
