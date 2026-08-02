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
        // Live session IDs computed once and reused for realtime status display
        $liveSessionIds = LoginLog::liveSessionIds();

        $query = LoginLog::latest('login_at');

        // Filter by user type
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        // Filter by realtime status
        $this->applyStatusFilter($query, $request);

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

        // Annotate each log with its realtime status (avoids N+1 session lookups)
        $logs->getCollection()->transform(function ($log) use ($liveSessionIds) {
            $log->live_status = $log->status($liveSessionIds);
            return $log;
        });

        // Stats (Active Sessions is now realtime)
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
     * Apply the realtime status filter (active | expired | logged_out).
     * Keeps the legacy boolean ?active=1 filter working too.
     */
    private function applyStatusFilter(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        // Legacy filter: ?active=1 -> active only
        if ($request->boolean('active')) {
            $query->active();
            return;
        }

        switch ($request->input('status')) {
            case 'active':
                $query->active();
                break;
            case 'expired':
                $live = LoginLog::liveSessionIds();
                $query->whereNull('logout_at')
                    ->when($live->isNotEmpty(), fn ($q) => $q->whereNotIn('session_id', $live));
                break;
            case 'logged_out':
                $query->whereNotNull('logout_at');
                break;
        }
    }

    /**
     * Export login logs to CSV.
     */
    public function exportCsv(Request $request)
    {
        $liveSessionIds = LoginLog::liveSessionIds();

        $query = LoginLog::latest('login_at');

        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }
        $this->applyStatusFilter($query, $request);

        $logs = $query->get();

        $headers = ['User Type', 'Name', 'Email', 'IP Address', 'Login At', 'Logout At', 'Status', 'User Agent'];

        $rows = $logs->map(fn($log) => [
            $log->user_type === 'admin' ? 'Admin/Client' : 'Registrant',
            $log->name,
            $log->email,
            $log->ip_address ?? '-',
            $log->login_at ? $log->login_at->format('Y-m-d H:i:s') : '-',
            $log->logout_at ? $log->logout_at->format('Y-m-d H:i:s') : ($log->status($liveSessionIds) === 'active' ? 'Still Active' : 'Expired'),
            ucfirst($log->status($liveSessionIds)),
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
