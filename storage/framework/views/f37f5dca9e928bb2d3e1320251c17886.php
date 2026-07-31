<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(isset($template) ? 'Edit' : 'Create'); ?> Template — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="<?php echo e(route('admin.feedback.templates')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Templates
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900"><?php echo e(isset($template) ? 'Edit' : 'Create'); ?> Template</h1>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8 max-w-3xl">
            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <form action="<?php echo e(isset($template) ? route('admin.feedback.templates.update', $template) : route('admin.feedback.templates.store')); ?>" method="POST" x-data="templateForm()">
                <?php echo csrf_field(); ?>
                <?php if(isset($template)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Template Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
                               placeholder="e.g. Workshop Feedback" value="<?php echo e(old('name', $template->name ?? '')); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none"
                                  placeholder="Optional description"><?php echo e(old('description', $template->description ?? '')); ?></textarea>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-800">Questions</h2>
                        <button type="button" @click="addQuestion()"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border border-indigo-200 transition">
                            + Add Question
                        </button>
                    </div>

                    <template x-for="(q, i) in questions" :key="i">
                        <div class="mb-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase" x-text="'Question #' + (i + 1)"></span>
                                <button type="button" @click="removeQuestion(i)" class="text-red-400 hover:text-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Question</label>
                                    <input type="text" x-model="q.text" :name="'questions[' + i + '][text]'" required
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
                                           placeholder="Enter your question">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                    <select x-model="q.type" :name="'questions[' + i + '][type]'"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                        <option value="text">Text</option>
                                        <option value="rating">Rating (1-5)</option>
                                        <option value="choice">Multiple Choice</option>
                                        <option value="yes_no">Yes / No</option>
                                    </select>
                                </div>
                            </div>

                            <div x-show="q.type === 'choice'" class="mb-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Options (one per line)</label>
                                <textarea x-model="q.options" :name="'questions[' + i + '][options]'" rows="3"
                                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none"
                                          placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                            </div>

                            
                            <div class="mb-3 p-3 bg-indigo-50/50 rounded-lg border border-indigo-100">
                                <p class="text-xs font-semibold text-indigo-600 mb-2">Conditional Logic (show this question only if...)</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Parent Question</label>
                                        <select x-model="q.parent_id" :name="'questions[' + i + '][parent_id]'"
                                                x-init="$nextTick(() => { $el.value = q.parent_id; })"
                                                class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                            <option value="">— No parent (always show) —</option>
                                            <template x-for="(pq, pi) in questions" :key="pi">
                                                <option :value="String(pi)" x-text="'#' + (pi + 1) + ': ' + (pq.text ? pq.text.substring(0, 40) : '')"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div x-show="q.parent_id !== '' && q.parent_id !== undefined">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Show when answer is</label>
                                        <input type="text" x-model="q.trigger_value" :name="'questions[' + i + '][trigger_value]'"
                                               class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
                                               placeholder="e.g. Yes, or option text">
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-gray-600">
                                <input type="hidden" :name="'questions[' + i + '][required]'" :value="q.required ? '1' : '0'">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="q.required" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    Required
                                </label>
                                <span x-show="q.parent_id" class="text-indigo-500 font-medium">🔗 Nested question</span>
                            </div>
                        </div>
                    </template>

                    <div x-show="questions.length === 0" class="text-center py-8 text-gray-400 text-sm">
                        No questions yet. Click "Add Question" to start building your template.
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm transition">
                        <?php echo e(isset($template) ? 'Update Template' : 'Create Template'); ?>

                    </button>
                    <a href="<?php echo e(route('admin.feedback.templates')); ?>" class="px-4 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
    $existingQuestions = [];
    if (isset($template)) {
        // Build a map of source question id → array index for parent reference
        $idToIndex = [];
        foreach ($template->questions as $idx => $q) {
            $idToIndex[$q->id] = $idx;
        }

        $existingQuestions = $template->questions->map(function ($q) use ($idToIndex) {
            $options = '';
            if ($q->question_type === 'choice' && $q->options) {
                $options = is_array($q->options) ? implode("\n", $q->options) : '';
            }
            $parentIdx = $q->parent_question_id ? ($idToIndex[$q->parent_question_id] ?? null) : null;
            return [
                'text' => $q->question_text,
                'type' => $q->question_type,
                'options' => $options,
                'required' => $q->required,
                'parent_id' => $parentIdx !== null ? (string) $parentIdx : '',
                'trigger_value' => $q->trigger_value ?? '',
            ];
        })->values()->toArray();
    }
?>

<script>
    function templateForm() {
        const existing = <?php echo json_encode($existingQuestions, 15, 512) ?>;

        return {
            questions: existing.length > 0 ? existing : [],
            addQuestion() {
                this.questions.push({ text: '', type: 'text', options: '', required: true, parent_id: '', trigger_value: '' });
            },
            removeQuestion(i) {
                if (this.questions.length > 1) {
                    const removedIdx = i;
                    this.questions.splice(i, 1);
                    // Update parent_id references for remaining questions (always as string)
                    this.questions.forEach((q) => {
                        if (q.parent_id !== '' && q.parent_id !== null && q.parent_id !== undefined) {
                            const pid = parseInt(q.parent_id);
                            if (pid === removedIdx) {
                                q.parent_id = '';
                            } else if (pid > removedIdx) {
                                q.parent_id = String(pid - 1);
                            }
                        }
                    });
                }
            },
        }
    }
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/feedback/template-form.blade.php ENDPATH**/ ?>