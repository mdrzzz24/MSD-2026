<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\AgendaItem;
use App\Models\AgendaFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminFeedbackController extends Controller
{
    use Exportable;
    /**
     * Toggle feedback form on/off for an agenda item.
     */
    public function toggle(AgendaItem $agendum)
    {
        $agendum->update([
            'feedback_enabled' => !$agendum->feedback_enabled,
        ]);

        $status = $agendum->feedback_enabled ? 'enabled' : 'disabled';
        return redirect()->back()
            ->with('success', "Feedback form for <strong>{$agendum->title}</strong> has been {$status}.");
    }

    /**
     * Show feedback responses for a specific agenda item.
     */
    public function show(AgendaItem $agendum)
    {
        $feedbacks = $agendum->feedback()->with('answers')->latest()->get();
        $questions = $agendum->feedbackQuestions;
        return view('admin.agenda.feedback', compact('agendum', 'feedbacks', 'questions'));
    }

    /**
     * Show list of all agenda items with feedback status.
     *
     * Sessions are split into two groups:
     * - Active: placed in the /admin/agenda grid (they have a room assigned).
     * - Inactive: not used in the agenda (no room assigned).
     *
     * Both groups can be filtered by feedback status (?status=all|on|off).
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all'); // all | on | off

        $baseQuery = AgendaItem::query();
        if ($status === 'on') {
            $baseQuery->where('feedback_enabled', true);
        } elseif ($status === 'off') {
            $baseQuery->where('feedback_enabled', false);
        }

        $groups = [
            'Active Sessions (in Agenda)'   => (clone $baseQuery)->whereNotNull('room')->withCount('feedback')->orderBy('start_time')->get(),
            'Inactive Sessions (not in Agenda)' => (clone $baseQuery)->whereNull('room')->withCount('feedback')->orderBy('start_time')->get(),
        ];

        $totalAll = AgendaItem::count();
        $totalOn  = AgendaItem::where('feedback_enabled', true)->count();
        $totalOff = AgendaItem::where('feedback_enabled', false)->count();

        return view('admin.agenda.feedback-index', compact('groups', 'status', 'totalAll', 'totalOn', 'totalOff'));
    }

    /**
     * Export sessions with feedback ENABLED (On) to Excel.
     * Contains: No, Session, Company, Type (General/Track/Workshop), Time,
     * Feedback, Responses, Normal Link, Short Link, plus QR codes for the
     * normal (full) link and the short link.
     */
    public function exportExcel()
    {
        // Only sessions whose feedback form is turned ON are exported.
        $items = AgendaItem::where('feedback_enabled', true)
            ->with(['workshop', 'track'])
            ->withCount('feedback')
            ->orderBy('start_time')
            ->get();

        $headers = ['No', 'Session', 'Company', 'Type', 'Time', 'Feedback', 'Responses', 'Normal Link', 'Short Link', 'QR Full Link', 'QR Short Link'];

        $rows = [];
        foreach ($items as $i => $item) {
            $type = match (true) {
                ($item->agenda_type === 'workshop' || !empty($item->workshop_id)) => 'Workshop',
                ($item->agenda_type === 'track' || !empty($item->track_id))       => 'Track',
                default                                                           => 'General',
            };

            $normalLink = route('feedback.form', $item->slug);
            $shortLink  = $item->shortUrl();

            $rows[] = [
                $i + 1,
                $item->title,
                $this->companyName($item) ?? '',
                $type,
                $item->timeLabel(),
                $item->feedback_enabled ? 'On' : 'Off',
                $item->feedback_count,
                $normalLink,
                $shortLink,
                $this->qrCell($normalLink),
                $this->qrCell($shortLink),
            ];
        }

        $filename = 'sessions-feedback-'.now()->format('YmdHis').'.xlsx';

        return \App\Services\XlsxExporter::download($headers, $rows, $filename);
    }

    /**
     * Fetch a QR code PNG for the given URL and wrap it as an image cell.
     * Falls back to an empty string if the QR service is unreachable.
     */
    private function qrCell(string $url): array|string
    {
        try {
            $response = Http::timeout(10)
                ->get('https://api.qrserver.com/v1/create-qr-code/', [
                    'size'   => '120x120',
                    'margin' => 0,
                    'data'   => $url,
                ]);

            if ($response->successful() && str_starts_with($response->header('Content-Type'), 'image/')) {
                return \App\Services\XlsxExporter::imageCell($response->body());
            }
        } catch (\Throwable $e) {
            // Ignore QR fetch errors; the cell stays empty.
        }

        return '';
    }

    /**
     * Resolve the vendor/company label for a session, mirroring the feedback
     * form's "{company} - {title}" logic (workshop name, else track name/title).
     */
    private function companyName(AgendaItem $item): ?string
    {
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
     * Export feedback responses for an agenda item as CSV.
     */
    public function exportCsv(AgendaItem $agendum)
    {
        $feedbacks = $agendum->feedback()->with('answers')->latest()->get();
        $questions = $agendum->feedbackQuestions;

        $headers = ['Name', 'Email', 'Submitted At'];

        foreach ($questions as $q) {
            $headers[] = $q->question_text;
        }

        $rows = [];
        foreach ($feedbacks as $fb) {
            $row = [
                $fb->name,
                $fb->email,
                $fb->created_at ? $fb->created_at->copy()->addHours(7)->format('Y-m-d H:i:s') : '',
            ];

            foreach ($questions as $q) {
                $answer = $fb->answers->firstWhere('agenda_item_question_id', $q->id);
                $value = $answer ? $answer->answer_value : '';
                if ($q->question_type === 'multi_choice' && $value) {
                    $decoded = json_decode($value, true);
                    $value = is_array($decoded) ? implode(' | ', $decoded) : $value;
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $filename = 'feedback-' . \Illuminate\Support\Str::slug($agendum->title) . '-' . now()->format('YmdHis') . '.csv';

        return $this->csvDownload($headers, $rows, $filename);
    }
}
