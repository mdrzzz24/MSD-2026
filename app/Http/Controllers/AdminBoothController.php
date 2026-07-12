<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\BoothVisit;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBoothController extends Controller
{
    /**
     * Display a listing of booths.
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('booths')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to Booths.');
        }

        $booths = Booth::ordered()->withCount('visits')->get();
        $totalVisits = BoothVisit::count();

        return view('admin.booths.index', compact('booths', 'totalVisits'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('booths')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to create booths.');
        }

        return view('admin.booths.create');
    }

    /**
     * Store a newly created booth.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('booths')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to create booths.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order'       => ['nullable', 'integer', 'min:0'],
        ]);

        Booth::create($validated);

        return redirect()->route('admin.booths.index')
            ->with('success', "Booth <strong>{$validated['name']}</strong> created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(Booth $booth)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('booths')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to edit booths.');
        }

        return view('admin.booths.edit', compact('booth'));
    }

    /**
     * Update the specified booth.
     */
    public function update(Request $request, Booth $booth)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('booths')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to update booths.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['nullable', 'boolean'],
            'order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $booth->update($validated);

        return redirect()->route('admin.booths.index')
            ->with('success', "Booth <strong>{$booth->name}</strong> updated successfully.");
    }

    /**
     * Remove the specified booth.
     */
    public function destroy(Booth $booth)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('booths')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to delete booths.');
        }

        $name = $booth->name;
        $booth->visits()->delete();
        $booth->delete();

        return redirect()->route('admin.booths.index')
            ->with('success', "Booth <strong>{$name}</strong> deleted successfully.");
    }

    /**
     * Toggle booth active status.
     */
    public function toggle(Booth $booth)
    {
        if (!Auth::user()->canWrite() || !Auth::user()->hasPermission('booths')) {
            return redirect()->back()->with('error', 'You do not have permission to toggle booths.');
        }

        $booth->update(['is_active' => !$booth->is_active]);

        $status = $booth->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Booth <strong>{$booth->name}</strong> has been {$status}.");
    }

    // ── QR Scan ──

    /**
     * Show QR scan page for a specific booth.
     */
    public function scan(Booth $booth)
    {
        if (!Auth::user()->hasPermission('booth_visits')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to scan booth visits.');
        }

        return view('admin.booths.scan', compact('booth'));
    }

    /**
     * Process a QR scan — record a visit for a registrant at this booth.
     */
    public function scanProcess(Request $request, Booth $booth)
    {
        if (!Auth::user()->hasPermission('booth_visits')) {
            return response()->json(['error' => 'You do not have permission to scan booth visits.'], 403);
        }

        $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
        ]);

        $token = trim($request->qr_token);

        // Try to find by qr_token first, then by unique_code
        $registrant = Registrant::where('qr_token', $token)
            ->orWhere('unique_code', $token)
            ->first();

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Registrant not found.',
            ]);
        }

        if (!$registrant->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant is not approved.',
            ]);
        }

        // Check if already visited this booth
        $existingVisit = BoothVisit::where('booth_id', $booth->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if ($existingVisit) {
            return response()->json([
                'success' => true,
                'message' => "{$registrant->name} has already visited this booth.",
                'registrant' => [
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                    'company' => $registrant->company,
                    'visited_at' => $existingVisit->visited_at->format('d M Y, H:i'),
                ],
                'already_visited' => true,
            ]);
        }

        // Record the visit
        BoothVisit::create([
            'booth_id'      => $booth->id,
            'registrant_id' => $registrant->id,
            'visited_at'    => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Visit recorded for <strong>{$registrant->name}</strong>!",
            'registrant' => [
                'name'    => $registrant->name,
                'email'   => $registrant->email,
                'company' => $registrant->company,
                'job_title' => $registrant->job_title,
            ],
            'already_visited' => false,
        ]);
    }

    /**
     * Display visitors for a specific booth.
     */
    public function visitors(Booth $booth)
    {
        if (!Auth::user()->hasPermission('booth_visits')) {
            return redirect()->route('admin.booths.index')->with('error', 'You do not have permission to view booth visits.');
        }

        $visits = BoothVisit::with('registrant')
            ->where('booth_id', $booth->id)
            ->orderByDesc('visited_at')
            ->paginate(30);

        return view('admin.booths.visitors', compact('booth', 'visits'));
    }

    /**
     * Export booth visitors to CSV.
     */
    public function exportVisitorsCsv(Booth $booth)
    {
        if (!Auth::user()->hasPermission('booth_visits')) {
            return redirect()->back()->with('error', 'You do not have permission to export booth visits.');
        }

        $visits = BoothVisit::with('registrant')
            ->where('booth_id', $booth->id)
            ->orderByDesc('visited_at')
            ->get();

        $headers = ['Booth Name', 'Registrant Name', 'Email', 'Phone', 'Company', 'Job Title', 'Visited At'];
        $rows = $visits->map(fn($v) => [
            $booth->name,
            $v->registrant->display_name ?: $v->registrant->name,
            $v->registrant->email,
            $v->registrant->phone ?? '-',
            $v->registrant->company ?? '-',
            $v->registrant->job_title ?? '-',
            $v->visited_at ? $v->visited_at->format('Y-m-d H:i:s') : '-',
        ])->toArray();

        return $this->csvDownload($headers, $rows, 'booth-' . $booth->id . '-visitors-' . now()->format('YmdHis') . '.csv');
    }

    /**
     * Helper: download CSV.
     */
    private function csvDownload(array $headers, array $rows, string $filename)
    {
        return response()->stream(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
