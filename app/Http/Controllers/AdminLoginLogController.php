<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;

class AdminLoginLogController extends Controller
{
    /**
     * Display login logs for all user types.
     */
    public function index(Request $request)
    {
        $query = LoginLog::latest('login_at');

        // Filter by user type
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        // Filter by active sessions only
        if ($request->boolean('active')) {
            $query->active();
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('login_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('login_at', '<=', $request->to);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(30);

        // Stats
        $totalLogins = LoginLog::count();
        $activeSessions = LoginLog::active()->count();
        $todayLogins = LoginLog::today()->count();
        $adminLogins = LoginLog::admins()->count();
        $registrantLogins = LoginLog::registrants()->count();

        return view('admin.management.login-logs', compact(
            'logs',
            'totalLogins',
            'activeSessions',
            'todayLogins',
            'adminLogins',
            'registrantLogins'
        ));
    }

    /**
     * Export login logs to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = LoginLog::latest('login_at');

        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }
        if ($request->boolean('active')) {
            $query->active();
        }

        $logs = $query->get();

        $headers = ['User Type', 'Name', 'Email', 'IP Address', 'Login At', 'Logout At', 'Status', 'User Agent'];

        $rows = $logs->map(fn($log) => [
            $log->user_type === 'admin' ? 'Admin/Client' : 'Registrant',
            $log->name,
            $log->email,
            $log->ip_address ?? '-',
            $log->login_at ? $log->login_at->format('Y-m-d H:i:s') : '-',
            $log->logout_at ? $log->logout_at->format('Y-m-d H:i:s') : 'Still Active',
            $log->isActive() ? 'Active' : 'Logged Out',
            $log->user_agent ?? '-',
        ])->toArray();

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'login-logs-' . now()->format('YmdHis') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
