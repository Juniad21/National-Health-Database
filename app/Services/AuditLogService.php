<?php

namespace App\Services;

use App\Models\AccessLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log a system action.
     *
     * @param string $action The action performed
     * @param string|null $description Additional details
     * @param string|null $module The module where the action occurred
     * @param string $severity The severity level (low, medium, high, critical)
     * @param string|null $targetType The class name of the target model
     * @param int|null $targetId The ID of the target model
     * @param mixed|null $oldValue Previous value
     * @param mixed|null $newValue New value
     * @return void
     */
    public static function logAction(
        string $action, 
        ?string $description = null, 
        ?string $module = null, 
        string $severity = 'low', 
        ?string $targetType = null, 
        ?int $targetId = null, 
        $oldValue = null, 
        $newValue = null,
        ?int $userId = null
    ) {
        try {
            $user = Auth::user();
            $finalUserId = $userId ?? ($user ? $user->id : null);
            $role = $user ? $user->role : 'guest';

            $hospitalId = null;
            if ($user && $user->role === 'hospital') {
                $hospitalId = $user->hospital ? $user->hospital->id : null;
            }

            AccessLog::create([
                'user_id' => $finalUserId,
                'hospital_id' => $hospitalId,
                'role' => $role,
                'action' => $action,
                'module' => $module,
                'severity' => $severity,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : $oldValue,
                'new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : $newValue,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to write to access_logs: ' . $e->getMessage());
        }
    }
}
