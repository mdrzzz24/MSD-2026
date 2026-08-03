<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnsiteController extends Controller
{
    /**
     * Onsite Event — participant list for name badge printing (admin & super admin).
     * Default filter: approved participants.
     */
    public function index(Request $request)
    {
        if (Auth::user()->isClient()) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to the Onsite Event page.');
        }

        $status   = $request->get('status', 'approved'); // default: approved
        $search   = $request->get('search');
        $profile  = array_values(array_filter((array) $request->get('profile')));
        $company  = $request->get('company');
        $source   = array_values(array_filter((array) $request->get('source')));
        $checkedIn = $request->get('checked_in');
        $sort     = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $query = $this->buildQuery($request);
        $registrants = $query->paginate(25)->withQueryString();

        // Stats scoped to the current non-status filters (so cards reflect the same view)
        $base = Registrant::query();
        if ($search) {
            $term = '%' . $search . '%';
            $base->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('company', 'like', $term)
                  ->orWhere('job_title', 'like', $term)
                  ->orWhere('job_role', 'like', $term)
                  ->orWhere('unique_code', 'like', $term);
            });
        }
        if ($profile) {
            $base->whereIn('job_title', $profile);
        }
        if ($company) {
            $base->where('company', 'like', '%' . $company . '%');
        }
        $base->filterBySources($source);
        if ($checkedIn === 'yes') {
            $base->whereNotNull('checked_in_at');
        } elseif ($checkedIn === 'no') {
            $base->whereNull('checked_in_at');
        }

        $total         = (clone $base)->count();
        $approved      = (clone $base)->approved()->count();
        $pending       = (clone $base)->pending()->count();
        $rejected      = (clone $base)->rejected()->count();
        $checkedInCount = (clone $base)->whereNotNull('checked_in_at')->count();

        $profiles  = Registrant::whereNotNull('job_title')->distinct()->orderBy('job_title')->pluck('job_title');
        $sources   = Registrant::whereNotNull('utm_source')->distinct()->orderBy('utm_source')->pluck('utm_source');
        $companies = Registrant::whereNotNull('company')->distinct()->orderBy('company')->limit(200)->pluck('company');

        // MQTT badge printer info (topic + status) for display on the page.
        // Topic uses the logged-in user's id, e.g. print/admin-11.
        $mqttEnabled = config('mqtt.enabled', false);
        $mqttTopic = config('mqtt.topic_prefix', 'print') . '/admin-' . Auth::id();

        // Number of participants that "Print Badges" (bulk, no selection) would send
        $bulkCount = match ($status) {
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            default    => $total,
        };

        return view('admin.onsite.index', compact(
            'registrants', 'status', 'search', 'profile', 'company', 'source', 'checkedIn',
            'sort', 'direction',
            'total', 'approved', 'pending', 'rejected', 'checkedInCount',
            'profiles', 'sources', 'companies', 'mqttEnabled', 'mqttTopic', 'bulkCount'
        ));
    }

    /**
     * Build the registrant query for the given filters/sort (shared by index & live search).
     */
    private function buildQuery(Request $request)
    {
        $status    = $request->get('status', 'approved');
        $search    = $request->get('search');
        $profile   = array_values(array_filter((array) $request->get('profile')));
        $company   = $request->get('company');
        $source    = array_values(array_filter((array) $request->get('source')));
        $checkedIn = $request->get('checked_in');
        $sort      = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        // Whitelisted sortable columns (clicking the table header toggles sort)
        $sortable = [
            'name'       => 'name',
            'company'    => 'company',
            'utm_source' => 'utm_source',
            'status'     => 'status',
            'checked_in' => 'checked_in_at',
            'created_at' => 'created_at',
        ];

        $query = Registrant::query();
        if ($sort && isset($sortable[$sort])) {
            $dir = $direction === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortable[$sort], $dir)->orderBy('id', $dir);
        } else {
            $query->latest();
        }

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'approved') {
            $query->approved();
        } elseif ($status === 'rejected') {
            $query->rejected();
        }
        // 'all' => no status filter

        if ($search) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('company', 'like', $term)
                  ->orWhere('job_title', 'like', $term)
                  ->orWhere('job_role', 'like', $term)
                  ->orWhere('unique_code', 'like', $term);
            });
        }

        if ($profile) {
            $query->whereIn('job_title', $profile);
        }
        if ($company) {
            $query->where('company', 'like', '%' . $company . '%');
        }
        $query->filterBySources($source);

        if ($checkedIn === 'yes') {
            $query->whereNotNull('checked_in_at');
        } elseif ($checkedIn === 'no') {
            $query->whereNull('checked_in_at');
        }

        return $query;
    }

    /**
     * Live search (AJAX) — returns the table HTML without a full page reload,
     * so typing keeps focus and is never interrupted.
     */
    public function search(Request $request)
    {
        if (Auth::user()->isClient()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $query = $this->buildQuery($request);
        $registrants = $query->paginate(25)->withQueryString();

        $status    = $request->get('status', 'approved');
        $sort      = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $html = view('admin.onsite._table', compact('registrants', 'status', 'sort', 'direction'))->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
            'total'   => $registrants->total(),
        ]);
    }

    /**
     * Printable name badge sheet for selected participants (or all in the current filter).
     */
    public function printBadges(Request $request)
    {
        if (Auth::user()->isClient()) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to the Onsite Event page.');
        }

        $ids = $request->input('ids');

        if ($ids) {
            $idList = collect(explode(',', $ids))
                ->map(fn ($v) => (int) trim($v))
                ->filter(fn ($v) => $v > 0)
                ->unique()
                ->values();

            $registrants = $idList->isNotEmpty()
                ? Registrant::whereIn('id', $idList)->orderBy('name')->get()
                : collect();
        } else {
            // Fallback: print all in the current status filter (default approved)
            $status = $request->get('status', 'approved');
            $query = Registrant::query();
            if ($status === 'pending') {
                $query->pending();
            } elseif ($status === 'approved') {
                $query->approved();
            } elseif ($status === 'rejected') {
                $query->rejected();
            }
            $registrants = $query->orderBy('name')->get();
        }

        if ($registrants->isEmpty()) {
            return back()->with('error', 'No participants selected to print.');
        }

        return view('admin.onsite.badges', compact('registrants'));
    }

    /**
     * Trigger the MQTT badge printer for the selected participants.
     * Publishes one JSON message per participant to print/admin-{adminId}.
     */
    public function triggerPrint(Request $request)
    {
        if (Auth::user()->isClient()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $ids    = $request->input('ids');
        $status = $request->input('status', 'approved');

        if ($ids) {
            $idList = collect(explode(',', $ids))
                ->map(fn ($v) => (int) trim($v))
                ->filter(fn ($v) => $v > 0)
                ->unique()
                ->values();

            $registrants = $idList->isNotEmpty()
                ? Registrant::whereIn('id', $idList)->get()
                : collect();
        } else {
            $query = Registrant::query();
            if ($status === 'pending') {
                $query->pending();
            } elseif ($status === 'approved') {
                $query->approved();
            } elseif ($status === 'rejected') {
                $query->rejected();
            }
            $registrants = $query->get();
        }

        if ($registrants->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No participants selected to print.'], 422);
        }

        $service = app(\App\Services\MqttService::class);
        $printedIds = $service->publishBadges($registrants, Auth::id());

        return response()->json([
            'success'      => true,
            'total'        => $registrants->count(),
            'published'    => count($printedIds),
            'ids'          => $printedIds,
            'checked_in_at'=> now()->addHours(7)->format('H:i'),
            'enabled'      => $service->enabled(),
        ]);
    }
}
