<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\AgendaItemQuestion;
use App\Models\FeedbackTemplate;
use App\Models\FeedbackTemplateQuestion;
use Illuminate\Http\Request;

class FeedbackTemplateController extends Controller
{
    /**
     * List all templates.
     */
    public function index()
    {
        $templates = FeedbackTemplate::withCount('questions')->latest()->get();
        return view('admin.feedback.templates', compact('templates'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.feedback.template-form');
    }

    /**
     * Store a new template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'questions'   => ['required', 'array', 'min:1'],
            'questions.*.text'     => ['required', 'string', 'max:500'],
            'questions.*.type'     => ['required', 'in:text,rating,choice,yes_no'],
            'questions.*.options'  => ['nullable', 'string'],
            'questions.*.required' => ['boolean'],
            'questions.*.parent_id' => ['nullable', 'string'],
            'questions.*.trigger_value' => ['nullable', 'string', 'max:255'],
        ]);

        $template = FeedbackTemplate::create([
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'created_by'  => auth()->id(),
        ]);

        $idMap = []; // maps old question index to new DB id for parent references

        foreach ($validated['questions'] as $i => $q) {
            $options = null;
            if ($q['type'] === 'choice' && !empty($q['options'])) {
                $options = array_map('trim', explode("\n", $q['options']));
            }

            $newQ = FeedbackTemplateQuestion::create([
                'template_id'   => $template->id,
                'question_text' => $q['text'],
                'question_type' => $q['type'],
                'options'       => $options,
                'order'         => $i,
                'required'      => $q['required'] ?? true,
                'parent_question_id' => null,
                'trigger_value' => $q['trigger_value'] ?? null,
            ]);
            $idMap[$i] = $newQ->id;
        }

        // Second pass: update parent references
        foreach ($validated['questions'] as $i => $q) {
            if ($q['parent_id'] !== '' && $q['parent_id'] !== null && isset($idMap[$q['parent_id']])) {
                $newId = $idMap[$i];
                FeedbackTemplateQuestion::where('id', $newId)
                    ->update(['parent_question_id' => $idMap[$q['parent_id']]]);
            }
        }

        return redirect()->route('admin.feedback.templates')
            ->with('success', "Template <strong>{$template->name}</strong> created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(FeedbackTemplate $template)
    {
        $template->load('questions');
        return view('admin.feedback.template-form', compact('template'));
    }

    /**
     * Update a template.
     */
    public function update(Request $request, FeedbackTemplate $template)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'questions'   => ['required', 'array', 'min:1'],
            'questions.*.text'     => ['required', 'string', 'max:500'],
            'questions.*.type'     => ['required', 'in:text,rating,choice,yes_no'],
            'questions.*.options'  => ['nullable', 'string'],
            'questions.*.required' => ['boolean'],
            'questions.*.parent_id' => ['nullable', 'string'],
            'questions.*.trigger_value' => ['nullable', 'string', 'max:255'],
        ]);

        $template->update([
            'name'        => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Delete existing questions and recreate
        $template->questions()->delete();

        $idMap = [];

        foreach ($validated['questions'] as $i => $q) {
            $options = null;
            if ($q['type'] === 'choice' && !empty($q['options'])) {
                $options = array_map('trim', explode("\n", $q['options']));
            }

            $newQ = FeedbackTemplateQuestion::create([
                'template_id'   => $template->id,
                'question_text' => $q['text'],
                'question_type' => $q['type'],
                'options'       => $options,
                'order'         => $i,
                'required'      => $q['required'] ?? true,
                'parent_question_id' => null,
                'trigger_value' => $q['trigger_value'] ?? null,
            ]);
            $idMap[$i] = $newQ->id;
        }

        // Second pass: update parent references
        foreach ($validated['questions'] as $i => $q) {
            if ($q['parent_id'] !== '' && $q['parent_id'] !== null && isset($idMap[$q['parent_id']])) {
                $newId = $idMap[$i];
                FeedbackTemplateQuestion::where('id', $newId)
                    ->update(['parent_question_id' => $idMap[$q['parent_id']]]);
            }
        }

        return redirect()->route('admin.feedback.templates')
            ->with('success', "Template <strong>{$template->name}</strong> updated successfully.");
    }

    /**
     * Delete a template.
     */
    public function destroy(FeedbackTemplate $template)
    {
        $name = $template->name;
        $template->delete();

        return redirect()->route('admin.feedback.templates')
            ->with('success', "Template <strong>{$name}</strong> deleted.");
    }

    /**
     * Show form to apply template to an agenda item.
     */
    public function applyForm(AgendaItem $agendum)
    {
        $templates = FeedbackTemplate::withCount('questions')->latest()->get();
        $currentQuestions = $agendum->feedbackQuestions;
        return view('admin.feedback.apply-template', compact('agendum', 'templates', 'currentQuestions'));
    }

    /**
     * Apply a template to an agenda item.
     */
    public function applyStore(Request $request, AgendaItem $agendum)
    {
        $validated = $request->validate([
            'template_id' => ['required', 'exists:feedback_templates,id'],
        ]);

        $template = FeedbackTemplate::findOrFail($validated['template_id']);

        // Remove existing questions
        $agendum->feedbackQuestions()->delete();

        // Copy template questions to agenda item
        $template->applyToAgendaItem($agendum);

        return redirect()->route('admin.agenda.feedback.show', $agendum)
            ->with('success', "Template <strong>{$template->name}</strong> applied to <strong>{$agendum->title}</strong>.");
    }

    /**
     * Remove all questions from an agenda item.
     */
    public function clearQuestions(AgendaItem $agendum)
    {
        $agendum->feedbackQuestions()->delete();

        return redirect()->route('admin.agenda.feedback.show', $agendum)
            ->with('success', "All questions removed from <strong>{$agendum->title}</strong>.");
    }

    /**
     * Update a single question on an agenda item (without affecting the template).
     */
    public function updateQuestion(Request $request, AgendaItem $agendum, AgendaItemQuestion $question)
    {
        if ($question->agenda_item_id !== $agendum->id) {
            abort(404);
        }

        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:500'],
            'question_type' => ['required', 'in:text,rating,choice,yes_no'],
            'options'       => ['nullable', 'string'],
            'required'      => ['boolean'],
            'trigger_value' => ['nullable', 'string', 'max:255'],
        ]);

        $options = null;
        if ($validated['question_type'] === 'choice' && !empty($validated['options'])) {
            $options = array_map('trim', explode("\n", $validated['options']));
        }

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options'       => $options,
            'required'      => $validated['required'] ?? true,
            'trigger_value' => $validated['trigger_value'] ?? null,
        ]);

        return redirect()->route('admin.agenda.feedback.questions', $agendum)
            ->with('success', 'Question updated successfully.');
    }
}
