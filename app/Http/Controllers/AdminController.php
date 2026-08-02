<?php

namespace App\Http\Controllers;

use App\Mail\RegistrantApproved;
use App\Mail\RegistrantCredentials;
use App\Mail\RegistrantRejected;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Models\User;
use App\Models\Workshop;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Gather all dashboard statistics into an array.
     */
    private function getDashboardStats(): array
    {
        $total      = Registrant::count();
        $pending    = Registrant::pending()->count();
        $approved   = Registrant::approved()->count();
        $rejected   = Registrant::rejected()->count();

        $recentRegistrants = Registrant::latest()->take(7)->get()->map(fn($r) => [
            'id'     => $r->id,
            'name'   => $r->name,
            'email'  => $r->email,
            'status' => $r->status,
            'initial' => strtoupper(substr($r->name, 0, 1)),
            'timeAgo' => $r->created_at->diffForHumans(),
        ]);

        // Daily history window: from the oldest registrant up to today (capped at 365 days)
        $oldestTimestamp = Registrant::orderBy('created_at')->value('created_at');
        $oldestDay = $oldestTimestamp
            ? \Carbon\Carbon::parse($oldestTimestamp)->startOfDay()
            : now()->startOfDay();
        $dailyTotalDays = max(1, min(365, (int) abs(now()->startOfDay()->diffInDays($oldestDay)) + 1));

        $dailyStats = Registrant::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count"),
            DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count"),
            DB::raw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count")
        )
        ->where('created_at', '>=', now()->subDays($dailyTotalDays - 1))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $chartData = [];
        $maxDaily = 0;
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $day = now()->subDays($i)->format('D');
            $stats = $dailyStats->get($date);
            $count = $stats ? (int) $stats->total : 0;
            $approvedCount = $stats ? (int) $stats->approved_count : 0;
            $pendingCount = $stats ? (int) $stats->pending_count : 0;
            $rejectedCount = $stats ? (int) $stats->rejected_count : 0;
            $chartData[] = [
                'day'      => $day,
                'date'     => $date,
                'total'    => $count,
                'approved' => $approvedCount,
                'pending'  => $pendingCount,
                'rejected' => $rejectedCount,
            ];
            if ($count > $maxDaily) $maxDaily = $count;
        }
        $chartData = array_reverse($chartData); // newest first
        $maxDaily = max($maxDaily, 1);

        // Daily breakdown for the paginated "Registrations Per Day" table
        $dailyData = [];
        $dailyMax = 0;
        for ($i = $dailyTotalDays - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $day = now()->subDays($i)->format('D');
            $stats = $dailyStats->get($date);
            $count = $stats ? (int) $stats->total : 0;
            $approvedCount = $stats ? (int) $stats->approved_count : 0;
            $pendingCount = $stats ? (int) $stats->pending_count : 0;
            $rejectedCount = $stats ? (int) $stats->rejected_count : 0;
            $dailyData[] = [
                'day'      => $day,
                'date'     => $date,
                'total'    => $count,
                'approved' => $approvedCount,
                'pending'  => $pendingCount,
                'rejected' => $rejectedCount,
            ];
            if ($count > $dailyMax) $dailyMax = $count;
        }
        $dailyData = array_reverse($dailyData); // newest first
        $dailyMax = max($dailyMax, 1);

        $workshopCount = Workshop::count();
        $workshopRegistrations = DB::table('registrant_workshop')->count();

        $todayCount = Registrant::whereDate('created_at', today())->count();
        $yesterdayCount = Registrant::whereDate('created_at', today()->subDay())->count();
        $trend = $yesterdayCount > 0 ? round(($todayCount - $yesterdayCount) / $yesterdayCount * 100) : ($todayCount > 0 ? 100 : 0);

        $stalePending = Registrant::pending()->where('created_at', '<', now()->subDays(2))->count();

        $checkedInToday = Registrant::approved()->whereDate('checked_in_at', today())->count();
        $checkedInTotal = Registrant::approved()->whereNotNull('checked_in_at')->count();

        $topSources = Registrant::whereNotNull('utm_source')
            ->select('utm_source', DB::raw('COUNT(*) as total'))
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $referralCount = Registrant::whereNotNull('referral_code')
            ->where('referral_code', '!=', '')->count();

        $workshopWaitlistTotal = DB::table('workshop_waitlist')->count();

        return compact(
            'total', 'pending', 'approved', 'rejected',
            'recentRegistrants', 'chartData', 'maxDaily',
            'dailyData', 'dailyMax', 'dailyTotalDays',
            'workshopCount', 'workshopRegistrations',
            'todayCount', 'trend', 'stalePending',
            'checkedInToday', 'checkedInTotal', 'topSources',
            'referralCount', 'workshopWaitlistTotal'
        );
    }

    /**
     * Show the admin dashboard with registrant statistics & overview.
     */
    public function dashboard(Request $request)
    {
        $data = $this->getDashboardStats();

        // Paginate the daily breakdown: show 7 most recent days per page
        $perPage = 7;
        $page = max(1, (int) $request->get('page', 1));
        $dailyItems = collect($data['dailyData']);
        $dailyPage = $dailyItems->forPage($page, $perPage)->values();

        $data['dailyPage'] = $dailyPage;
        $data['dailyPagination'] = new LengthAwarePaginator(
            $dailyPage,
            $dailyItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.dashboard', $data);
    }

    /**
     * Return dashboard stats as JSON for real-time polling.
     */
    public function dashboardData()
    {
        $data = $this->getDashboardStats();
        return response()->json($data);
    }

    /**
     * Return registrants for a specific date as JSON (for daily detail modal).
     */
    public function dailyDetail(string $date, Request $request)
    {
        $statusFilter = $request->get('status'); // 'pending', 'approved', 'rejected', or null for all

        $query = Registrant::whereDate('created_at', $date)
            ->with('clientRemarkedBy')
            ->orderBy('created_at');

        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $statusFilter);
        }

        $registrants = $query->get()->map(fn($r) => [
                'id'            => $r->id,
                'name'          => $r->name,
                'email'         => $r->email,
                'phone'         => $r->phone,
                'company'       => $r->company,
                'job_title'     => $r->job_title,
                'status'        => $r->status,
                'utm_source'    => $r->utm_source,
                'unique_code'   => $r->unique_code,
                'created_at'    => $r->created_at->format('H:i'),
                'time_ago'      => $r->created_at->diffForHumans(),
                'initial'       => strtoupper(substr($r->name, 0, 1)),
                'has_remark'    => $r->hasClientRemark(),
                'remark_action' => $r->client_remark_action,
                'remark'        => $r->client_remark,
                'remark_by'     => $r->clientRemarkedBy?->name,
                'remark_at'     => $r->client_remarked_at?->copy()->addHours(7)->format('d M Y, H:i'),
            ]);

        $stats = [
            'total'    => $registrants->count(),
            'approved' => $registrants->where('status', 'approved')->count(),
            'pending'  => $registrants->where('status', 'pending')->count(),
            'rejected' => $registrants->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'date'         => $date,
            'date_formatted' => \Carbon\Carbon::parse($date)->format('d M Y'),
            'day_name'     => \Carbon\Carbon::parse($date)->isoFormat('dddd'),
            'registrants'  => $registrants,
            'stats'        => $stats,
        ]);
    }

    /**
     * Approve a registrant, generate password, and send email.
     */
    public function approve(Request $request, Registrant $registrant)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to approve registrants.');
        }

        $plainPassword = Str::random(10);

        $registrant->update([
            'status'         => 'approved',
            'approved_by'    => Auth::id(),
            'password'       => $plainPassword,
            'plain_password' => $plainPassword,
            'qr_token'       => Registrant::generateQrToken(),
            'admin_notes'    => $request->input('admin_notes'),
            'processed_at'   => now(),
        ]);

        // Send approval email using template if available
        $template = EmailTemplate::activeOfType(EmailTemplate::TYPE_APPROVAL);
        if ($template) {
            EmailService::send($registrant, $template, ['password' => $plainPassword]);
        } else {
            // Fallback: send credentials directly via blade view + log manually
            try {
                Mail::to($registrant->email)->send(
                    new RegistrantCredentials($registrant, $plainPassword)
                );
                \App\Models\EmailLog::create([
                    'email_template_id' => null,
                    'registrant_id'     => $registrant->id,
                    'template_type'     => 'approval',
                    'recipient_email'   => $registrant->email,
                    'recipient_name'    => $registrant->display_name,
                    'subject'           => 'Akun Anda Telah Disetujui — Login Credentials',
                    'html_content'      => null, // not stored for fallback
                    'status'            => 'sent',
                    'sent_at'           => now(),
                ]);
            } catch (\Throwable $e) {
                \App\Models\EmailLog::create([
                    'email_template_id' => null,
                    'registrant_id'     => $registrant->id,
                    'template_type'     => 'approval',
                    'recipient_email'   => $registrant->email,
                    'recipient_name'    => $registrant->display_name,
                    'subject'           => 'Akun Anda Telah Disetujui — Login Credentials',
                    'html_content'      => null,
                    'status'            => 'failed',
                    'error_message'     => $e->getMessage(),
                    'sent_at'           => now(),
                ]);
            }
        }

        return back()
            ->with('success', "Registrant <strong>{$registrant->name}</strong> has been approved. <br><small class='text-gray-600'>Password: <code class='bg-gray-100 px-1.5 py-0.5 rounded text-xs'>{$plainPassword}</code> (sent via email)</small>");
    }

    /**
     * Reject a registrant and send email.
     */
    public function reject(Request $request, Registrant $registrant)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to reject registrants.');
        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $registrant->update([
            'status'       => 'rejected',
            'rejected_by'  => Auth::id(),
            'admin_notes'  => $request->input('admin_notes'),
            'processed_at' => now(),
        ]);

        // Send rejection email using template if available, otherwise fallback to blade view
        $rejTemplate = EmailTemplate::activeOfType(EmailTemplate::TYPE_REJECTION);
        if ($rejTemplate) {
            EmailService::send($registrant, $rejTemplate, [
                'admin_notes' => $request->input('admin_notes', ''),
            ]);
        } else {
            try {
                Mail::to($registrant->email)->send(
                    new \App\Mail\RegistrantRejected($registrant)
                );
                \App\Models\EmailLog::create([
                    'email_template_id' => null,
                    'registrant_id'     => $registrant->id,
                    'template_type'     => 'rejection',
                    'recipient_email'   => $registrant->email,
                    'recipient_name'    => $registrant->display_name,
                    'subject'           => 'Pendaftaran Anda Ditolak',
                    'html_content'      => null,
                    'status'            => 'sent',
                    'sent_at'           => now(),
                ]);
            } catch (\Throwable $e) {
                \App\Models\EmailLog::create([
                    'email_template_id' => null,
                    'registrant_id'     => $registrant->id,
                    'template_type'     => 'rejection',
                    'recipient_email'   => $registrant->email,
                    'recipient_name'    => $registrant->display_name,
                    'subject'           => 'Pendaftaran Anda Ditolak',
                    'html_content'      => null,
                    'status'            => 'failed',
                    'error_message'     => $e->getMessage(),
                    'sent_at'           => now(),
                ]);
            }
        }

        return back()
            ->with('success', "Registrant <strong>{$registrant->name}</strong> has been rejected and a notification email has been sent.");
    }

    /**
     * Show a single registrant detail.
     */
    public function show(Registrant $registrant)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to the Registrants page.');
        }
        $workshops = $registrant->workshops;
        $agendaItems = $registrant->agendaItems;
        $emailLogs = $registrant->emailLogs()->latest('sent_at')->get();

        // Determine which email types should have been sent based on registrant status
        $sentTypes = $emailLogs->pluck('template_type')->unique()->toArray();
        $allTypes = EmailTemplate::types();
        $expectedTypes = [];

        // Registration auto-reply should always be sent
        $expectedTypes[] = [
            'type' => 'registration',
            'label' => $allTypes['registration']['label'],
            'sent' => in_array('registration', $sentTypes),
        ];

        if ($registrant->isApproved()) {
            $expectedTypes[] = [
                'type' => 'approval',
                'label' => $allTypes['approval']['label'],
                'sent' => in_array('approval', $sentTypes),
            ];
            $expectedTypes[] = [
                'type' => 'reminder',
                'label' => $allTypes['reminder']['label'],
                'sent' => in_array('reminder', $sentTypes),
            ];
        }

        if ($registrant->status === 'rejected') {
            $expectedTypes[] = [
                'type' => 'rejection',
                'label' => $allTypes['rejection']['label'],
                'sent' => in_array('rejection', $sentTypes),
            ];
        }

        // Workshop-related emails
        $workshopApproved = $workshops->filter(fn($w) => $w->pivot?->status === 'approved');
        $workshopRejected = $workshops->filter(fn($w) => $w->pivot?->status === 'rejected');

        if ($workshopApproved->count() > 0) {
            $expectedTypes[] = [
                'type' => 'workshop_approval',
                'label' => $allTypes['workshop_approval']['label'],
                'sent' => in_array('workshop_approval', $sentTypes),
            ];
        }
        if ($workshopRejected->count() > 0) {
            $expectedTypes[] = [
                'type' => 'workshop_rejection',
                'label' => $allTypes['workshop_rejection']['label'],
                'sent' => in_array('workshop_rejection', $sentTypes),
            ];
        }

        // Track-related emails
        $trackApproved = $agendaItems->filter(fn($a) => $a->pivot?->status === 'approved');
        $trackRejected = $agendaItems->filter(fn($a) => $a->pivot?->status === 'rejected');

        if ($trackApproved->count() > 0) {
            $expectedTypes[] = [
                'type' => 'track_approval',
                'label' => $allTypes['track_approval']['label'],
                'sent' => in_array('track_approval', $sentTypes),
            ];
        }
        if ($trackRejected->count() > 0) {
            $expectedTypes[] = [
                'type' => 'track_rejection',
                'label' => $allTypes['track_rejection']['label'],
                'sent' => in_array('track_rejection', $sentTypes),
            ];
        }

        return view('admin.registrant-detail', compact(
            'registrant', 'workshops', 'agendaItems', 'emailLogs', 'expectedTypes'
        ));
    }

    /**
     * Display a listing of registrants with filtering.
     */
    public function index(Request $request)
    {
        // Permission check — clients without registrants permission are redirected
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to the Registrants page.');
        }

        $status = $request->get('status', 'all');
        $utmSource   = $request->get('utm_source');
        $utmMedium   = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');
        $direct      = $request->get('direct');
        $search      = $request->get('search');
        $profile     = $request->get('profile');
        $source      = $request->get('source');
        $dateFrom    = $request->get('date_from');
        $dateTo      = $request->get('date_to');
        $query = $this->buildRegistrantQuery($request);

        $registrants = $query->paginate(20)->withQueryString();

        // Stats — scoped to current filters
        $statsQuery = Registrant::query();
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $statsQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('company', 'like', $searchTerm)
                  ->orWhere('job_title', 'like', $searchTerm)
                  ->orWhere('job_role', 'like', $searchTerm)
                  ->orWhere('unique_code', 'like', $searchTerm);
            });
        }
        if ($profile) {
            $statsQuery->where('job_title', $profile);
        }
        if ($source === 'direct') {
            $statsQuery->whereNull('utm_source');
        } elseif ($source) {
            $statsQuery->where('utm_source', $source);
        }
        if ($dateFrom) {
            $statsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $statsQuery->whereDate('created_at', '<=', $dateTo);
        }
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

        // Dropdown options for the filter bar
        $profiles = Registrant::whereNotNull('job_title')->distinct()->orderBy('job_title')->pluck('job_title');
        $sources  = Registrant::whereNotNull('utm_source')->distinct()->orderBy('utm_source')->pluck('utm_source');

        return view('admin.registrants.index', compact(
            'registrants', 'total', 'pending', 'approved', 'rejected',
            'status', 'utmFilter', 'search', 'profiles', 'sources'
        ));
    }

    /**
     * AJAX live-search endpoint for the registrants table.
     */
    public function search(Request $request)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $query = $this->buildRegistrantQuery($request);
        $registrants = $query->paginate(20)->withQueryString();

        return response()->json([
            'success'    => true,
            'rows'       => view('admin.registrants._rows', ['registrants' => $registrants])->render(),
            'pagination' => $registrants->links()->render(),
            'total'      => $registrants->total(),
        ]);
    }

    /**
     * Build the registrant query from request filters (shared by index & search).
     */
    private function buildRegistrantQuery(Request $request)
    {
        $status = $request->get('status', 'all');
        $utmSource   = $request->get('utm_source');
        $utmMedium   = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');
        $direct      = $request->get('direct');
        $search      = $request->get('search');
        $profile     = $request->get('profile');
        $source      = $request->get('source');
        $dateFrom    = $request->get('date_from');
        $dateTo      = $request->get('date_to');

        $query = Registrant::withCount('emailLogs')->latest();

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'approved') {
            $query->approved();
        } elseif ($status === 'rejected') {
            $query->rejected();
        }

        // Search by name, email, phone, company, job title, or job role
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('company', 'like', $searchTerm)
                  ->orWhere('job_title', 'like', $searchTerm)
                  ->orWhere('job_role', 'like', $searchTerm)
                  ->orWhere('unique_code', 'like', $searchTerm);
            });
        }

        // Filter by profile (job title)
        if ($profile) {
            $query->where('job_title', $profile);
        }

        // Filter by source dropdown ('direct' = no UTM source)
        if ($source === 'direct') {
            $query->whereNull('utm_source');
        } elseif ($source) {
            $query->where('utm_source', $source);
        }

        // Filter by registration date range
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
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

        return $query;
    }

    /**
     * Show edit form for a registrant.
     */
    public function edit(Registrant $registrant)
    {
        if (!Auth::user()->hasPermission('registrants')) {
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
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to update registrants.');
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'first_name'   => ['nullable', 'string', 'max:255'],
            'last_name'    => ['nullable', 'string', 'max:255'],
            'job_title'    => ['nullable', 'string', 'max:255'],
            'job_role'     => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'company'      => ['nullable', 'string', 'max:255'],
            'industry'     => ['nullable', 'string', 'max:255'],
            'employees'    => ['nullable', 'string', 'max:50'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'admin_notes'  => ['nullable', 'string', 'max:2000'],

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
        if (!Auth::user()->hasPermission('registrants')) {
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
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to resend credentials.');
        }

        if ($registrant->status !== 'approved' || !$registrant->plain_password) {
            return redirect()->back()
                ->with('error', 'This registrant has not been approved or has no password on record.');
        }

        try {
            Mail::to($registrant->email)->send(
                new RegistrantCredentials($registrant, $registrant->plain_password)
            );
            \App\Models\EmailLog::create([
                'email_template_id' => null,
                'registrant_id'     => $registrant->id,
                'template_type'     => 'approval',
                'recipient_email'   => $registrant->email,
                'recipient_name'    => $registrant->display_name,
                'subject'           => 'Akun Anda Telah Disetujui — Login Credentials',
                'html_content'      => null,
                'status'            => 'sent',
                'sent_at'           => now(),
            ]);
        } catch (\Throwable $e) {
            \App\Models\EmailLog::create([
                'email_template_id' => null,
                'registrant_id'     => $registrant->id,
                'template_type'     => 'approval',
                'recipient_email'   => $registrant->email,
                'recipient_name'    => $registrant->display_name,
                'subject'           => 'Akun Anda Telah Disetujui — Login Credentials',
                'html_content'      => null,
                'status'            => 'failed',
                'error_message'     => $e->getMessage(),
                'sent_at'           => now(),
            ]);
            return redirect()->back()
                ->with('error', "Failed to resend credentials: {$e->getMessage()}");
        }

        return redirect()->back()
            ->with('success', "Credentials have been resent to <strong>{$registrant->email}</strong>.");
    }

    /**
     * Update admin notes for a registrant.
     */
    public function updateNotes(Request $request, Registrant $registrant)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('registrants')) {
            return response()->json(['error' => 'You do not have permission to update notes.'], 403);
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $registrant->update($validated);

        return response()->json([
            'success'   => true,
            'message'   => 'Admin notes updated.',
            'notes'     => $registrant->admin_notes,
            'updatedAt' => $registrant->updated_at->format('d M Y, H:i'),
        ]);
    }

    /**
     * Save or update a client remark (approve/reject recommendation).
     * Accessible by clients and admins/super_admins.
     */
    public function clientRemark(Request $request, Registrant $registrant)
    {
        if (Auth::user()->isClient() && !Auth::user()->hasPermission('registrants')) {
            return response()->json(['error' => 'You do not have permission.'], 403);
        }

        $validated = $request->validate([
            'client_remark'        => ['nullable', 'string', 'max:2000'],
            'client_remark_action' => ['required', 'string', 'in:approve,reject'],
        ]);

        $registrant->update([
            'client_remark'        => $validated['client_remark'],
            'client_remark_action' => $validated['client_remark_action'],
            'client_remarked_by'   => Auth::id(),
            'client_remarked_at'   => now(),
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Your recommendation has been submitted.',
            'remark'    => $registrant->client_remark,
            'action'    => $registrant->client_remark_action,
            'updatedAt' => $registrant->client_remarked_at->format('d M Y, H:i'),
        ]);
    }

    /**
     * Show registrants with client recommendations for admin/super admin to review.
     */
    public function registConfirmation(Request $request)
    {
        $statusFilter   = $request->get('status', 'all');      // all, pending, approved, rejected
        $recommendFilter = $request->get('recommend', 'all');   // all, approve, reject

        $query = Registrant::whereNotNull('client_remark_action')
            ->with('clientRemarkedBy');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if ($recommendFilter !== 'all') {
            $query->where('client_remark_action', $recommendFilter);
        }

        $registrants = $query->latest('client_remarked_at')->paginate(20)->withQueryString();

        $base = Registrant::whereNotNull('client_remark_action');
        $stats = [
            'total_approve'  => (clone $base)->where('client_remark_action', 'approve')->where('status', 'pending')->count(),
            'total_reject'   => (clone $base)->where('client_remark_action', 'reject')->where('status', 'pending')->count(),
            'total_approved' => (clone $base)->where('client_remark_action', 'approve')->where('status', 'approved')->count(),
            'total_rejected' => (clone $base)->where('client_remark_action', 'reject')->where('status', 'rejected')->count(),
        ];

        return view('admin.regist-confirmation', compact('registrants', 'stats', 'statusFilter', 'recommendFilter'));
    }

    /**
     * Export regist-confirmation (client recommendations) to CSV.
     * Respects the same status & recommend filters as the page.
     */
    public function exportRegistConfirmationCsv(Request $request)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to export registrants.');
        }

        $statusFilter    = $request->get('status', 'all');      // all, pending, approved, rejected
        $recommendFilter = $request->get('recommend', 'all');   // all, approve, reject

        $query = Registrant::whereNotNull('client_remark_action')
            ->with(['clientRemarkedBy', 'approver', 'rejecter']);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if ($recommendFilter !== 'all') {
            $query->where('client_remark_action', $recommendFilter);
        }

        $registrants = $query->latest('client_remarked_at')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="regist-confirmation-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($registrants) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'ID', 'Name', 'First Name', 'Last Name', 'Email', 'Phone',
                'Job Title', 'Job Role', 'Company', 'Organization', 'Industry', 'Employees',
                'Status', 'Unique Code', 'Notes', 'Admin Notes',
                'Registered At', 'Processed At',
                'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content',
                'Referral Code', 'Referral Source', 'Attended Before',
                'Approved By', 'Rejected By', 'Checked In At',
                'Client Recommendation', 'Client Remark', 'Remarked By', 'Remarked At',
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
                    $r->job_role,
                    $r->company,
                    $r->organization,
                    $r->industry,
                    $r->employees,
                    $r->status,
                    $r->unique_code,
                    $r->notes,
                    $r->admin_notes,
                    $r->created_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
                    $r->processed_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
                    $r->utm_source ?? '',
                    $r->utm_medium ?? '',
                    $r->utm_campaign ?? '',
                    $r->utm_content ?? '',
                    $r->referral_code ?? '',
                    $r->referral_source ?? '',
                    $r->attended_before ? 'Yes' : 'No',
                    $r->approver?->name ?? '',
                    $r->rejecter?->name ?? '',
                    $r->checked_in_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
                    $r->client_remark_action ?? '',
                    $r->client_remark ?? '',
                    $r->clientRemarkedBy?->name ?? '',
                    $r->client_remarked_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk approve registrants.
     */
    public function bulkApprove(Request $request)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to bulk approve.');
        }

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['exists:registrants,id'],
        ]);

        $count = 0;
        $registrants = Registrant::whereIn('id', $request->ids)->pending()->get();
        $template = EmailTemplate::activeOfType(EmailTemplate::TYPE_APPROVAL);

        foreach ($registrants as $registrant) {
            $plainPassword = Str::random(10);
            $registrant->update([
                'status'         => 'approved',
                'approved_by'    => Auth::id(),
                'password'       => $plainPassword,
                'plain_password' => $plainPassword,
                'qr_token'       => Registrant::generateQrToken(),
                'processed_at'   => now(),
            ]);

            if ($template) {
                EmailService::send($registrant, $template, ['password' => $plainPassword]);
            } else {
                try {
                    Mail::to($registrant->email)->send(
                        new RegistrantCredentials($registrant, $plainPassword)
                    );
                    \App\Models\EmailLog::create([
                        'email_template_id' => null,
                        'registrant_id'     => $registrant->id,
                        'template_type'     => 'approval',
                        'recipient_email'   => $registrant->email,
                        'recipient_name'    => $registrant->display_name,
                        'subject'           => 'Akun Anda Telah Disetujui — Login Credentials',
                        'html_content'      => null,
                        'status'            => 'sent',
                        'sent_at'           => now(),
                    ]);
                } catch (\Throwable $e) {
                    \App\Models\EmailLog::create([
                        'email_template_id' => null,
                        'registrant_id'     => $registrant->id,
                        'template_type'     => 'approval',
                        'recipient_email'   => $registrant->email,
                        'recipient_name'    => $registrant->display_name,
                        'subject'           => 'Akun Anda Telah Disetujui — Login Credentials',
                        'html_content'      => null,
                        'status'            => 'failed',
                        'error_message'     => $e->getMessage(),
                        'sent_at'           => now(),
                    ]);
                }
            }

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
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission to bulk reject.');
        }

        $request->validate([
            'ids'         => ['required', 'array', 'min:1'],
            'ids.*'       => ['exists:registrants,id'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $count = 0;
        $registrants = Registrant::whereIn('id', $request->ids)->pending()->get();

        $rejTemplate = EmailTemplate::activeOfType(EmailTemplate::TYPE_REJECTION);

        foreach ($registrants as $registrant) {
            $registrant->update([
                'status'       => 'rejected',
                'rejected_by'  => Auth::id(),
                'admin_notes'  => $request->admin_notes,
                'processed_at' => now(),
            ]);

            if ($rejTemplate) {
                EmailService::send($registrant, $rejTemplate, [
                    'admin_notes' => $request->admin_notes ?? '',
                ]);
            } else {
                try {
                    Mail::to($registrant->email)->send(
                        new \App\Mail\RegistrantRejected($registrant)
                    );
                    \App\Models\EmailLog::create([
                        'email_template_id' => null,
                        'registrant_id'     => $registrant->id,
                        'template_type'     => 'rejection',
                        'recipient_email'   => $registrant->email,
                        'recipient_name'    => $registrant->display_name,
                        'subject'           => 'Pendaftaran Anda Ditolak',
                        'html_content'      => null,
                        'status'            => 'sent',
                        'sent_at'           => now(),
                    ]);
                } catch (\Throwable $e) {
                    \App\Models\EmailLog::create([
                        'email_template_id' => null,
                        'registrant_id'     => $registrant->id,
                        'template_type'     => 'rejection',
                        'recipient_email'   => $registrant->email,
                        'recipient_name'    => $registrant->display_name,
                        'subject'           => 'Pendaftaran Anda Ditolak',
                        'html_content'      => null,
                        'status'            => 'failed',
                        'error_message'     => $e->getMessage(),
                        'sent_at'           => now(),
                    ]);
                }
            }

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
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to export registrants.');
        }

        $status     = $request->get('status', 'all');
        $utmSource   = $request->get('utm_source');
        $utmMedium   = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');
        $direct      = $request->get('direct');
        $profile     = $request->get('profile');
        $source      = $request->get('source');
        $dateFrom    = $request->get('date_from');
        $dateTo      = $request->get('date_to');

        $query = Registrant::query();

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'approved') {
            $query->approved();
        } elseif ($status === 'rejected') {
            $query->rejected();
        }

        if ($profile) {
            $query->where('job_title', $profile);
        }
        if ($source === 'direct') {
            $query->whereNull('utm_source');
        } elseif ($source) {
            $query->where('utm_source', $source);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($utmSource) {
            $query->where('utm_source', $utmSource);
        }
        if ($utmMedium) {
            $query->where('utm_medium', $utmMedium);
        }
        if ($utmCampaign) {
            $query->where('utm_campaign', $utmCampaign);
        }
        if ($direct) {
            $query->whereNull('utm_source');
        }

        $registrants = $query->with('clientRemarkedBy')->latest()->get();

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
                'Job Title', 'Job Role', 'Company', 'Industry',
                'Employees', 'Status', 'Unique Code', 'Notes', 'Admin Notes',
                'Registered At', 'Processed At',
                'UTM Source', 'UTM Medium', 'UTM Campaign',
                'Client Recommendation', 'Client Remark', 'Remarked By', 'Remarked At',
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
                    $r->job_role,
                    $r->company,
                    $r->industry,
                    $r->employees,
                    $r->status,
                    $r->unique_code,
                    $r->notes,
                    $r->admin_notes,
                    $r->created_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
                    $r->processed_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
                    $r->utm_source ?? '',
                    $r->utm_medium ?? '',
                    $r->utm_campaign ?? '',
                    $r->client_remark_action ?? '',
                    $r->client_remark ?? '',
                    $r->clientRemarkedBy?->name ?? '',
                    $r->client_remarked_at?->copy()->addHours(7)->format('Y-m-d H:i:s'),
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

    // ── Walk-in Registration ──

    /**
     * Show walk-in registration form.
     */
    public function walkinForm()
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }

        return view('admin.walkin');
    }

    /**
     * Process walk-in registration — auto-approved with QR code.
     */
    public function walkinStore(Request $request)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission.');
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:registrants,email'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'company'    => ['nullable', 'string', 'max:255'],
            'job_title'  => ['nullable', 'string', 'max:255'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        $plainPassword = Str::random(8);

        $registrant = Registrant::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'] ?? null,
            'company'       => $validated['company'] ?? null,
            'job_title'     => $validated['job_title'] ?? null,
            'notes'         => $validated['notes'] ?? null,
            'status'        => 'approved',
            'approved_by'   => Auth::id(),
            'password'      => $plainPassword,
            'plain_password'=> $plainPassword,
            'qr_token'      => Registrant::generateQrToken(),
            'processed_at'  => now(),
            'checked_in_at' => now(),
            'utm_source'    => 'walk-in',
        ]);

        return redirect()->route('admin.walkin.show', $registrant)
            ->with('success', "Walk-in <strong>{$registrant->name}</strong> registered successfully!");
    }

    /**
     * Show walk-in registration result with QR code.
     */
    public function walkinShow(Registrant $registrant)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }

        return view('admin.walkin-result', compact('registrant'));
    }

    /**
     * Send an email by template type to a registrant.
     * Used for re-sending emails that were not sent initially.
     */
    public function sendEmailByType(Request $request, Registrant $registrant, string $type)
    {
        if (!Auth::user()->hasPermission('registrants')) {
            return redirect()->back()->with('error', 'You do not have permission.');
        }

        $validTypes = array_keys(EmailTemplate::types());
        if (!in_array($type, $validTypes)) {
            return redirect()->back()->with('error', 'Invalid email type.');
        }

        $template = EmailTemplate::activeOfType($type);
        if (!$template) {
            return redirect()->back()->with('error', 'No active template for this type. Please create a template first.');
        }

        $extraData = [];

        // Include password for approval/registration types
        if (in_array($type, [EmailTemplate::TYPE_APPROVAL, EmailTemplate::TYPE_REGISTRATION]) && $registrant->plain_password) {
            $extraData['password'] = $registrant->plain_password;
        }

        // Include admin notes for rejection types
        if (in_array($type, [EmailTemplate::TYPE_REJECTION, EmailTemplate::TYPE_WORKSHOP_REJECTION, EmailTemplate::TYPE_TRACK_REJECTION])) {
            $extraData['admin_notes'] = $registrant->admin_notes ?? '';
        }

        // Include workshop data for workshop-related types
        if (in_array($type, [EmailTemplate::TYPE_WORKSHOP_APPROVAL, EmailTemplate::TYPE_WORKSHOP_REJECTION])) {
            $workshop = $registrant->workshops()
                ->wherePivotIn('status', ['approved', 'rejected'])
                ->first();
            if ($workshop) {
                $extraData = array_merge($extraData, $workshop->emailData());
            }
        }

        // Include track/session data for track-related types
        if (in_array($type, [EmailTemplate::TYPE_TRACK_APPROVAL, EmailTemplate::TYPE_TRACK_REJECTION])) {
            $agendaItem = $registrant->agendaItems()
                ->wherePivotIn('status', ['approved', 'rejected'])
                ->first();
            if ($agendaItem) {
                $extraData['track_name'] = $agendaItem->title;
                $extraData['workshop_room'] = $agendaItem->room ?? '';
                $extraData['workshop_date'] = $agendaItem->date?->format('l, d F Y') ?? '';
                $extraData['workshop_time'] = ($agendaItem->start_time ? date('H:i', strtotime($agendaItem->start_time)) : '') . ' – ' . ($agendaItem->end_time ? date('H:i', strtotime($agendaItem->end_time)) : '');
            }
        }

        $result = EmailService::send($registrant, $template, $extraData);

        if ($result->status === 'sent') {
            return redirect()->back()
                ->with('success', 'Email <strong>' . e(EmailTemplate::typeLabel($type)) . '</strong> has been sent to <strong>' . e($registrant->display_name) . '</strong>.');
        }

        return redirect()->back()
            ->with('error', 'Failed to send email: ' . e($result->error_message ?? 'Unknown error'));
    }
}

