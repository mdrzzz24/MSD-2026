<?php

namespace App\Http\Controllers;

use App\Mail\RegistrantApproved;
use App\Mail\RegistrantCredentials;
use App\Mail\RegistrantRejected;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with registrant statistics & overview.
     */
    public function dashboard()
    {
        // Core stats
        $total      = Registrant::count();
        $pending    = Registrant::pending()->count();
        $approved   = Registrant::approved()->count();
        $rejected   = Registrant::rejected()->count();

        // Recent registrants (last 7)
        $recentRegistrants = Registrant::latest()->take(7)->get();

        // Registrations per day (last 14 days)
        $dailyStats = Registrant::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count"),
            DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
        )
        ->where('created_at', '>=', now()->subDays(14))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        // Fill missing dates with zeros
        $chartData = [];
        $maxDaily = 0;
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $day = now()->subDays($i)->format('D');
            $stats = $dailyStats->get($date);
            $count = $stats ? (int) $stats->total : 0;
            $approvedCount = $stats ? (int) $stats->approved_count : 0;
            $pendingCount = $stats ? (int) $stats->pending_count : 0;
            $chartData[] = [
                'day'       => $day,
                'date'      => $date,
                'total'     => $count,
                'approved'  => $approvedCount,
                'pending'   => $pendingCount,
            ];
            if ($count > $maxDaily) $maxDaily = $count;
        }
        $maxDaily = max($maxDaily, 1);

        // Workshop stats
        $workshopCount = Workshop::count();
        $workshopRegistrations = DB::table('registrant_workshop')->count();

        // Registration trend (today vs yesterday)
        $todayCount = Registrant::whereDate('created_at', today())->count();
        $yesterdayCount = Registrant::whereDate('created_at', today()->subDay())->count();
        $trend = $yesterdayCount > 0 ? round(($todayCount - $yesterdayCount) / $yesterdayCount * 100) : ($todayCount > 0 ? 100 : 0);

        // Pending needing attention (older than 2 days)
        $stalePending = Registrant::pending()->where('created_at', '<', now()->subDays(2))->count();

        // Check-in stats
        $checkedInToday = Registrant::approved()->whereDate('checked_in_at', today())->count();
        $checkedInTotal = Registrant::approved()->whereNotNull('checked_in_at')->count();

        // Source tracking (all registrants)
        $topSources = Registrant::whereNotNull('utm_source')
            ->select('utm_source', DB::raw('COUNT(*) as total'))
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Referral code usage (all registrants)
        $referralCount = Registrant::whereNotNull('referral_code')
            ->where('referral_code', '!=', '')->count();

        // Workshop waitlist
        $workshopWaitlistTotal = DB::table('workshop_waitlist')->count();

        return view('admin.dashboard', compact(
            'total', 'pending', 'approved', 'rejected',
            'recentRegistrants', 'chartData', 'maxDaily',
            'workshopCount', 'workshopRegistrations',
            'todayCount', 'trend', 'stalePending',
            'checkedInToday', 'checkedInTotal', 'topSources',
            'referralCount', 'workshopWaitlistTotal'
        ));
    }

    /**
     * Approve a registrant, generate password, and send email.
     */
    public function approve(Request $request, Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to approve registrants.');
        }

        $plainPassword = Str::random(10);

        $registrant->update([
            'status'         => 'approved',
            'approved_by'    => auth()->id(),
            'password'       => $plainPassword,
            'plain_password' => $plainPassword,
            'qr_token'       => Registrant::generateQrToken(),
            'admin_notes'    => $request->input('admin_notes'),
            'processed_at'   => now(),
        ]);

        // Send credentials email with password
        Mail::to($registrant->email)->send(
            new RegistrantCredentials($registrant, $plainPassword)
        );

        return redirect()->route('admin.dashboard')
            ->with('success', "Registrant <strong>{$registrant->name}</strong> has been approved. <br><small class='text-gray-600'>Password: <code class='bg-gray-100 px-1.5 py-0.5 rounded text-xs'>{$plainPassword}</code> (sent via email)</small>");
    }

    /**
     * Reject a registrant and send email.
     */
    public function reject(Request $request, Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to reject registrants.');
        }

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ], [
            'admin_notes.required' => 'Rejection reason is required.',
        ]);

        $registrant->update([
            'status'       => 'rejected',
            'rejected_by'  => auth()->id(),
            'admin_notes'  => $request->input('admin_notes'),
            'processed_at' => now(),
        ]);

        // Send rejection email
        $template = EmailTemplate::rejection()->active()->first();
        Mail::to($registrant->email)->send(
            new RegistrantRejected($registrant, $template)
        );

        return redirect()->route('admin.dashboard')
            ->with('success', "Registrant <strong>{$registrant->name}</strong> has been rejected and a notification email has been sent.");
    }

    /**
     * Show a single registrant detail.
     */
    public function show(Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to the Registrants page.');
        }
        $workshops = $registrant->workshops;
        $agendaItems = $registrant->agendaItems;
        return view('admin.registrant-detail', compact('registrant', 'workshops', 'agendaItems'));
    }

    /**
     * Display a listing of registrants with filtering.
     */
    public function index(Request $request)
    {
        // Permission check — clients without registrants permission are redirected
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to the Registrants page.');
        }

        $status = $request->get('status', 'all');
        $utmSource   = $request->get('utm_source');
        $utmMedium   = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');
        $direct      = $request->get('direct');

        $query = Registrant::latest();

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'approved') {
            $query->approved();
        } elseif ($status === 'rejected') {
            $query->rejected();
        }

        // Filter by UTM parameters
        if ($utmSource) {
            $query->where('utm_source', $utmSource);
        }
        if ($utmMedium) {
            $query->where('utm_medium', $utmMedium);
        }
        if ($utmCampaign) {
            $query->where('utm_campaign', $utmCampaign);
        }

        // Filter for Direct (no UTM)
        if ($direct) {
            $query->whereNull('utm_source');
        }

        $registrants = $query->paginate(20)->withQueryString();

        // Stats — scoped to UTM filters if applied
        $statsQuery = Registrant::query();
        if ($utmSource) {
            $statsQuery->where('utm_source', $utmSource);
        }
        if ($utmMedium) {
            $statsQuery->where('utm_medium', $utmMedium);
        }
        if ($utmCampaign) {
            $statsQuery->where('utm_campaign', $utmCampaign);
        }
        if ($direct) {
            $statsQuery->whereNull('utm_source');
        }

        $total    = (clone $statsQuery)->count();
        $pending  = (clone $statsQuery)->pending()->count();
        $approved = (clone $statsQuery)->approved()->count();
        $rejected = (clone $statsQuery)->rejected()->count();

        $utmFilter = $utmSource ?: null;

        return view('admin.registrants.index', compact(
            'registrants', 'total', 'pending', 'approved', 'rejected', 'status', 'utmFilter'
        ));
    }

    /**
     * Show edit form for a registrant.
     */
    public function edit(Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->route('admin.registrants.show', $registrant)
                ->with('error', 'You do not have permission to edit registrants.');
        }

        return view('admin.registrants.edit', compact('registrant'));
    }

    /**
     * Update a registrant.
     */
    public function update(Request $request, Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to update registrants.');
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'first_name'   => ['nullable', 'string', 'max:255'],
            'last_name'    => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'company'      => ['nullable', 'string', 'max:255'],
            'industry'     => ['nullable', 'string', 'max:255'],
            'employees'    => ['nullable', 'string', 'max:50'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'admin_notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $registrant->update($validated);

        return redirect()->route('admin.registrants.show', $registrant)
            ->with('success', "Registrant <strong>{$registrant->name}</strong> has been updated.");
    }

    /**
     * Delete a registrant.
     */
    public function destroy(Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to delete registrants.');
        }

        $name = $registrant->name;
        $registrant->workshops()->detach();
        $registrant->delete();

        return redirect()->route('admin.registrants.index')
            ->with('success', "Registrant <strong>{$name}</strong> has been deleted.");
    }

    /**
     * Resend credentials email to an approved registrant.
     */
    public function resendCredentials(Registrant $registrant)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to resend credentials.');
        }

        if ($registrant->status !== 'approved' || !$registrant->plain_password) {
            return redirect()->back()
                ->with('error', 'This registrant has not been approved or has no password on record.');
        }

        Mail::to($registrant->email)->send(
            new RegistrantCredentials($registrant, $registrant->plain_password)
        );

        return redirect()->back()
            ->with('success', "Credentials have been resent to <strong>{$registrant->email}</strong>.");
    }

    /**
     * Bulk approve registrants.
     */
    public function bulkApprove(Request $request)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to bulk approve.');
        }

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['exists:registrants,id'],
        ]);

        $count = 0;
        $registrants = Registrant::whereIn('id', $request->ids)->pending()->get();

        foreach ($registrants as $registrant) {
            $plainPassword = Str::random(10);
            $registrant->update([
                'status'         => 'approved',
                'approved_by'    => auth()->id(),
                'password'       => $plainPassword,
                'plain_password' => $plainPassword,
                'qr_token'       => Registrant::generateQrToken(),
                'processed_at'   => now(),
            ]);

            Mail::to($registrant->email)->send(
                new RegistrantCredentials($registrant, $plainPassword)
            );

            $count++;
        }

        return redirect()->back()
            ->with('success', "<strong>{$count}</strong> registrant(s) have been approved and notified.");
    }

    /**
     * Bulk reject registrants.
     */
    public function bulkReject(Request $request)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to bulk reject.');
        }

        $request->validate([
            'ids'         => ['required', 'array', 'min:1'],
            'ids.*'       => ['exists:registrants,id'],
            'admin_notes' => ['required', 'string', 'max:500'],
        ]);

        $count = 0;
        $registrants = Registrant::whereIn('id', $request->ids)->pending()->get();

        foreach ($registrants as $registrant) {
            $registrant->update([
                'status'       => 'rejected',
                'rejected_by'  => auth()->id(),
                'admin_notes'  => $request->admin_notes,
                'processed_at' => now(),
            ]);

            $template = EmailTemplate::rejection()->active()->first();
            Mail::to($registrant->email)->send(
                new RegistrantRejected($registrant, $template)
            );

            $count++;
        }

        return redirect()->back()
            ->with('success', "<strong>{$count}</strong> registrant(s) have been rejected and notified.");
    }

    /**
     * Export registrants to CSV.
     */
    public function exportCsv(Request $request)
    {
        if (!auth()->user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to export registrants.');
        }

        $status = $request->get('status', 'all');

        $query = Registrant::query();

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'approved') {
            $query->approved();
        } elseif ($status === 'rejected') {
            $query->rejected();
        }

        $registrants = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="registrants-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($registrants) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'ID', 'Name', 'First Name', 'Last Name', 'Email', 'Phone',
                'Job Title', 'Company', 'Industry',
                'Employees', 'Status', 'Unique Code', 'Notes', 'Admin Notes',
                'Registered At', 'Processed At',
            ]);

            foreach ($registrants as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->name,
                    $r->first_name,
                    $r->last_name,
                    $r->email,
                    $r->phone,
                    $r->job_title,
                    $r->company,
                    $r->industry,
                    $r->employees,
                    $r->status,
                    $r->unique_code,
                    $r->notes,
                    $r->admin_notes,
                    $r->created_at?->format('Y-m-d H:i:s'),
                    $r->processed_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Toggle registration form open/close (super admin only).
     */
    public function toggleRegistration()
    {
        $current = Cache::get('registration_forced_open', false);
        $new = !$current;
        Cache::put('registration_forced_open', $new);

        $status = $new ? 'OPEN' : 'CLOSED (follows countdown)';
        return redirect()->back()
            ->with('success', "Registration form is now <strong>{$status}</strong>.");
    }
}
