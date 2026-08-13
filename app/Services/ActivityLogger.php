<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
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
        ?array $newValues = null
    ): ActivityLog {
        $user = Auth::user();

        return ActivityLog::create([
            'user_id'      => $user?->id,
            'user_name'    => $user?->name ?? 'زائر / نظام',
            'event'        => $event,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId ? (string) $subjectId : null,
            'description'  => $description,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::header('User-Agent'),
        ]);
    }
}
