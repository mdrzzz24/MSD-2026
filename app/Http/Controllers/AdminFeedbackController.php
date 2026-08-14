<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\AgendaItem;
use App\Models\AgendaFeedback;
use Illuminate\Http\Request;

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
