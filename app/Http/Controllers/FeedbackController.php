<?php

namespace App\Http\Controllers;

use App\Models\AgendaFeedbackAnswer;
use App\Models\AgendaItem;
use App\Models\AgendaFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Resolve a short link code (/f/{code}) to its feedback page.
     */
    public function shortlink(string $code)
    {
        $agendum = AgendaItem::where('short_code', $code)->firstOrFail();
        return redirect()->route('feedback.form', $agendum->slug);
    }

    /**
     * Show feedback form for an agenda item.
     */
    public function form(AgendaItem $agendum)
    {
        if (!$agendum->feedback_enabled) {
            return redirect()->back()->with('error', 'Feedback form is not available for this session.');
        }

        $registrant = Auth::guard('registrant')->user();
        $questions = $agendum->feedbackQuestions;

        // Unauthenticated visitors can open the page but must sign in (login popup).
        $needsLogin = $registrant === null;
        $existingFeedback = null;

        if (!$needsLogin) {
            // If this registrant has already submitted, show a read-only summary of their answers.
            $existingFeedback = AgendaFeedback::where('agenda_item_id', $agendum->id)
                ->where('registrant_id', $registrant->id)
                ->with('answers')
                ->first();
        }

        return view('feedback.form', compact('agendum', 'questions', 'registrant', 'existingFeedback', 'needsLogin'));
    }

    /**
     * Store feedback submission.
     */
    public function store(Request $request, AgendaItem $agendum)
    {
        if (!$agendum->feedback_enabled) {
            return redirect()->back()->with('error', 'Feedback form is not available for this session.');
        }

        $questions = $agendum->feedbackQuestions;

        $registrant = Auth::guard('registrant')->user();

        // Build dynamic validation rules
        $rules = [];

        // Build a parent-answer lookup from the request
        $requestAnswers = $request->input('answers', []);

        foreach ($questions as $q) {
            $field = 'answers.' . $q->id;
            $fieldRules = [];

            // Check if this question is visible based on parent condition
            $isVisible = true;
            if ($q->parent_question_id) {
                $parentAnswer = $requestAnswers[$q->parent_question_id] ?? null;
                $triggerValue = strtolower(trim((string) $q->trigger_value));
                $norm = fn ($v) => strtolower(trim((string) $v));
                if (is_array($parentAnswer)) {
                    $isVisible = collect($parentAnswer)->contains(fn ($v) => $norm($v) === $triggerValue);
                } else {
                    $isVisible = $norm($parentAnswer) === $triggerValue;
                }
            }

            if ($q->required && $isVisible) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($q->question_type === 'rating') {
                $fieldRules[] = 'integer';
                $fieldRules[] = 'min:1';
                $fieldRules[] = 'max:' . ($q->rating_max ?: 5);
            } elseif ($q->question_type === 'choice') {
                $fieldRules[] = 'string';
            } elseif ($q->question_type === 'multi_choice') {
                $fieldRules[] = 'array';
                $rules[$field . '.*'] = ['string', 'max:255'];
                // Optional free-text field shown when "Other" is selected
                $rules['other_answers.' . $q->id] = ['nullable', 'string', 'max:1000'];
            } elseif ($q->question_type === 'yes_no') {
                $fieldRules[] = 'in:yes,no';
            } else {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:5000';
            }

            $rules[$field] = $fieldRules;
        }

        $validated = $request->validate($rules);

        // Check if this registrant already submitted feedback for this agenda
        $existing = AgendaFeedback::where('agenda_item_id', $agendum->id)
            ->where('registrant_id', $registrant->id)
            ->exists();

        if ($existing) {
            return back()->with('error', 'You have already submitted feedback for this session.');
        }

        // Create feedback record
        $feedback = AgendaFeedback::create([
            'agenda_item_id' => $agendum->id,
            'registrant_id'  => $registrant->id,
            'name'           => $registrant->display_name,
            'email'          => $registrant->email,
        ]);

        // Save answers
        foreach ($questions as $q) {
            $raw = $validated['answers'][$q->id] ?? null;
            $otherText = trim((string) ($validated['other_answers'][$q->id] ?? ''));

            if ($q->question_type === 'multi_choice' && is_array($raw)) {
                $selected = array_values(array_filter($raw, fn ($v) => $v !== '' && $v !== null));
                if (in_array('__other__', $selected, true)) {
                    $selected = array_values(array_map(function ($v) use ($otherText) {
                        return $v === '__other__'
                            ? ('Other' . ($otherText !== '' ? ": {$otherText}" : ''))
                            : $v;
                    }, $selected));
                }
                $answerValue = count($selected) > 0 ? json_encode($selected) : null;
            } elseif ($q->question_type === 'choice' && $raw === '__other__') {
                $answerValue = 'Other' . ($otherText !== '' ? ": {$otherText}" : '');
            } else {
                $answerValue = $raw;
            }

            if ($answerValue !== null && $answerValue !== '') {
                AgendaFeedbackAnswer::create([
                    'agenda_feedback_id'     => $feedback->id,
                    'agenda_item_question_id' => $q->id,
                    'answer_value'           => $answerValue,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
}
