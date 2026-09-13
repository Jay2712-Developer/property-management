<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Record an activity log entry from an instance context.
     *
     * @param string $action Description or action name (e.g. 'created', 'updated', 'deleted')
     * @param string $module Module category (e.g. 'Property', 'Agent', 'User', 'Settings')
     * @param int|null $recordId Associated record ID
     * @param int|null $userId User ID responsible (defaults to authenticated user)
     * @param string|null $ipAddress Client IP address (defaults to request()->ip())
     * @param string|null $userAgent Client User Agent (defaults to request()->userAgent())
     * @return ActivityLog
     */
    public function logActivity(
        string $action,
        string $module,
        ?int $recordId = null,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Record an activity log entry from a static context.
     *
     * @param string $action Description or action name
     * @param string $module Module category
     * @param int|null $recordId Associated record ID
     * @param int|null $userId User ID responsible
     * @param string|null $ipAddress Client IP address
     * @param string|null $userAgent Client User Agent
     * @return ActivityLog
     */
    public static function logAction(
        string $action,
        string $module,
        ?int $recordId = null,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }
}
