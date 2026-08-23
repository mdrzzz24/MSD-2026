<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\AgendaItem;
use App\Models\AgendaFeedback;
use App\Models\AgendaFeedbackAnswer;
use App\Models\AgendaItemQuestion;
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

        $timeSlots = \App\Models\TimeSlot::ordered()->get();

        return view('admin.agenda.feedback-index', compact('groups', 'status', 'totalAll', 'totalOn', 'totalOff', 'timeSlots'));
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

        $timeSlots = \App\Models\TimeSlot::ordered()->get();

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
                $item->timeLabelWith($timeSlots),
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

    /**
     * Export ALL feedback submissions (every respondent & their answers) to CSV.
     *
     * Wide format — one row per submission. Two-row header: row 1 = questions,
     * row 2 = answer options beneath each question (a choice question spans its
     * option cells). ✓ marks the selected option(s). Questions sharing the same
     * text across sessions (template copies) are merged into a single column
     * group. A submission with no answers still gets a row.
     */
    public function exportAllFeedbackCsv()
    {
        $feedbacks = AgendaFeedback::with([
            'agendaItem.workshop',
            'agendaItem.track',
            'registrant',
            'answers.question',
        ])
        ->orderByDesc('created_at')
        ->get();

        // Only sessions that actually received feedback contribute columns.
        $sessionIds = $feedbacks->pluck('agenda_item_id')->filter()->unique()->values();

        // All questions of those sessions, so unanswered questions still appear.
        $questions = AgendaItemQuestion::whereIn('agenda_item_id', $sessionIds)
            ->orderBy('agenda_item_id')
            ->orderBy('order')
            ->get();

        $columnGroups = $this->buildFeedbackColumns($questions);

        // Two-row header: row 1 carries the questions, row 2 the answer options
        // beneath each question. For a choice question the question text sits on
        // the first option column (row 1) and each option fills its own cell
        // (row 2), so the question visually spans its options in Excel.
        $headerRow1 = ['Session', 'Type', 'Name', 'Email', 'Phone', 'Company', 'Job Title', 'Job Role', 'Industry', 'Employees', 'Unique Code', 'Submitted At (WIB)'];
        $headerRow2 = array_fill(0, count($headerRow1), '');

        // Flatten question groups into actual CSV columns.
        $columns = [];
        foreach ($columnGroups as $key => $group) {
            if (in_array($group['type'], ['choice', 'multi_choice'], true)) {
                $options = $group['options'];
                $hasOther = false;
                foreach ($options as $i => $opt) {
                    if (mb_strtolower(trim($opt)) === 'other') {
                        $hasOther = true;
                        $options[$i] = '__OTHER__';
                    }
                }
                if ($group['other'] && !$hasOther) {
                    $options[] = '__OTHER__';
                }

                foreach ($options as $i => $opt) {
                    $display = $opt === '__OTHER__' ? 'Other' : $opt;
                    $headerRow1[] = $i === 0 ? $key : '';
                    $headerRow2[] = $display;
                    $columns[] = ['key' => $key, 'option' => $opt];
                }
            } else {
                $headerRow1[] = $key;
                $headerRow2[] = '';
                $columns[] = ['key' => $key, 'option' => null];
            }
        }

        $rows = [];
        foreach ($feedbacks as $fb) {
            $item    = $fb->agendaItem;
            $type    = $item ? $this->feedbackType($item) : 'General';
            $session = $item ? $this->feedbackSessionLabel($item) : '—';

            $selected = $this->feedbackSelectedMap($fb);

            $row = [
                $session,
                $type,
                $fb->name ?: ($fb->registrant?->name ?? ''),
                $fb->email ?: ($fb->registrant?->email ?? ''),
                $fb->registrant?->phone ?? '',
                $fb->registrant?->company ?? '',
                $fb->registrant?->job_title ?? '',
                $fb->registrant?->job_role ?? '',
                $fb->registrant?->industry ?? '',
                $fb->registrant?->employees ?? '',
                $fb->registrant?->unique_code ?? '',
                $fb->created_at ? $fb->created_at->copy()->addHours(7)->format('Y-m-d H:i:s') : '',
            ];

            foreach ($columns as $col) {
                $row[] = $this->feedbackCellValue($col, $selected[$col['key']] ?? []);
            }

            $rows[] = $row;
        }

        return $this->csvDownload([$headerRow1, $headerRow2], $rows, 'all-feedback-' . now()->format('YmdHis') . '.csv');
    }

    /**
     * Workshop / Track / General label for a session (mirrors the feedback page).
     */
    private function feedbackType(AgendaItem $item): string
    {
        if ($item->agenda_type === 'workshop' || !empty($item->workshop_id)) {
            return 'Workshop';
        }
        if ($item->agenda_type === 'track' || !empty($item->track_id)) {
            return 'Track';
        }
        return 'General';
    }

    /**
     * "{company} - {title}" for track/workshop sessions, else the plain title.
     */
    private function feedbackSessionLabel(AgendaItem $item): string
    {
        $company = $this->companyName($item);
        return $company ? $company . ' - ' . $item->title : $item->title;
    }

    /**
     * Merge questions into column groups keyed by their (normalized) text.
     * Template copies that share text across sessions are combined, with their
     * options unioned and the "Other" flag OR-ed.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\AgendaItemQuestion>  $questions
     */
    private function buildFeedbackColumns($questions): array
    {
        $groups = [];
        foreach ($questions as $q) {
            $key = trim((string) $q->question_text);
            if ($key === '') {
                $key = '(untitled question)';
            }

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'type'    => $q->question_type,
                    'options' => [],
                    'other'   => false,
                ];
            }

            foreach ((array) $q->options as $opt) {
                $opt = trim((string) $opt);
                if ($opt !== '' && !in_array($opt, $groups[$key]['options'], true)) {
                    $groups[$key]['options'][] = $opt;
                }
            }

            if ($q->allow_other) {
                $groups[$key]['other'] = true;
            }
        }

        return $groups;
    }

    /**
     * Collect the selected answer values per question for one submission.
     * Multi-choice answers (stored as JSON arrays) are flattened.
     */
    private function feedbackSelectedMap(AgendaFeedback $fb): array
    {
        $map = [];
        foreach ($fb->answers as $a) {
            if (!$a->question) {
                continue;
            }

            $key = trim((string) $a->question->question_text);
            if ($key === '') {
                $key = '(untitled question)';
            }

            $value = (string) $a->answer_value;
            if ($a->question->question_type === 'multi_choice' && $value !== '') {
                $decoded = json_decode($value, true);
                $values = is_array($decoded) ? array_map('strval', $decoded) : [trim($value)];
            } else {
                $values = [trim($value)];
            }

            foreach ($values as $v) {
                if ($v !== '' && !in_array($v, $map[$key] ?? [], true)) {
                    $map[$key][] = $v;
                }
            }
        }

        return $map;
    }

    /**
     * Resolve the cell value for one export column.
     *
     * - Scalar questions (text / rating / yes_no) return the answer itself.
     * - Regular choice options return ✓ when selected, empty otherwise.
     * - The "Other" sub-column returns the typed free text (or ✓ when empty).
     */
    private function feedbackCellValue(array $col, array $selected): string
    {
        $option = $col['option'];

        if ($option === null) {
            return implode(' | ', $selected);
        }

        if ($option === '__OTHER__') {
            foreach ($selected as $v) {
                if (mb_stripos($v, 'Other') === 0) {
                    $text = ltrim(trim(mb_substr($v, 5)), ': ');
                    return $text === '' ? '✓' : $text;
                }
            }
            return '';
        }

        return in_array($option, $selected, true) ? '✓' : '';
    }
}
