<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use App\Models\Workshop;
use App\Models\WorkshopInvitation;
use App\Models\EmailTemplate;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkshopInvitationController extends Controller
{
    /**
     * Show the invitation landing page.
     */
    public function show($token)
    {
        $invitation = WorkshopInvitation::where('token', $token)
            ->with(['workshop.agendaItems.speakers', 'track.speakers'])
            ->firstOrFail();

        $workshop = $invitation->workshop;
        $track = $invitation->track;
        $email = old('email', request('email', $invitation->email ?? ''));

        // Determine which speakers to show
        $speakers = $track?->speakers ?? $workshop->agendaItems->first()?->speakers ?? collect();

        // Check if this email is already registered for this workshop/track
        $registrationStatus = null;
        if ($email) {
            $registrant = \App\Models\Registrant::where('email', $email)->first();
            if ($registrant) {
                if ($track) {
                    // Check track-level registration
                    $trackAgendaItems = $track->agendaItems;
                    foreach ($trackAgendaItems as $ai) {
                        $existing = $registrant->agendaItems()->where('agenda_item_id', $ai->id)->first();
                        if ($existing) {
                            $registrationStatus = $existing->pivot->status;
                            break;
                        }
                    }
                }
                // Fallback to workshop-level check
                if (!$registrationStatus) {
                    $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();
                    if ($existing) {
                        $registrationStatus = $existing->pivot->status;
                    }
                }
            }
        }

        return view('workshop-invitation', compact('workshop', 'track', 'invitation', 'email', 'registrationStatus', 'speakers'));
    }

    /**
     * Handle invitation registration — verify email and register.
     */
    public function register(Request $request, $token)
    {
        $invitation = WorkshopInvitation::where('token', $token)
            ->with(['workshop', 'track'])
            ->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect(route('workshop.invitation', $token) . '?email=' . urlencode($request->input('email', '')))
                ->with('error', 'This invitation link is no longer valid.');
        }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $workshop = $invitation->workshop;
        $track = $invitation->track;
        $email = $request->input('email');
        $redirectUrl = route('workshop.invitation', $token) . '?email=' . urlencode($email);

        // Find registrant by email
        $registrant = Registrant::where('email', $email)->first();

        if (!$registrant) {
            return redirect($redirectUrl)->withInput()->with('error', 'No registration found with this email. Please register for MSD 2026 first.');
        }

        // Check if registrant is approved
        if ($registrant->status !== 'approved') {
            return redirect($redirectUrl)->withInput()->with('error', 'Your registration needs to be approved first before you can join a workshop.');
        }

        // ── Track-specific registration ──
        if ($track) {
            $track->load('agendaItems');
            $workshop->load('agendaItems');

            // Try to find an agenda item linked to this track, or fallback to workshop's first agenda item
            $agendaItem = $track->agendaItems->first() ?? $workshop->agendaItems->first();

            // Register at agenda item level (for track tracking)
            if ($agendaItem) {
                $existingAi = $registrant->agendaItems()->where('agenda_item_id', $agendaItem->id)->first();
                if ($existingAi) {
                    $status = $existingAi->pivot->status;
                    if ($status === 'approved') {
                        return redirect($redirectUrl)->with('info', 'You are already registered for this track.');
                    } elseif ($status === 'pending') {
                        return redirect($redirectUrl)->with('info', 'Your registration for this track is pending approval.');
                    }
                    $registrant->agendaItems()->updateExistingPivot($agendaItem->id, [
                        'status' => 'pending',
                        'admin_notes' => null,
                        'processed_by' => null,
                        'processed_at' => null,
                    ]);
                } else {
                    $registrant->agendaItems()->attach($agendaItem->id, ['status' => 'pending']);
                }
            }

            // Also sync to workshop-level pivot with track_id
            $this->syncWorkshopRegistration($registrant, $workshop, 'pending', $track->id);

            $invitation->incrementUse();
            return redirect($redirectUrl)->with('success', 'Successfully registered for track <strong>' . e($track->name) . '</strong>. Waiting for admin approval.');
        }

        // ── Workshop-level registration (no track, fallback to original behavior) ──
        // Check existing workshop registration
        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();

        if ($existing) {
            $status = $existing->pivot->status;
            if ($status === 'approved') {
                return redirect($redirectUrl)->with('info', 'You are already registered for this workshop.');
            } elseif ($status === 'pending') {
                return redirect($redirectUrl)->with('info', 'Your registration for this workshop is pending approval.');
            }
            // Re-register if rejected — update status back to pending
            $registrant->workshops()->updateExistingPivot($workshop->id, [
                'status' => 'pending',
                'admin_notes' => null,
                'processed_by' => null,
                'processed_at' => null,
            ]);
            $invitation->incrementUse();
            return redirect($redirectUrl)->with('success', 'Re-registered successfully. Waiting for admin approval.');
        }

        // Check time conflict with other workshop registrations (exclude rejected)
        $workshop->load('agendaItems');
        $agendaItem = $workshop->agendaItems->first();
        $wsDate = $workshop->date ?? $agendaItem?->date;
        $wsStart = $workshop->start_time ?? $agendaItem?->start_time;
        $wsEnd = $workshop->end_time ?? $agendaItem?->end_time;

        if ($wsDate && $wsStart && $wsEnd) {
            $conflict = $registrant->workshops()
                ->where('workshops.id', '!=', $workshop->id)
                ->wherePivot('status', '!=', 'rejected')
                ->where(function ($q) use ($wsDate, $wsStart, $wsEnd) {
                    $q->where('date', $wsDate)
                      ->where(function ($q2) use ($wsStart, $wsEnd) {
                          $q2->whereBetween('start_time', [$wsStart, $wsEnd])
                             ->orWhereBetween('end_time', [$wsStart, $wsEnd])
                             ->orWhere(function ($q3) use ($wsStart, $wsEnd) {
                                 $q3->where('start_time', '<=', $wsStart)
                                    ->where('end_time', '>=', $wsEnd);
                             });
                      });
                })->exists();

            if (!$conflict) {
                $conflict = $registrant->agendaItems()
                    ->wherePivot('status', '!=', 'rejected')
                    ->where(function ($q) use ($wsDate) {
                        $q->where('agenda_items.date', $wsDate)
                          ->orWhereNull('agenda_items.date');
                    })
                    ->where(function ($q) use ($wsStart, $wsEnd) {
                        $q->where('start_time', '<', $wsEnd)
                           ->where('end_time', '>', $wsStart);
                    })->exists();
            }

            if ($conflict) {
                return back()->withInput()->with('error', 'You are already registered for another session at the same time.');
            }
        }

        // Register the registrant for the workshop
        $registrant->workshops()->attach($workshop->id, ['status' => 'pending']);
        $invitation->incrementUse();

        return redirect($redirectUrl)->with('success', 'Successfully registered for the workshop. Waiting for admin approval.');
    }

    /**
     * Sync workshop-level registration pivot.
     */
    private function syncWorkshopRegistration(Registrant $registrant, Workshop $workshop, string $status, ?int $trackId = null): void
    {
        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();
        if ($existing) {
            $registrant->workshops()->updateExistingPivot($workshop->id, [
                'status' => $status,
                'track_id' => $trackId ?? $existing->pivot->track_id,
            ]);
        } else {
            $registrant->workshops()->attach($workshop->id, [
                'status' => $status,
                'track_id' => $trackId,
            ]);
        }
    }

    /**
     * Admin: Generate a new invitation link for a workshop.
     */
    public function generate(Request $request, Workshop $workshop)
    {
        if (!Auth::user()->hasPermission('workshops')) {
            return back()->with('error', 'You do not have permission to generate invitations.');
        }

        $request->validate([
            'email'    => ['nullable', 'email', 'max:255'],
            'max_uses' => ['nullable', 'integer', 'min:0'],
            'track_id' => ['nullable', 'exists:tracks,id'],
        ]);

        // Verify track belongs to this workshop
        if ($request->filled('track_id')) {
            $track = \App\Models\Track::findOrFail($request->input('track_id'));
            if ($track->workshop_id !== $workshop->id) {
                return back()->with('error', 'Track does not belong to this workshop.');
            }
        }

        $invitation = WorkshopInvitation::create([
            'workshop_id' => $workshop->id,
            'track_id'    => $request->input('track_id'),
            'email'       => $request->input('email'),
            'max_uses'    => $request->input('max_uses', 0),
            'is_active'   => true,
        ]);

        $link = route('workshop.invitation', $invitation->token);
        $trackName = $invitation->track?->name;
        $label = $trackName ? " ({$trackName})" : '';

        return back()->with('success', "Invitation link{$label} generated: <a href=\"{$link}\" target=\"_blank\" style=\"color:#4f46e5;font-weight:600;text-decoration:underline;\">{$link}</a>");
    }

    /**
     * Admin: List all invitations for a workshop.
     */
    public function index(Workshop $workshop)
    {
        if (!Auth::user()->hasPermission('workshops')) {
            return back()->with('error', 'You do not have permission to view invitations.');
        }

        $invitations = $workshop->invitations()->with('track')->latest()->get();
        return view('admin.workshops.invitations', compact('workshop', 'invitations'));
    }

    /**
     * Admin: Toggle invitation active status.
     */
    public function toggle(WorkshopInvitation $invitation)
    {
        if (!Auth::user()->hasPermission('workshops')) {
            return back()->with('error', 'You do not have permission to manage invitations.');
        }

        $invitation->update(['is_active' => !$invitation->is_active]);

        return back()->with('success', 'Invitation ' . ($invitation->is_active ? 'activated' : 'deactivated') . '.');
    }

    /**
     * Admin: Update max_uses for an existing invitation.
     */
    public function updateMaxUses(Request $request, WorkshopInvitation $invitation)
    {
        if (!Auth::user()->hasPermission('workshops')) {
            return back()->with('error', 'You do not have permission to manage invitations.');
        }

        $request->validate([
            'max_uses' => ['required', 'integer', 'min:0'],
        ]);

        $oldValue = $invitation->max_uses;
        $newValue = $request->input('max_uses');

        $invitation->update(['max_uses' => $newValue]);

        $oldLabel = $oldValue === 0 ? 'Unlimited' : $oldValue;
        $newLabel = $newValue === 0 ? 'Unlimited' : $newValue;

        return back()->with('success', "Invitation limit updated from {$oldLabel} to {$newLabel}.");
    }
}
