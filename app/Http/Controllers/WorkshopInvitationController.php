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
        $invitation = WorkshopInvitation::where(function ($q) use ($token) {
                $q->where('token', $token)->orWhere('slug', $token);
            })
            ->with(['workshop.agendaItems.speakers', 'track.speakers'])
            ->firstOrFail();

        $workshop = $invitation->workshop;
        $track = $invitation->track;

        // For a workshop-level (master custom-slug) invitation, resolve the session/track from the matching UTM link
        if (!$track) {
            $track = \App\Models\UtmLink::resolveTrackForWorkshop($workshop->id, [
                'utm_source'   => request('utm_source'),
                'utm_medium'   => request('utm_medium'),
                'utm_campaign' => request('utm_campaign'),
                'utm_content'  => request('utm_content'),
            ]);
        }
        if ($track) {
            $track->load(['speakers', 'agendaItems.speakers']);
        }
        $email = old('email', request('email', $invitation->email ?? ''));

        // Whether to show the inline event registration form (came from an unregistered email submission)
        $needRegistration = request()->has('need_registration');

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

        return view('workshop-invitation', compact('workshop', 'track', 'invitation', 'email', 'registrationStatus', 'speakers', 'needRegistration'));
    }

    /**
     * Handle invitation registration — verify email and register.
     */
    public function register(Request $request, $token)
    {
        $invitation = WorkshopInvitation::where(function ($q) use ($token) {
                $q->where('token', $token)->orWhere('slug', $token);
            })
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

        // For a workshop-level (master custom-slug) invitation, resolve the session/track from the matching UTM link
        if (!$track) {
            $track = \App\Models\UtmLink::resolveTrackForWorkshop($workshop->id, [
                'utm_source'   => $request->input('utm_source'),
                'utm_medium'   => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
                'utm_content'  => $request->input('utm_content'),
            ]);
        }
        $email = $request->input('email');
        $redirectUrl = route('workshop.invitation', $token) . '?email=' . urlencode($email);

        // Keep UTM params in the redirect so a master custom-slug link keeps resolving the same track after registering
        $utmParams = array_filter($request->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']), fn($v) => !is_null($v) && $v !== '');
        if ($utmParams) {
            $redirectUrl .= '&' . http_build_query($utmParams);
        }

        // Find registrant by email
        $registrant = Registrant::where('email', $email)->first();

        // Capture UTM attribution from the invitation link (only fills empty fields)
        if ($registrant) {
            $utmFill = [];
            if (!$registrant->utm_source)   $utmFill['utm_source']   = $request->input('utm_source');
            if (!$registrant->utm_medium)   $utmFill['utm_medium']   = $request->input('utm_medium');
            if (!$registrant->utm_campaign) $utmFill['utm_campaign'] = $request->input('utm_campaign');
            if ($utmFill) {
                $registrant->update($utmFill);
            }
        }

        if (!$registrant) {
            // Store invitation token + UTM (+ chosen track) in session so we can auto-register after event registration
            session([
                'pending_workshop_invitation' => $token,
                'pending_workshop_utm' => array_filter($request->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']), fn($v) => !is_null($v) && $v !== ''),
            ]);

            // Stay on the invitation page — show the event registration form inline (no redirect to home)
            $params = ['email' => $email, 'need_registration' => 1];
            foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'] as $k) {
                if ($request->filled($k)) {
                    $params[$k] = $request->input($k);
                }
            }

            return redirect(route('workshop.invitation', $token) . '?' . http_build_query($params))
                ->with('info', 'Your email is not registered for the event yet. Complete the event registration form below — after that, you will be automatically registered for this workshop.');
        }

        // Check if registrant is approved (skip if workshop bypasses approval)
        if (!$workshop->invitation_bypass_approval && $registrant->status !== 'approved') {
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
                }

                // Block registering for a session that overlaps another registration
                if ($registrant->hasTimeConflict($workshop->id, $agendaItem->id, $agendaItem->date, $agendaItem->start_time, $agendaItem->end_time)) {
                    return redirect($redirectUrl)->withInput()->with('error', 'You are already registered for another session at the same time.');
                }

                if ($existingAi) {
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
            $this->syncWorkshopRegistration($registrant, $workshop, 'pending', $track->id, $this->utmOverride($request));

            $invitation->incrementUse();
            return redirect($redirectUrl)->with('success', 'Successfully registered. Waiting for admin approval.');
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

        if ($registrant->hasTimeConflict($workshop->id, null, $wsDate, $wsStart, $wsEnd)) {
            return back()->withInput()->with('error', 'You are already registered for another session at the same time.');
        }

        // Register the registrant for the workshop
        $registrant->workshops()->attach($workshop->id, $registrant->utmForPivot($this->utmOverride($request)) + ['status' => 'pending']);
        $invitation->incrementUse();

        return redirect($redirectUrl)->with('success', 'Successfully registered for the workshop. Waiting for admin approval.');
    }

    /**
     * Extract non-empty UTM params from the request (workshop invitation link).
     */
    private function utmOverride(Request $request): array
    {
        return array_filter($request->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']), fn($v) => !is_null($v) && $v !== '');
    }

    /**
     * Sync workshop-level registration pivot.
     */
    private function syncWorkshopRegistration(Registrant $registrant, Workshop $workshop, string $status, ?int $trackId = null, array $utm = []): void
    {
        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();
        if ($existing) {
            $registrant->workshops()->updateExistingPivot($workshop->id, array_merge([
                'status' => $status,
                'track_id' => $trackId ?? $existing->pivot->track_id,
            ], $utm));
        } else {
            $registrant->workshops()->attach($workshop->id, array_merge([
                'status' => $status,
                'track_id' => $trackId,
            ], $utm));
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
            'link_type' => ['required', 'in:random,custom'],
            'slug'      => ['nullable', 'string', 'max:120'],
            'email'     => ['nullable', 'email', 'max:255'],
            'max_uses'  => ['nullable', 'integer', 'min:0'],
            'track_id'  => ['nullable', 'exists:tracks,id'],
        ]);

        // Verify track belongs to this workshop
        if ($request->filled('track_id')) {
            $track = \App\Models\Track::findOrFail($request->input('track_id'));
            if ($track->workshop_id !== $workshop->id) {
                return back()->with('error', 'Track does not belong to this workshop.');
            }
        }

        // Custom slug link (optional) — default is the random token
        $slug = null;
        if ($request->input('link_type') === 'custom') {
            $slug = Str::slug($request->input('slug', ''));
            if ($slug === '') {
                return back()->with('error', 'Custom slug is required for a custom link.');
            }
            if (WorkshopInvitation::where('slug', $slug)->exists()) {
                return back()->with('error', "Custom link '/invitation/workshop/{$slug}' already exists. Please use a different slug.");
            }
        }

        $invitation = WorkshopInvitation::create([
            'workshop_id' => $workshop->id,
            'track_id'    => $request->input('track_id'),
            'email'       => $request->input('email'),
            'max_uses'    => $request->input('max_uses', 0),
            'is_active'   => true,
            'slug'        => $slug,
        ]);

        $link = $invitation->invitation_url;
        $trackName = $invitation->track?->name;
        $label = $trackName ? " ({$trackName})" : '';
        $typeLabel = $slug ? 'Custom' : 'Random';

        return back()->with('success', "{$typeLabel} invitation link{$label} generated: <a href=\"{$link}\" target=\"_blank\" style=\"color:#4f46e5;font-weight:600;text-decoration:underline;\">{$link}</a>");
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
        $tracks = $workshop->tracks()->get();
        $utmLinks = \App\Models\UtmLink::forWorkshop()->where('workshop_id', $workshop->id)->latest()->get();

        // Lightweight JSON data for the UTM modal (invitation/slug + track pickers)
        $wsInvitationData = $invitations->map(fn($i) => [
            'id'          => $i->id,
            'workshop_id' => $i->workshop_id,
            'track_id'    => $i->track_id,
            'slug'        => $i->slug,
            'token'       => $i->token,
            'track_name'  => $i->track?->name,
        ])->values();
        $wsTrackData = $tracks->map(fn($t) => [
            'id'          => $t->id,
            'workshop_id' => $t->workshop_id,
            'name'        => $t->name,
        ])->values();

        return view('admin.workshops.invitations', compact('workshop', 'invitations', 'tracks', 'utmLinks', 'wsInvitationData', 'wsTrackData'));
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
