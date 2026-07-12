<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Questions — <?php echo e($agendum->title); ?> — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="<?php echo e(route('admin.agenda.feedback.show', $agendum)); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <?php echo e($agendum->title); ?>

                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Manage Questions</h1>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8 max-w-3xl">
            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800">Current Questions (<?php echo e($currentQuestions->count()); ?>)</h2>
                    <?php if($currentQuestions->count() > 0): ?>
                        <form action="<?php echo e(route('admin.agenda.feedback.questions.clear', $agendum)); ?>" method="POST" onsubmit="return confirm('Remove all questions?')">
                            <?php echo csrf_field(); ?>
                            <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition">Remove All</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $currentQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="px-6 py-4 hover:bg-gray-50/50 transition" x-data="{ editing: false }">
                            
                            <div x-show="!editing" class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800"><?php echo e($q->question_text); ?></p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <span class="capitalize"><?php echo e(str_replace('_', ' ', $q->question_type)); ?></span>
                                        <?php if($q->required): ?> · <span class="text-emerald-600 font-medium">Required</span> <?php endif; ?>
                                        <?php if($q->source_template_id): ?> · <span class="text-indigo-500">from template</span> <?php endif; ?>
                                        <?php if($q->parent_question_id): ?> · <span class="text-indigo-500">nested</span> <?php endif; ?>
                                        <?php if($q->trigger_value): ?> · trigger: <span class="font-mono text-indigo-500"><?php echo e($q->trigger_value); ?></span> <?php endif; ?>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                                    <span class="text-xs text-gray-400">#<?php echo e($q->order + 1); ?></span>
                                    <button @click="editing = true" class="px-3 py-1 text-xs font-medium rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">Edit</button>
                                </div>
                            </div>

                            
                            <form x-show="editing" action="<?php echo e(route('admin.agenda.feedback.questions.update', [$agendum, $q])); ?>" method="POST">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Question</label>
                                        <input type="text" name="question_text" value="<?php echo e($q->question_text); ?>" required
                                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                            <select name="question_type"
                                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                                <option value="text" <?php echo e($q->question_type === 'text' ? 'selected' : ''); ?>>Text</option>
                                                <option value="rating" <?php echo e($q->question_type === 'rating' ? 'selected' : ''); ?>>Rating (1-5)</option>
                                                <option value="choice" <?php echo e($q->question_type === 'choice' ? 'selected' : ''); ?>>Multiple Choice</option>
                                                <option value="yes_no" <?php echo e($q->question_type === 'yes_no' ? 'selected' : ''); ?>>Yes / No</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Trigger Value</label>
                                            <input type="text" name="trigger_value" value="<?php echo e($q->trigger_value); ?>"
                                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
                                                   placeholder="e.g. Yes">
                                        </div>
                                    </div>
                                    <?php if($q->question_type === 'choice'): ?>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Options (one per line)</label>
                                        <textarea name="options" rows="3"
                                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none"><?php echo e(is_array($q->options) ? implode("\n", $q->options) : ''); ?></textarea>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" name="required" value="0">
                                        <label class="flex items-center gap-2 text-xs text-gray-600">
                                            <input type="checkbox" name="required" value="1" <?php echo e($q->required ? 'checked' : ''); ?> class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            Required
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">Save</button>
                                        <button type="button" @click="editing = false" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-10 text-center text-sm text-gray-400">No questions set yet. Apply a template below.</div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-800 mb-4">Apply Template</h2>
                <form action="<?php echo e(route('admin.agenda.feedback.apply-template', $agendum)); ?>" method="POST" onsubmit="return confirm('This will replace all existing questions. Continue?')">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <select name="template_id" required
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <option value="">— Select Template —</option>
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?> (<?php echo e($t->questions_count); ?> questions)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm transition">
                        Apply Template
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/feedback/apply-template.blade.php ENDPATH**/ ?>