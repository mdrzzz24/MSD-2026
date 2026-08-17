<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\AgendaVisit;
use App\Models\Booth;
use App\Models\BoothVisit;
use App\Models\Registrant;
use App\Models\ScanLog;
use App\Models\User;
use App\Models\Workshop;
use App\Services\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Login — validates credentials against the `users` table.
     *
     * POST /api/login  body: { email, password }
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user' => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'is_admin' => $user->is_admin,
                    'role'     => $user->role,
                    'room'     => $user->isRoomAccount()
                        ? ['id' => $user->room_id, 'name' => $user->room?->name]
                        : null,
                    'booth'    => $user->isBoothAccount()
                        ? ['id' => $user->booth_id, 'name' => $user->booth?->name]
                        : null,
                ],
            ],
        ]);
    }

    /**
     * Get all active booths.
     */
    public function booths(Request $request): JsonResponse
    {
        // Booth accounts only see their own booth; everyone else sees all.
        $apiUser = $this->resolveApiUser($request->integer('admin_id') ?: null);
        $ids     = $apiUser ? $apiUser->scopedBoothIds() : null;

        $query = Booth::active()->ordered()->withCount('visits');
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }

        $booths = $query->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'name'         => $b->name,
                'description'  => $b->description,
                'is_active'    => $b->is_active,
                'order'        => $b->order,
                'visitor_count' => $b->visits_count ?? 0,
                'created_at'   => $b->created_at,
            ]);

        return response()->json([
            'success' => true,
            'booth'   => $apiUser && $apiUser->isBoothAccount()
                ? ['id' => $apiUser->booth_id, 'name' => $apiUser->booth?->name]
                : null,
            'booth_scope' => $this->boothScopeMeta($apiUser),
            'data'    => $booths,
        ]);
    }

    /**
     * Get all agenda items.
     */
    public function agenda(Request $request): JsonResponse
    {
        // Room accounts only see the sessions assigned to them; an account with
        // no assignments sees the full agenda (default).
        $apiUser = $this->resolveApiUser($request->integer('admin_id') ?: null);
        $scope   = $apiUser ? $apiUser->scopedAgendaItemIds() : null;

        // Time slots are needed for rowspan-aware display end times — e.g. the
        // Confluent workshop spans 3 slots (13:00–14:30) but its raw end_time is
        // the first slot's end (13:30).
        $timeSlots = \App\Models\TimeSlot::ordered()->get();

        $query = AgendaItem::ordered()->with(['speakers', 'workshop', 'track']);
        if ($scope !== null) {
            $query->whereIn('id', $scope);
        }

        $items = $query->get()
            ->map(fn($item) => [
                'id'             => $item->id,
                'title'          => $item->title,
                'topic_headline' => $item->topic_headline,
                'description'    => $item->description,
                'agenda_type'    => $item->agenda_type ?? ($item->workshop_id ? 'workshop' : ($item->track_id ? 'track' : 'session')),
                'workshop_id'    => $item->workshop_id,
                'track_id'       => $item->track_id,
                // Vendor / company behind the session — consistent across workshop / track / general keynote.
                'company'        => $item->workshop
                    ? ($item->workshop->name ?: $item->workshop->title)
                    : ($item->track ? ($item->track->name ?: $item->track->title)
                    : (($item->category === 'general' && $item->speakers->isNotEmpty()) ? $item->title : null)),
                'workshop_name'  => $item->workshop ? ($item->workshop->name ?: $item->workshop->title) : null,
                'track_name'     => $item->track ? ($item->track->name ?: $item->track->title) : null,
                'room'           => $item->room,
                'date'           => $item->date?->format('Y-m-d'),
                'start_time'     => $item->start_time,
                'end_time'       => $item->displayEndTime($timeSlots),
                'capacity'       => $item->capacity,
                'is_registrable' => $item->is_registrable,
                'feedback_enabled' => $item->feedback_enabled,
                'speakers'       => $item->speakers->map(fn($s) => [
                    'id'    => $s->id,
                    'name'  => $s->name,
                    'title' => $s->title,
                    'photo' => $s->photo ? asset('storage/' . $s->photo) : null,
                ]),
            ]);

        // The room this account is bound to (null for non-room accounts).
        $room = $apiUser && $apiUser->isRoomAccount()
            ? ['id' => $apiUser->room_id, 'name' => $apiUser->room?->name]
            : null;

        // Distinct rooms among the sessions this account can manage/track —
        // readable directly from /agenda without a separate /config call.
        $rooms = $items->pluck('room')->filter()->unique()->values();

        return response()->json([
            'success' => true,
            'room'    => $room,
            'rooms'   => $rooms,
            'scope'   => $this->scopeMeta($apiUser),
            'data'    => $items,
        ]);
    }

    /**
     * Scan a registrant's QR at a booth.
     */
    public function boothScan(Request $request, Booth $booth): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
            'admin_id' => ['nullable', 'integer'],
        ]);

        // Booth accounts may only scan their own booth.
        $user = $this->resolveApiUser($validated['admin_id'] ?? null);
        if (!$this->boothAllowed($user, $booth)) {
            return response()->json([
                'success'     => false,
                'message'     => 'This booth is not assigned to your account.',
                'booth_scope' => 'forbidden',
            ], 403);
        }

        $token = trim($validated['qr_token']);
        $registrant = Registrant::where('qr_token', $token)
            ->orWhere('unique_code', $token)
            ->first();

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Registrant not found.',
            ], 404);
        }

        if (!$registrant->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant is not approved.',
            ], 403);
        }

        $existing = BoothVisit::where('booth_id', $booth->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success'        => true,
                'message'        => 'Already visited this booth.',
                'already_visited' => true,
                'data' => [
                    'registrant' => [
                        'id'    => $registrant->id,
                        'name'  => $registrant->name,
                        'email' => $registrant->email,
                    ],
                    'visited_at' => $existing->visited_at,
                ],
            ]);
        }

        BoothVisit::create([
            'booth_id'      => $booth->id,
            'registrant_id' => $registrant->id,
            'visited_at'    => now(),
        ]);

        $this->logScan([
            'action'          => 'booth_scan',
            'registrant_id'   => $registrant->id,
            'registrant_name' => $registrant->name,
            'qr_token'        => $token,
            'item_id'         => $booth->id,
            'item_type'       => 'booth',
            'item_label'      => $booth->name,
            'success'         => true,
            'message'         => 'Visit recorded.',
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Visit recorded.',
            'already_visited' => false,
            'data' => [
                'registrant' => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                    'phone' => $registrant->phone,
                    'company' => $registrant->company,
                    'job_title' => $registrant->job_title,
                ],
                'visited_at' => now(),
            ],
        ]);
    }

    /**
     * Scan a registrant's QR at an agenda session.
     */
    public function agendaScan(Request $request, AgendaItem $agendum): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
            'admin_id' => ['nullable', 'integer'],
        ]);

        // Room accounts may only check in to sessions assigned to them.
        $user = $this->resolveApiUser($validated['admin_id'] ?? null);
        if (!$this->agendaItemAllowed($user, $agendum)) {
            return response()->json([
                'success' => false,
                'message' => 'This session is not assigned to your account.',
                'scope'   => 'forbidden',
            ], 403);
        }

        $token = trim($validated['qr_token']);
        $registrant = Registrant::where('qr_token', $token)
            ->orWhere('unique_code', $token)
            ->first();

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Registrant not found.',
            ], 404);
        }

        if (!$registrant->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant is not approved.',
            ], 403);
        }

        // Workshop agenda items require the registrant to already be registered
        // AND approved for that workshop. Otherwise notify them and do NOT
        // record them as present. Track/general sessions are walk-in (no gate).
        $registration = 'not_applicable';
        if ($agendum->workshop_id) {
            $registration = $this->checkWorkshopRegistration($registrant, $agendum);
            if ($registration !== 'approved') {
                return response()->json([
                    'success'      => false,
                    'message'      => $registration === 'not_registered'
                        ? 'Registrant is not registered for this workshop.'
                        : 'Registrant is not yet approved for this workshop.',
                    'registration' => $registration,
                    'data'         => [
                        'registrant' => [
                            'id'        => $registrant->id,
                            'name'      => $registrant->name,
                            'email'     => $registrant->email,
                            'company'   => $registrant->company,
                            'job_title' => $registrant->job_title,
                        ],
                    ],
                ], 403);
            }
        }

        $existing = AgendaVisit::where('agenda_item_id', $agendum->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success'        => true,
                'message'        => 'Already checked in to this session.',
                'already_visited' => true,
                'registration'   => $registration,
                'data' => [
                    'registrant' => [
                        'id'    => $registrant->id,
                        'name'  => $registrant->name,
                        'email' => $registrant->email,
                    ],
                    'visited_at' => $existing->visited_at,
                ],
            ]);
        }

        // Pakai waktu scan dari device (scanned_at) bila dikirim app, agar
        // konsisten dengan track-out; fallback ke waktu server.
        $visitedAt = $request->input('scanned_at') ?: now();

        AgendaVisit::create([
            'agenda_item_id' => $agendum->id,
            'registrant_id'  => $registrant->id,
            'visited_at'     => $visitedAt,
        ]);

        $this->logScan([
            'action'          => 'agenda_scan',
            'registrant_id'   => $registrant->id,
            'registrant_name' => $registrant->name,
            'qr_token'        => $token,
            'item_id'         => $agendum->id,
            'item_type'       => 'agenda',
            'item_label'      => $agendum->title,
            'success'         => true,
            'message'         => 'Check-in recorded.',
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Check-in recorded.',
            'already_visited' => false,
            'registration'   => $registration,
            'data' => [
                'registrant' => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                    'phone' => $registrant->phone,
                    'company' => $registrant->company,
                    'job_title' => $registrant->job_title,
                ],
                'visited_at' => $visitedAt,
            ],
        ]);
    }

    /**
     * Track a registrant OUT of an agenda session.
     *
     * POST /api/agenda/{agendum}/trackout  body: { qr_token }
     *   - registrant not found → 404
     *   - registrant not approved → 403
     *   - no prior check-in for this session → 409 (must check in first)
     *   - already tracked out → 200 with already_tracked_out = true
     *   - otherwise → sets left_at and returns 200
     */
    public function agendaTrackOut(Request $request, AgendaItem $agendum): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
            'admin_id' => ['nullable', 'integer'],
        ]);

        // Room accounts may only track out of sessions assigned to them.
        $user = $this->resolveApiUser($validated['admin_id'] ?? null);
        if (!$this->agendaItemAllowed($user, $agendum)) {
            return response()->json([
                'success' => false,
                'message' => 'This session is not assigned to your account.',
                'scope'   => 'forbidden',
            ], 403);
        }

        $token = trim($validated['qr_token']);
        $registrant = Registrant::where('qr_token', $token)
            ->orWhere('unique_code', $token)
            ->first();

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Registrant not found.',
            ], 404);
        }

        if (!$registrant->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant is not approved.',
            ], 403);
        }

        $visit = AgendaVisit::where('agenda_item_id', $agendum->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if (!$visit) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant has not checked in to this session yet.',
            ], 409);
        }

        if ($visit->left_at) {
            return response()->json([
                'success'            => true,
                'message'            => 'Registrant has already been tracked out of this session.',
                'already_tracked_out'=> true,
                'data' => [
                    'registrant' => [
                        'id'    => $registrant->id,
                        'name'  => $registrant->name,
                        'email' => $registrant->email,
                    ],
                    'visited_at' => $visit->visited_at,
                    'left_at'    => $visit->left_at,
                ],
            ]);
        }

        // Pakai waktu scan dari device (scanned_at) bila dikirim app, agar
        // konsisten dengan track-in; fallback ke waktu server.
        $visit->update(['left_at' => $request->input('scanned_at') ?: now()]);

        $this->logScan([
            'action'          => 'agenda_trackout',
            'registrant_id'   => $registrant->id,
            'registrant_name' => $registrant->name,
            'qr_token'        => $token,
            'item_id'         => $agendum->id,
            'item_type'       => 'agenda',
            'item_label'      => $agendum->title,
            'success'         => true,
            'message'         => 'Track-out recorded.',
        ]);

        return response()->json([
            'success'            => true,
            'message'            => 'Track-out recorded.',
            'already_tracked_out'=> false,
            'data' => [
                'registrant' => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                    'phone' => $registrant->phone,
                    'company' => $registrant->company,
                    'job_title' => $registrant->job_title,
                ],
                'visited_at' => $visit->visited_at,
                'left_at'    => $visit->fresh()->left_at,
            ],
        ]);
    }

    /**
     * Check whether the registrant is registered (and approved) for the
     * workshop behind this agenda item.
     *
     * Returns:
     *   - 'approved'        → registered & approved (allowed to check in)
     *   - 'pending'         → registered but awaiting approval
     *   - 'not_registered'  → no registration row for this workshop
     */
    private function checkWorkshopRegistration(Registrant $registrant, AgendaItem $agendum): string
    {
        $pivot = $registrant->workshops()
            ->where('workshops.id', $agendum->workshop_id)
            ->first();

        if (!$pivot) {
            return 'not_registered';
        }

        return $pivot->pivot->status === 'approved' ? 'approved' : 'pending';
    }

    /**
     * Registration check-in scan — mirrors the Onsite Event flow: triggers the
     * MQTT badge printer (publish/admin-{admin_id}) and marks the registrant
     * as checked in on arrival.
     *
     * POST /api/registration/scan  body: { qr_token, admin_id? }
     *   - admin_id (optional): which admin's printer to trigger. Defaults to the
     *     first super admin when omitted.
     */
    public function registrationScan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
            'admin_id' => ['nullable', 'integer'],
        ]);

        $token = trim($validated['qr_token']);
        $registrant = Registrant::where('qr_token', $token)
            ->orWhere('unique_code', $token)
            ->first();

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Registrant not found.',
            ], 404);
        }

        if (!$registrant->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant is not approved.',
            ], 403);
        }

        $alreadyCheckedIn = $registrant->checked_in_at !== null;

        // Mirror the Onsite Event flow: publish the badge to the printer via MQTT.
        // The printer follows the logged-in user: admin_id = the user id from
        // /api/login, so the badge goes to print/admin-{their id}.
        $service = app(MqttService::class);
        $adminId = $this->resolvePrinterAdminId($validated['admin_id'] ?? null);
        $printedIds = $service->publishBadges(collect([$registrant]), $adminId);

        // A registration arrival always counts as checked in (badge print is optional).
        if ($registrant->checked_in_at === null) {
            $registrant->update(['checked_in_at' => now()]);
        }

        $this->logScan([
            'action'          => 'registration_scan',
            'registrant_id'   => $registrant->id,
            'registrant_name' => $registrant->name,
            'qr_token'        => $token,
            'item_type'       => 'registration',
            'item_label'      => 'Registration / Onsite',
            'admin_id'        => $adminId,
            'success'         => true,
            'printed'         => count($printedIds) > 0,
            'message'         => $alreadyCheckedIn
                ? 'Already checked in. Badge print triggered.'
                : 'Check-in recorded. Badge print triggered.',
        ]);

        return response()->json([
            'success'            => true,
            'message'            => $alreadyCheckedIn
                ? 'Already checked in. Badge print triggered.'
                : 'Check-in recorded. Badge print triggered.',
            'already_checked_in' => $alreadyCheckedIn,
            'printed'            => count($printedIds) > 0,
            'mqtt_enabled'       => $service->enabled(),
            'data'               => [
                'registrant' => [
                    'id'       => $registrant->id,
                    'name'     => $registrant->name,
                    'email'    => $registrant->email,
                    'phone'    => $registrant->phone,
                    'company'  => $registrant->company,
                    'job_title'=> $registrant->job_title,
                ],
                'checked_in_at' => $registrant->fresh()->checked_in_at,
            ],
        ]);
    }

    /**
     * Register a registrant to a workshop via API — immediately approved.
     *
     * POST /api/workshops/{workshop}/register
     *   body: { "registrant_id": 123 }  OR  { "qr_token": "..." }
     *
     * Behavior:
     *   - registrant not found → 404
     *   - registrant not approved → 403
     *   - not registered → pivot created with status approved
     *   - registered but pending/rejected → set to approved
     *   - already approved → info response (success true, already_registered)
     */
    public function workshopRegister(Request $request, Workshop $workshop): JsonResponse
    {
        $validated = $request->validate([
            'registrant_id' => ['required_without:qr_token', 'integer'],
            'qr_token'      => ['required_without:registrant_id', 'string', 'max:255'],
        ]);

        if (!empty($validated['registrant_id'])) {
            $registrant = Registrant::find($validated['registrant_id']);
        } else {
            $token = trim($validated['qr_token']);
            $registrant = Registrant::where('qr_token', $token)
                ->orWhere('unique_code', $token)
                ->first();
        }

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant not found.',
            ], 404);
        }

        if (!$registrant->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant is not approved.',
            ], 403);
        }

        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();

        // Already registered & approved — idempotent info response
        if ($existing && $existing->pivot->status === 'approved') {
            return response()->json([
                'success'            => true,
                'message'            => 'Registrant is already registered & approved for this workshop.',
                'already_registered' => true,
                'data' => [
                    'workshop' => [
                        'id'   => $workshop->id,
                        'name' => $workshop->name ?: $workshop->title,
                    ],
                    'registrant' => [
                        'id'    => $registrant->id,
                        'name'  => $registrant->name,
                        'email' => $registrant->email,
                    ],
                    'status' => 'approved',
                ],
            ]);
        }

        // Create or approve the workshop registration
        if ($existing) {
            $registrant->workshops()->updateExistingPivot($workshop->id, [
                'status'       => 'approved',
                'admin_notes'  => null,
                'processed_by' => null,
                'processed_at' => now(),
            ]);
        } else {
            $registrant->workshops()->attach($workshop->id, $registrant->utmForPivot() + [
                'status'       => 'approved',
                'processed_by' => null,
                'processed_at' => now(),
            ]);
        }

        // Sync to linked agenda items (mirror the admin approve flow)
        $workshop->load('agendaItems');
        foreach ($workshop->agendaItems as $item) {
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'approved', 'processed_by' => null, 'processed_at' => now(),
                ]);
            } else {
                $registrant->agendaItems()->attach($item->id, [
                    'status' => 'approved', 'processed_by' => null, 'processed_at' => now(),
                ]);
            }
        }

        $this->logScan([
            'action'          => 'workshop_register',
            'registrant_id'   => $registrant->id,
            'registrant_name' => $registrant->name,
            'qr_token'        => $validated['qr_token'] ?? null,
            'item_id'         => $workshop->id,
            'item_type'       => 'workshop',
            'item_label'      => $workshop->name ?: $workshop->title,
            'success'         => true,
            'message'         => 'Registrant registered & approved for this workshop.',
        ]);

        return response()->json([
            'success'            => true,
            'message'            => 'Registrant registered & approved for this workshop.',
            'already_registered' => false,
            'data' => [
                'workshop' => [
                    'id'   => $workshop->id,
                    'name' => $workshop->name ?: $workshop->title,
                ],
                'registrant' => [
                    'id'       => $registrant->id,
                    'name'     => $registrant->name,
                    'email'    => $registrant->email,
                    'phone'    => $registrant->phone,
                    'company'  => $registrant->company,
                    'job_title'=> $registrant->job_title,
                ],
                'status' => 'approved',
            ],
        ]);
    }

    /**
     * List registrants who have visited a booth (attendees).
     *
     * GET /api/booths/{booth}/attendees
     */
    public function boothAttendees(Request $request, Booth $booth): JsonResponse
    {
        // Booth accounts may only view attendees of their own booth.
        $user = $this->resolveApiUser($request->integer('admin_id') ?: null);
        if (!$this->boothAllowed($user, $booth)) {
            return response()->json([
                'success'     => false,
                'message'     => 'This booth is not assigned to your account.',
                'booth_scope' => 'forbidden',
            ], 403);
        }

        $visits = BoothVisit::where('booth_id', $booth->id)
            ->with('registrant')
            ->orderByDesc('visited_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'booth' => [
                    'id'   => $booth->id,
                    'name' => $booth->name,
                ],
                'total'     => $visits->count(),
                'attendees' => $visits->map(fn($v) => [
                    'id'         => $v->registrant?->id,
                    'name'       => $v->registrant?->name,
                    'email'      => $v->registrant?->email,
                    'company'    => $v->registrant?->company,
                    'job_title'  => $v->registrant?->job_title,
                    'phone'      => $v->registrant?->phone,
                    'visited_at' => $v->visited_at,
                ]),
            ],
        ]);
    }

    /**
     * List registrants who have checked in to an agenda session / workshop.
     *
     * GET /api/agenda/{agendum}/attendees
     */
    public function agendaAttendees(Request $request, AgendaItem $agendum): JsonResponse
    {
        // Room accounts may only view attendees of sessions assigned to them.
        $user = $this->resolveApiUser($request->integer('admin_id') ?: null);
        if (!$this->agendaItemAllowed($user, $agendum)) {
            return response()->json([
                'success' => false,
                'message' => 'This session is not assigned to your account.',
                'scope'   => 'forbidden',
            ], 403);
        }

        $visits = AgendaVisit::where('agenda_item_id', $agendum->id)
            ->with('registrant')
            ->orderByDesc('visited_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'agenda_item' => [
                    'id'     => $agendum->id,
                    'title'  => $agendum->title,
                    'type'   => $agendum->agenda_type ?? ($agendum->workshop_id ? 'workshop' : ($agendum->track_id ? 'track' : 'session')),
                    'room'   => $agendum->room,
                ],
                'total'     => $visits->count(),
                'attendees' => $visits->map(fn($v) => [
                    'id'         => $v->registrant?->id,
                    'name'       => $v->registrant?->name,
                    'email'      => $v->registrant?->email,
                    'company'    => $v->registrant?->company,
                    'job_title'  => $v->registrant?->job_title,
                    'phone'      => $v->registrant?->phone,
                    'visited_at' => $v->visited_at,
                    'left_at'    => $v->left_at,
                ]),
            ],
        ]);
    }

    /**
     * Bulk sync of offline scans — the app queues scans made while offline and
     * uploads them here once connectivity returns.
     *
     * POST /api/sync/scans
     * body: { "scans": [ { "client_id": "...", "action": "...", "qr_token": "...", "item_id": 123, "scanned_at": "2026-08-09T10:00:00+07:00" } ] }
     *
     * action values:
     *   registration_scan → check-in registrant on arrival (sets checked_in_at)
     *   agenda_scan       → check-in at an agenda/workshop item (item_id = agenda item id)
     *   agenda_trackout   → track-out (left_at) at an agenda/workshop item (item_id = agenda item id)
     *   booth_scan        → check-in at a booth (item_id = booth id)
     *   workshop_register → register & approve registrant to a workshop (item_id = workshop id)
     *
     * Idempotent: re-uploading the same scan returns already_* = true and does
     * not duplicate visits/registrations. Uses scanned_at (if provided) as the
     * visit/check-in timestamp so offline times are preserved.
     */
    public function syncScans(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scans'             => ['required', 'array', 'max:500'],
            'scans.*.client_id' => ['required', 'string', 'max:255'],
            'scans.*.action'    => ['required', 'string', 'in:registration_scan,agenda_scan,agenda_trackout,booth_scan,workshop_register'],
            'scans.*.qr_token'  => ['required', 'string', 'max:255'],
            'scans.*.item_id'   => ['nullable', 'integer'],
            'scans.*.scanned_at'=> ['nullable', 'date'],
            'admin_id'          => ['nullable', 'integer'],
        ]);

        $user    = $this->resolveApiUser($validated['admin_id'] ?? null);
        $results = [];
        foreach ($validated['scans'] as $scan) {
            $results[] = $this->processSyncScan($scan, $user);
        }

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * Dispatch a single queued scan to its handler.
     */
    private function processSyncScan(array $scan, ?User $user): array
    {
        $action = $scan['action'];

        $result = match ($action) {
            'registration_scan' => $this->syncRegistration($scan),
            'agenda_scan'       => $this->syncAgenda($scan, $user),
            'agenda_trackout'   => $this->syncAgendaTrackOut($scan, $user),
            'booth_scan'        => $this->syncBooth($scan),
            'workshop_register' => $this->syncWorkshop($scan),
            default             => [
                'client_id' => $scan['client_id'],
                'action'    => $action,
                'success'   => false,
                'message'   => 'Unknown action.',
            ],
        };

        // Persist an audit log for each queued scan (feeds the activity feed).
        $this->logSyncScan($scan, $result);

        return $result;
    }

    /**
     * Persist a scan/print activity log (audit trail + activity feed).
     * Never throws — logging must never break a scan.
     */
    private function logScan(array $data): void
    {
        try {
            ScanLog::create(array_merge([
                'source'  => 'mobile',
                'success' => true,
                'printed' => false,
            ], $data));
        } catch (\Throwable $e) {
            Log::warning('ScanLog write failed: ' . $e->getMessage());
        }
    }

    /**
     * Log an offline queued scan (batch sync) from its result.
     */
    private function logSyncScan(array $scan, array $result): void
    {
        $registrant = $result['registrant'] ?? null;

        $this->logScan([
            'action'          => $scan['action'] ?? ($result['action'] ?? 'scan'),
            'registrant_id'   => is_array($registrant) ? ($registrant['id'] ?? null) : null,
            'registrant_name' => is_array($registrant) ? ($registrant['name'] ?? null) : null,
            'qr_token'        => $scan['qr_token'] ?? null,
            'item_id'         => $scan['item_id'] ?? null,
            'item_type'       => match ($scan['action'] ?? null) {
                'agenda_scan', 'agenda_trackout' => 'agenda',
                'booth_scan'                     => 'booth',
                'workshop_register'              => 'workshop',
                default                          => 'registration',
            },
            'source'    => 'sync',
            'client_id' => $scan['client_id'] ?? null,
            'success'   => (bool) ($result['success'] ?? false),
            'printed'   => false,
            'message'   => $result['message'] ?? null,
        ]);
    }

    /**
     * Resolve the registrant for a queued scan (shared by all sync handlers).
     */
    private function resolveSyncRegistrant(array $scan): array
    {
        $registrant = Registrant::where('qr_token', trim($scan['qr_token']))
            ->orWhere('unique_code', trim($scan['qr_token']))
            ->first();

        if (!$registrant) {
            return ['error' => 'Invalid QR code. Registrant not found.', 'code' => 404];
        }

        if (!$registrant->isApproved()) {
            return ['error' => 'Registrant is not approved.', 'code' => 403];
        }

        return ['registrant' => $registrant];
    }

    /**
     * Offline registration scan → check-in on arrival (no MQTT badge print here).
     */
    private function syncRegistration(array $scan): array
    {
        $resolved = $this->resolveSyncRegistrant($scan);
        if (isset($resolved['error'])) {
            return array_merge([
                'client_id' => $scan['client_id'],
                'action'    => 'registration_scan',
                'success'   => false,
            ], $resolved);
        }
        /** @var Registrant $registrant */
        $registrant = $resolved['registrant'];

        $scannedAt = !empty($scan['scanned_at']) ? $scan['scanned_at'] : now();
        $alreadyCheckedIn = $registrant->checked_in_at !== null;
        if (!$alreadyCheckedIn) {
            $registrant->update(['checked_in_at' => $scannedAt]);
        }

        return [
            'client_id'          => $scan['client_id'],
            'action'             => 'registration_scan',
            'success'            => true,
            'message'            => $alreadyCheckedIn ? 'Already checked in.' : 'Check-in recorded.',
            'already_checked_in' => $alreadyCheckedIn,
            'registrant'         => [
                'id'       => $registrant->id,
                'name'     => $registrant->name,
                'email'    => $registrant->email,
                'company'  => $registrant->company,
                'job_title'=> $registrant->job_title,
            ],
            'checked_in_at' => $registrant->fresh()->checked_in_at,
        ];
    }

    /**
     * Offline agenda/workshop scan → check-in, honoring the workshop gate.
     */
    private function syncAgenda(array $scan, ?User $user): array
    {
        // Room accounts may only check in to sessions assigned to them.
        $scopeItem = isset($scan['item_id']) ? AgendaItem::find($scan['item_id']) : null;
        if ($scopeItem && !$this->agendaItemAllowed($user, $scopeItem)) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_scan',
                'success'   => false,
                'message'   => 'This session is not assigned to your account.',
                'scope'     => 'forbidden',
            ];
        }

        $resolved = $this->resolveSyncRegistrant($scan);
        if (isset($resolved['error'])) {
            return array_merge([
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_scan',
                'success'   => false,
            ], $resolved);
        }
        /** @var Registrant $registrant */
        $registrant = $resolved['registrant'];

        $agendum = isset($scan['item_id']) ? AgendaItem::find($scan['item_id']) : null;
        if (!$agendum) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_scan',
                'success'   => false,
                'message'   => 'Agenda item not found.',
            ];
        }

        // Workshop gate: only approved workshop registrations may check in.
        if ($agendum->workshop_id) {
            $registration = $this->checkWorkshopRegistration($registrant, $agendum);
            if ($registration !== 'approved') {
                return [
                    'client_id'    => $scan['client_id'],
                    'action'       => 'agenda_scan',
                    'success'      => false,
                    'message'      => $registration === 'not_registered'
                        ? 'Registrant is not registered for this workshop.'
                        : 'Registrant is not yet approved for this workshop.',
                    'registration' => $registration,
                ];
            }
        }

        $existing = AgendaVisit::where('agenda_item_id', $agendum->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if ($existing) {
            return [
                'client_id'      => $scan['client_id'],
                'action'         => 'agenda_scan',
                'success'        => true,
                'message'        => 'Already checked in to this session.',
                'already_visited'=> true,
                'registration'   => $agendum->workshop_id ? 'approved' : 'not_applicable',
                'registrant'     => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                ],
                'visited_at' => $existing->visited_at,
            ];
        }

        $visitedAt = !empty($scan['scanned_at']) ? $scan['scanned_at'] : now();
        AgendaVisit::create([
            'agenda_item_id' => $agendum->id,
            'registrant_id'  => $registrant->id,
            'visited_at'     => $visitedAt,
        ]);

        return [
            'client_id'      => $scan['client_id'],
            'action'         => 'agenda_scan',
            'success'        => true,
            'message'        => 'Check-in recorded.',
            'already_visited'=> false,
            'registration'   => $agendum->workshop_id ? 'approved' : 'not_applicable',
            'registrant'     => [
                'id'       => $registrant->id,
                'name'     => $registrant->name,
                'email'    => $registrant->email,
                'company'  => $registrant->company,
                'job_title'=> $registrant->job_title,
            ],
            'visited_at' => $visitedAt,
        ];
    }

    /**
     * Offline agenda track-out → set left_at on the existing check-in.
     */
    private function syncAgendaTrackOut(array $scan, ?User $user): array
    {
        // Room accounts may only track out of sessions assigned to them.
        $scopeItem = isset($scan['item_id']) ? AgendaItem::find($scan['item_id']) : null;
        if ($scopeItem && !$this->agendaItemAllowed($user, $scopeItem)) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_trackout',
                'success'   => false,
                'message'   => 'This session is not assigned to your account.',
                'scope'     => 'forbidden',
            ];
        }

        $resolved = $this->resolveSyncRegistrant($scan);
        if (isset($resolved['error'])) {
            return array_merge([
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_trackout',
                'success'   => false,
            ], $resolved);
        }
        /** @var Registrant $registrant */
        $registrant = $resolved['registrant'];

        $agendum = isset($scan['item_id']) ? AgendaItem::find($scan['item_id']) : null;
        if (!$agendum) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_trackout',
                'success'   => false,
                'message'   => 'Agenda item not found.',
            ];
        }

        $visit = AgendaVisit::where('agenda_item_id', $agendum->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if (!$visit) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'agenda_trackout',
                'success'   => false,
                'message'   => 'Registrant has not checked in to this session yet.',
                'status'    => 'not_checked_in',
            ];
        }

        if ($visit->left_at) {
            return [
                'client_id'          => $scan['client_id'],
                'action'             => 'agenda_trackout',
                'success'            => true,
                'message'            => 'Already tracked out of this session.',
                'already_tracked_out'=> true,
                'registrant'         => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                ],
                'visited_at' => $visit->visited_at,
                'left_at'    => $visit->left_at,
            ];
        }

        $leftAt = !empty($scan['scanned_at']) ? $scan['scanned_at'] : now();
        $visit->update(['left_at' => $leftAt]);

        return [
            'client_id'          => $scan['client_id'],
            'action'             => 'agenda_trackout',
            'success'            => true,
            'message'            => 'Track-out recorded.',
            'already_tracked_out'=> false,
            'registrant'         => [
                'id'       => $registrant->id,
                'name'     => $registrant->name,
                'email'    => $registrant->email,
                'company'  => $registrant->company,
                'job_title'=> $registrant->job_title,
            ],
            'visited_at' => $visit->visited_at,
            'left_at'    => $visit->fresh()->left_at,
        ];
    }

    /**
     * Offline booth scan → record visit.
     */
    private function syncBooth(array $scan): array
    {
        $resolved = $this->resolveSyncRegistrant($scan);
        if (isset($resolved['error'])) {
            return array_merge([
                'client_id' => $scan['client_id'],
                'action'    => 'booth_scan',
                'success'   => false,
            ], $resolved);
        }
        /** @var Registrant $registrant */
        $registrant = $resolved['registrant'];

        $booth = isset($scan['item_id']) ? Booth::find($scan['item_id']) : null;
        if (!$booth) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'booth_scan',
                'success'   => false,
                'message'   => 'Booth not found.',
            ];
        }

        $existing = BoothVisit::where('booth_id', $booth->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if ($existing) {
            return [
                'client_id'      => $scan['client_id'],
                'action'         => 'booth_scan',
                'success'        => true,
                'message'        => 'Already visited this booth.',
                'already_visited'=> true,
                'registrant'     => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                ],
                'visited_at' => $existing->visited_at,
            ];
        }

        $visitedAt = !empty($scan['scanned_at']) ? $scan['scanned_at'] : now();
        BoothVisit::create([
            'booth_id'      => $booth->id,
            'registrant_id' => $registrant->id,
            'visited_at'    => $visitedAt,
        ]);

        return [
            'client_id'      => $scan['client_id'],
            'action'         => 'booth_scan',
            'success'        => true,
            'message'        => 'Visit recorded.',
            'already_visited'=> false,
            'registrant'     => [
                'id'       => $registrant->id,
                'name'     => $registrant->name,
                'email'    => $registrant->email,
                'company'  => $registrant->company,
                'job_title'=> $registrant->job_title,
            ],
            'visited_at' => $visitedAt,
        ];
    }

    /**
     * Offline workshop registration → register & approve (idempotent).
     */
    private function syncWorkshop(array $scan): array
    {
        $resolved = $this->resolveSyncRegistrant($scan);
        if (isset($resolved['error'])) {
            return array_merge([
                'client_id' => $scan['client_id'],
                'action'    => 'workshop_register',
                'success'   => false,
            ], $resolved);
        }
        /** @var Registrant $registrant */
        $registrant = $resolved['registrant'];

        $workshop = isset($scan['item_id']) ? Workshop::find($scan['item_id']) : null;
        if (!$workshop) {
            return [
                'client_id' => $scan['client_id'],
                'action'    => 'workshop_register',
                'success'   => false,
                'message'   => 'Workshop not found.',
            ];
        }

        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();

        if ($existing && $existing->pivot->status === 'approved') {
            return [
                'client_id'         => $scan['client_id'],
                'action'            => 'workshop_register',
                'success'           => true,
                'message'           => 'Already registered & approved for this workshop.',
                'already_registered'=> true,
                'registrant'        => [
                    'id'    => $registrant->id,
                    'name'  => $registrant->name,
                    'email' => $registrant->email,
                ],
                'status' => 'approved',
            ];
        }

        if ($existing) {
            $registrant->workshops()->updateExistingPivot($workshop->id, [
                'status' => 'approved', 'processed_by' => null, 'processed_at' => now(),
            ]);
        } else {
            $registrant->workshops()->attach($workshop->id, $registrant->utmForPivot() + [
                'status' => 'approved', 'processed_by' => null, 'processed_at' => now(),
            ]);
        }

        // Sync to linked agenda items (mirror admin approve flow)
        $workshop->load('agendaItems');
        foreach ($workshop->agendaItems as $item) {
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'approved', 'processed_by' => null, 'processed_at' => now(),
                ]);
            } else {
                $registrant->agendaItems()->attach($item->id, [
                    'status' => 'approved', 'processed_by' => null, 'processed_at' => now(),
                ]);
            }
        }

        return [
            'client_id'         => $scan['client_id'],
            'action'            => 'workshop_register',
            'success'           => true,
            'message'           => 'Registrant registered & approved for this workshop.',
            'already_registered'=> false,
            'registrant'        => [
                'id'       => $registrant->id,
                'name'     => $registrant->name,
                'email'    => $registrant->email,
                'company'  => $registrant->company,
                'job_title'=> $registrant->job_title,
            ],
            'status' => 'approved',
        ];
    }

    // ────────────────────────────────────────────────────────────────────
    // Mobile app support — config, MQTT status/test, activity feed
    // ────────────────────────────────────────────────────────────────────

    /**
     * Resolve which admin's printer to use as the default for the logged-in
     * user. Priority: the provided admin_id (if it is an existing admin) →
     * first super admin → 1.
     *
     * The mobile app passes the id of the user who logged in via /api/login
     * (e.g. ?admin_id=12 or body { "admin_id": 12 }), so the default printer
     * topic follows whoever is logged in (print/admin-{their id}) instead of
     * always print/admin-1.
     */
    /**
     * Resolve the user making an API call from an optional admin_id.
     */
    private function resolveApiUser(?int $adminId): ?User
    {
        if (!$adminId) {
            return null;
        }

        return User::find($adminId);
    }

    /**
     * Whether the resolved API user may manage/track the given agenda item.
     * - No user / non-room account → allowed (open API, backward compatible).
     * - Room account with NO assignments → allowed (manages ALL sessions).
     * - Room account WITH assignments → only the assigned sessions.
     */
    private function agendaItemAllowed(?User $user, AgendaItem $item): bool
    {
        if (!$user || !$user->isRoomAccount()) {
            return true;
        }

        $ids = $user->scopedAgendaItemIds();

        return $ids === null || in_array($item->id, $ids, true);
    }

    /**
     * Machine-readable scope description for the mobile app.
     */
    private function scopeMeta(?User $user): array
    {
        if (!$user || !$user->isRoomAccount()) {
            return ['type' => 'unrestricted', 'agenda_item_ids' => []];
        }

        $ids = $user->scopedAgendaItemIds();

        return [
            'type'            => $ids === null ? 'all' : 'assigned',
            'agenda_item_ids' => $ids ?? [],
        ];
    }

    /**
     * Whether the resolved API user may manage/track the given booth.
     * - No user / non-booth account → allowed (open API, backward compatible).
     * - Booth account → only its own booth.
     */
    private function boothAllowed(?User $user, Booth $booth): bool
    {
        if (!$user || !$user->isBoothAccount()) {
            return true;
        }

        $ids = $user->scopedBoothIds();

        return $ids === null || in_array($booth->id, $ids, true);
    }

    /**
     * Machine-readable booth scope description for the mobile app.
     */
    private function boothScopeMeta(?User $user): array
    {
        if (!$user || !$user->isBoothAccount()) {
            return ['type' => 'unrestricted', 'booth_ids' => []];
        }

        $ids = $user->scopedBoothIds();

        return [
            'type'      => $ids === null ? 'all' : 'assigned',
            'booth_ids' => $ids ?? [],
        ];
    }

    private function resolvePrinterAdminId(?int $adminId): int
    {
        if ($adminId && User::where('id', $adminId)->where('is_admin', true)->exists()) {
            return $adminId;
        }

        return User::where('role', 'super_admin')->orderBy('id')->value('id') ?? 1;
    }

    /**
     * App configuration for the mobile app (base URL, event, MQTT, printers).
     *
     * GET /api/config?admin_id={userId}   — admin_id (optional) = the logged-in
     * user's id; default_topic follows it. Falls back to first super admin.
     */
    public function config(Request $request): JsonResponse
    {
        $defaultAdminId = $this->resolvePrinterAdminId($request->integer('admin_id') ?: null);
        $prefix         = config('mqtt.topic_prefix', 'print');
        $base           = $request->getSchemeAndHttpHost();
        $apiUser        = $this->resolveApiUser($request->integer('admin_id') ?: null);

        return response()->json([
            'success' => true,
            'data'    => [
                'app' => [
                    'name'           => config('app.name'),
                    'event_id'       => '',
                    'base_url'       => $base,
                    'api_base_url'   => $base . '/api',
                    'request_format' => 'json',
                    'version'        => '1.0.0',
                    'room'           => $apiUser && $apiUser->isRoomAccount()
                        ? ['id' => $apiUser->room_id, 'name' => $apiUser->room?->name]
                        : null,
                    'booth'          => $apiUser && $apiUser->isBoothAccount()
                        ? ['id' => $apiUser->booth_id, 'name' => $apiUser->booth?->name]
                        : null,
                    'scope'          => $this->scopeMeta($apiUser),
                ],
                'mqtt' => [
                    'enabled'          => app(\App\Services\MqttService::class)->isActive(),
                    'host'             => config('mqtt.host'),
                    'port'             => (int) config('mqtt.port'),
                    'topic_prefix'     => $prefix,
                    'default_admin_id' => $defaultAdminId,
                    'default_topic'    => $prefix . '/admin-' . $defaultAdminId,
                ],
                'printers' => User::where('is_admin', true)
                    ->orderBy('id')
                    ->get()
                    ->pipe(function ($admins) use ($prefix) {
                        $online = app(\App\Services\MqttService::class)->printersActive($admins->pluck('id')->all());

                        return $admins->map(fn ($u) => [
                            'id'     => $u->id,
                            'name'   => $u->name,
                            'email'  => $u->email,
                            'role'   => $u->role,
                            'topic'  => $prefix . '/admin-' . $u->id,
                            'online' => (bool) ($online[$u->id] ?? false),
                        ]);
                    })
                    ->values(),
                'server_time' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * MQTT broker + printer status (which topic each admin's printer listens on).
     *
     * GET /api/mqtt/status?admin_id={userId}  — admin_id (optional) = the
     * logged-in user's id; default_topic follows it. Falls back to first super
     * admin.
     */
    public function mqttStatus(Request $request): JsonResponse
    {
        $service        = app(MqttService::class);
        $prefix         = config('mqtt.topic_prefix', 'print');
        $defaultAdminId = $this->resolvePrinterAdminId($request->integer('admin_id') ?: null);

        return response()->json([
            'success' => true,
            'data'    => [
                'enabled'          => $service->isActive(),
                'host'             => config('mqtt.host'),
                'port'             => (int) config('mqtt.port'),
                'topic_prefix'     => $prefix,
                'default_admin_id' => $defaultAdminId,
                'default_topic'    => $prefix . '/admin-' . $defaultAdminId,
                'printers'         => User::where('is_admin', true)
                    ->orderBy('id')
                    ->get()
                    ->pipe(function ($admins) use ($prefix) {
                        $online = app(\App\Services\MqttService::class)->printersActive($admins->pluck('id')->all());

                        return $admins->map(fn ($u) => [
                            'id'     => $u->id,
                            'name'   => $u->name,
                            'role'   => $u->role,
                            'topic'  => $prefix . '/admin-' . $u->id,
                            'online' => (bool) ($online[$u->id] ?? false),
                        ]);
                    })
                    ->values(),
            ],
        ]);
    }

    /**
     * Send a test badge to an admin's printer via MQTT — verifies the whole
     * chain (server → broker → printer) without scanning a real participant.
     *
     * POST /api/mqtt/test  body: { admin_id?, name?, company? }
     */
    public function mqttTest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_id' => ['nullable', 'integer'],
            'name'     => ['nullable', 'string', 'max:255'],
            'company'  => ['nullable', 'string', 'max:255'],
        ]);

        $adminId = $this->resolvePrinterAdminId($validated['admin_id'] ?? null);

        $service = app(MqttService::class);
        $topic   = config('mqtt.topic_prefix', 'print') . '/admin-' . $adminId;

        $published = $service->publish($topic, [
            'objQRCode'    => 'TEST-' . now()->format('His'),
            'objName'      => $validated['name'] ?? 'Test Print',
            'objCompany'   => $validated['company'] ?? 'MQTT Test',
            'objFirstName' => 'Test',
            'objLastName'  => 'Print',
            'objJob'       => '',
            'objTrackCode' => '',
            'objTableNum'  => '',
            'test'         => true,
        ]);

        $this->logScan([
            'action'    => 'mqtt_test',
            'item_type' => 'printer',
            'item_label'=> $topic,
            'admin_id'  => $adminId,
            'success'   => $published,
            'printed'   => $published,
            'message'   => $published ? 'Test print published.' : 'MQTT publish failed (disabled or broker unreachable).',
        ]);

        return response()->json([
            'success'      => $published,
            'message'      => $published
                ? 'Test print published to ' . $topic . '.'
                : 'MQTT is not enabled or the broker is unreachable.',
            'published'    => $published,
            'mqtt_enabled' => $service->enabled(),
            'topic'        => $topic,
        ], $published ? 200 : 422);
    }

    /**
     * Recent scan/print activity feed — for live monitoring of the mobile app.
     *
     * GET /api/activity?limit=20&action=registration_scan
     */
    public function activity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action'   => ['nullable', 'string', 'max:50'],
            'limit'    => ['nullable', 'integer', 'min:1', 'max:100'],
            'admin_id' => ['nullable', 'integer'],
        ]);

        $query = ScanLog::query();
        if (!empty($validated['action'])) {
            $query->where('action', $validated['action']);
        }

        // Room accounts only see activity for the sessions assigned to them.
        $apiUser = $this->resolveApiUser($validated['admin_id'] ?? null);
        if ($apiUser) {
            // Room account → only agenda activity for its assigned sessions.
            $agendaIds = $apiUser->scopedAgendaItemIds();
            if ($agendaIds !== null) {
                $query->where('item_type', 'agenda')->whereIn('item_id', $agendaIds);
            } else {
                // Booth account → only booth activity for its own booth.
                $boothIds = $apiUser->scopedBoothIds();
                if ($boothIds !== null) {
                    $query->where('item_type', 'booth')->whereIn('item_id', $boothIds);
                }
            }
        }

        $logs = $query->latest()->limit($validated['limit'] ?? 20)->get()->map(fn ($l) => [
            'id'              => $l->id,
            'action'          => $l->action,
            'registrant_id'   => $l->registrant_id,
            'registrant_name' => $l->registrant_name,
            'item_id'         => $l->item_id,
            'item_type'       => $l->item_type,
            'item_label'      => $l->item_label,
            'source'          => $l->source,
            'client_id'       => $l->client_id,
            'admin_id'        => $l->admin_id,
            'success'         => $l->success,
            'printed'         => $l->printed,
            'message'         => $l->message,
            'created_at'      => $l->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $logs,
        ]);
    }
}
