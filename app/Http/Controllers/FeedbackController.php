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
     * Show feedback form for an agenda item.
     */
    public function form(AgendaItem $agendum)
    {
        if (!$agendum->feedback_enabled) {
            return redirect()->back()->with('error', 'Feedback form is not available for this session.');
        }

        $registrant = Auth::guard('registrant')->user();
        $questions = $agendum->feedbackQuestions;
        return view('feedback.form', compact('agendum', 'questions', 'registrant'));
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
                $isVisible = $parentAnswer === $q->trigger_value;
            }

            if ($q->required && $isVisible) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($q->question_type === 'rating') {
                $fieldRules[] = 'integer';
                $fieldRules[] = 'min:1';
                $fieldRules[] = 'max:5';
            } elseif ($q->question_type === 'choice') {
                $fieldRules[] = 'string';
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
            $answerValue = $validated['answers'][$q->id] ?? null;
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
