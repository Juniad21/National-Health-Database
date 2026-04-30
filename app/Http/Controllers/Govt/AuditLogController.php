<?php

namespace App\Http\Controllers\Govt;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AccessLog::with(['user', 'hospital'])->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhere('module', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%")
                ->orWhere('target_id', $search);
            });
        }

        // Filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('govt_admin.audit_logs.index', [
            'logs' => $logs
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = AccessLog::with(['user', 'hospital'])->orderBy('created_at', 'desc');
        
        // Apply same filters as index
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('severity')) $query->where('severity', $request->severity);
        if ($request->filled('module')) $query->where('module', $request->module);
        
        $logs = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=audit_logs_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Date', 'User', 'Role', 'Action', 'Module', 'Severity', 'Description', 'IP Address'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System',
                    ucfirst($log->role),
                    $log->action,
                    $log->module ?? 'General',
                    strtoupper($log->severity),
                    $log->description,
                    $log->ip_address
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
