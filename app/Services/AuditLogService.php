<?php

namespace App\Services;

use App\Models\AccessLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log a hospital action.
     *
     * @param string $action The action performed (e.g., 'hospital dashboard viewed', 'hospital resource updated')
     * @param string|null $description Additional details
     * @param string|null $targetType The class name or type of the target model
     * @param int|null $targetId The ID of the target model
     * @return void
     */
    public static function logHospitalAction(string $action, ?string $description = null, ?string $targetType = null, ?int $targetId = null)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return;
            }

            // Only log for hospital role as requested, or adjust if other roles need it
            if ($user->role !== 'hospital') {
                return;
            }

            $hospital = $user->hospital;

            AccessLog::create([
                'user_id' => $user->id,
                'hospital_id' => $hospital ? $hospital->id : null,
                'role' => $user->role,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Keep logging lightweight and do not break the main flow if it fails
            \Log::error('Failed to write to access_logs: ' . $e->getMessage());
        }
    }
}
