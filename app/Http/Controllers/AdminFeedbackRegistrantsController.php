<?php

namespace App\Http\Controllers;

use App\Models\AgendaFeedback;
use App\Models\AgendaItem;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Feedback (Registrants) — read-only viewer/admin page that shows which
 * sessions/tracks have received feedback and which registrants filled it.
 * Two display modes:
 *   - view=session   (default): grouped by session / track
 *   - view=registrant: grouped by registrant
 * Supports search and a QR-code scan that resolves a registrant and shows
 * their feedback detail.
 */
class AdminFeedbackRegistrantsController extends Controller
{
    /**
     * Permission key required to view this page (same as Registrants).
     */
    private function authorizeView(): void
    {
        if (!Auth::user()->hasPermission('registrants')) {
            abort(403, 'You do not have access to the Feedback page.');
        }
    }

    /**
     * List sessions/tracks that have received feedback (or registrants who
     * submitted feedback, when ?view=registrant).
     */
    public function index(Request $request)
    {
        $this->authorizeView();

        $view = $request->get('view', 'session'); // session | registrant
        $search = trim((string) $request->get('search'));

        $totalWithFeedback = AgendaFeedback::distinct('registrant_id')->count('registrant_id');
        $totalSessions     = AgendaItem::whereHas('feedback')->count();

        if ($view === 'registrant') {
            $query = Registrant::query()
                ->whereHas('feedbacks')
                ->with('feedbacks.agendaItem')
                ->withCount('feedbacks');

            if ($search !== '') {
                $term = '%' . $search . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('company', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term);
                });
            }

            $registrants = $query->orderBy('name')->paginate(20)->withQueryString();

            return view('admin.feedback-registrants.index', compact('registrants', 'view', 'search', 'totalWithFeedback', 'totalSessions'));
        }

        // Default: group by session / track.
        $query = AgendaItem::query()
            ->whereHas('feedback')
            ->with(['feedback.registrant', 'feedback.answers.question'])
            ->withCount('feedback');

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('room', 'like', $term)
                  ->orWhereHas('workshop', fn ($w) => $w->where('name', 'like', $term))
                  ->orWhereHas('track', fn ($t) => $t->where('name', 'like', $term)->orWhere('title', 'like', $term));
            });
        }

        $sessions = $query->orderBy('start_time')->orderBy('order')->paginate(20)->withQueryString();

        return view('admin.feedback-registrants.index', compact('sessions', 'view', 'search', 'totalWithFeedback', 'totalSessions'));
    }

    /**
     * AJAX live-search endpoint for the feedback-registrants table.
     */
    public function search(Request $request)
    {
        $this->authorizeView();

        $view = $request->get('view', 'session'); // session | registrant
        $search = trim((string) $request->get('search'));

        if ($view === 'registrant') {
            $query = Registrant::query()
                ->whereHas('feedbacks')
                ->with('feedbacks.agendaItem')
                ->withCount('feedbacks');

            if ($search !== '') {
                $term = '%' . $search . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('company', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term);
                });
            }

            $registrants = $query->orderBy('name')->paginate(20)->withQueryString();

            return response()->json([
                'success'    => true,
                'rows'       => view('admin.feedback-registrants._rows', ['registrants' => $registrants])->render(),
                'pagination' => $registrants->links()->render(),
                'total'      => $registrants->total(),
            ]);
        }

        // Group by session / track.
        $query = AgendaItem::query()
            ->whereHas('feedback')
            ->with(['feedback.registrant', 'feedback.answers.question'])
            ->withCount('feedback');

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('room', 'like', $term)
                  ->orWhereHas('workshop', fn ($w) => $w->where('name', 'like', $term))
                  ->orWhereHas('track', fn ($t) => $t->where('name', 'like', $term)->orWhere('title', 'like', $term));
            });
        }

        $sessions = $query->orderBy('start_time')->orderBy('order')->paginate(20)->withQueryString();

        return response()->json([
            'success'    => true,
            'rows'       => view('admin.feedback-registrants._session_rows', ['sessions' => $sessions])->render(),
            'pagination' => $sessions->links()->render(),
            'total'      => $sessions->total(),
        ]);
    }

    /**
     * Detail page for a single registrant — which sessions/tracks they filled
     * feedback for, with the submitted answers.
     */
    public function show(Registrant $registrant)
    {
        $this->authorizeView();

        $feedbacks = $registrant->feedbacks()
            ->with(['answers.question', 'agendaItem'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.feedback-registrants.show', compact('registrant', 'feedbacks'));
    }

    /**
     * QR-code lookup. The registrant's QR encodes their unique_code (or the
     * qr_token). Accepts a scanned code and returns JSON with the registrant
     * plus the sessions they submitted feedback for.
     */
    public function qrLookup(Request $request)
    {
        $this->authorizeView();

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = trim($request->input('code'));

        $registrant = Registrant::where('unique_code', $code)
            ->orWhere('qr_token', $code)
            ->first();

        if (!$registrant) {
            return response()->json([
                'success' => false,
                'message' => 'Registrant not found for the scanned QR code.',
            ], 404);
        }

        $feedbacks = $registrant->feedbacks()
            ->with(['answers.question', 'agendaItem'])
            ->orderByDesc('created_at')
            ->get();

        $sessions = $feedbacks->map(fn ($fb) => [
            'id'           => $fb->agenda_item_id,
            'title'        => $fb->agendaItem?->title ?? '—',
            'company'      => $this->companyName($fb->agendaItem),
            'type'         => $fb->agendaItem ? $this->sessionType($fb->agendaItem) : 'General',
            'room'         => $fb->agendaItem?->room ?? '—',
            'time'         => $fb->agendaItem ? $fb->agendaItem->timeLabel() : '—',
            'submitted_at' => $fb->created_at ? $fb->created_at->format('d M Y, H:i') : '',
            'answers'      => $fb->answers->map(fn ($a) => [
                'question' => $a->question?->question_text ?? 'Question',
                'type'     => $a->question?->question_type ?? 'text',
                'value'    => $a->answer_value,
            ])->values(),
        ])->values();

        return response()->json([
            'success'    => true,
            'registrant' => [
                'id'          => $registrant->id,
                'name'        => $registrant->display_name ?: $registrant->name,
                'email'       => $registrant->email,
                'phone'       => $registrant->phone,
                'company'     => $registrant->company,
                'job_title'   => $registrant->job_title,
                'job_role'    => $registrant->job_role,
                'status'      => $registrant->status,
                'unique_code' => $registrant->unique_code,
            ],
            'sessions' => $sessions,
            'total'    => $feedbacks->count(),
        ]);
    }

    /**
     * Resolve the vendor/company label for a session, mirroring the feedback
     * form's "{company} - {title}" logic (workshop name, else track name/title).
     */
    private function companyName($item): ?string
    {
        if (!$item) {
            return null;
        }

        if (!in_array($item->agenda_type, ['track', 'workshop'], true)
            && empty($item->track_id) && empty($item->workshop_id)) {
            return null;
        }

        $name = null;
        if ($item->workshop) {
            $name = trim((string) $item->workshop->name);
        }
        if ((empty($name) || $name === '-') && $item->track) {
            $name = trim((string) ($item->track->name ?: $item->track->title));
        }

        return (empty($name) || $name === '-') ? null : $name;
    }

    /**
     * Human session type (Workshop / Track / General).
     */
    private function sessionType($item): string
    {
        if ($item->agenda_type === 'workshop' || !empty($item->workshop_id)) {
            return 'Workshop';
        }
        if ($item->agenda_type === 'track' || !empty($item->track_id)) {
            return 'Track';
        }
        return 'General';
    }
}
