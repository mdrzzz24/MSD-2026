<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\AgendaVisit;
use App\Models\Booth;
use App\Models\BoothVisit;
use App\Models\Registrant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Get all active booths.
     */
    public function booths(): JsonResponse
    {
        $booths = Booth::active()
            ->ordered()
            ->withCount('visits')
            ->get()
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
            'data'    => $booths,
        ]);
    }

    /**
     * Get all agenda items.
     */
    public function agenda(): JsonResponse
    {
        $items = AgendaItem::ordered()
            ->with(['speakers', 'workshop', 'track'])
            ->get()
            ->map(fn($item) => [
                'id'             => $item->id,
                'title'          => $item->title,
                'description'    => $item->description,
                'agenda_type'    => $item->agenda_type ?? ($item->workshop_id ? 'workshop' : ($item->track_id ? 'track' : 'session')),
                'room'           => $item->room,
                'date'           => $item->date?->format('Y-m-d'),
                'start_time'     => $item->start_time,
                'end_time'       => $item->end_time,
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

        return response()->json([
            'success' => true,
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

        $existing = AgendaVisit::where('agenda_item_id', $agendum->id)
            ->where('registrant_id', $registrant->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success'        => true,
                'message'        => 'Already checked in to this session.',
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

        AgendaVisit::create([
            'agenda_item_id' => $agendum->id,
            'registrant_id'  => $registrant->id,
            'visited_at'     => now(),
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Check-in recorded.',
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
}
