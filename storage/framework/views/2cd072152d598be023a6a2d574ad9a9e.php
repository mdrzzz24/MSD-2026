<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback — <?php echo e($registrant->display_name ?: $registrant->name); ?> — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-3">
                <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="<?php echo e(route('admin.feedback-registrants.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Feedback
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900 truncate"><?php echo e($registrant->display_name ?: $registrant->name); ?></h1>
                <div class="ml-auto flex items-center gap-2">
                    <a href="<?php echo e(route('admin.registrants.show', $registrant)); ?>"
                       class="px-4 py-2 text-sm font-semibold rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                        Registrant Detail
                    </a>
                    <a href="<?php echo e(route('admin.feedback-registrants.index')); ?>"
                       class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">
                        Back to List
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6">
            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                        <?php echo e(strtoupper(mb_substr($registrant->display_name ?: $registrant->name, 0, 1))); ?>

                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-xl font-bold text-gray-900"><?php echo e($registrant->display_name ?: $registrant->name); ?></h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize <?php echo e($registrant->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($registrant->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700')); ?>">
                                <?php echo e($registrant->status); ?>

                            </span>
                        </div>
                        <div class="flex flex-wrap gap-x-5 gap-y-1 mt-2 text-sm text-gray-600">
                            <?php if($registrant->email): ?>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <?php echo e($registrant->email); ?>

                                </span>
                            <?php endif; ?>
                            <?php if($registrant->phone): ?>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <?php echo e($registrant->phone); ?>

                                </span>
                            <?php endif; ?>
                            <?php if($registrant->company): ?>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <?php echo e($registrant->company); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if($registrant->job_title || $registrant->job_role): ?>
                            <p class="text-xs text-gray-400 mt-1.5">
                                <?php echo e(collect([$registrant->job_title, $registrant->job_role])->filter()->implode(' · ')); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-3xl font-bold text-indigo-600"><?php echo e($feedbacks->count()); ?></p>
                        <p class="text-xs text-gray-400">session<?php echo e($feedbacks->count() === 1 ? '' : 's'); ?> with feedback</p>
                    </div>
                </div>
            </div>

            
            <div class="space-y-4">
                <?php $__empty_2 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <?php
                        $agendum = $fb->agendaItem;
                        $type = $agendum ? ($agendum->agenda_type === 'workshop' || !empty($agendum->workshop_id) ? 'Workshop' : ($agendum->agenda_type === 'track' || !empty($agendum->track_id) ? 'Track' : 'General')) : 'General';
                        $typeClass = $type === 'Workshop' ? 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200' : ($type === 'Track' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-gray-100 text-gray-600 border-gray-200');
                        $company = null;
                        if ($agendum) {
                            if (in_array($agendum->agenda_type, ['track', 'workshop'], true) || !empty($agendum->track_id) || !empty($agendum->workshop_id)) {
                                $company = $agendum->workshop ? trim((string) $agendum->workshop->name) : null;
                                if ((empty($company) || $company === '-') && $agendum->track) {
                                    $company = trim((string) ($agendum->track->name ?: $agendum->track->title));
                                }
                                if (empty($company) || $company === '-') { $company = null; }
                            }
                        }
                        $displayTitle = $company ? $company . ' - ' . ($agendum?->title ?? '') : ($agendum?->title ?? 'Unknown session');
                    ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-start justify-between gap-3 flex-wrap">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border <?php echo e($typeClass); ?>"><?php echo e($type); ?></span>
                                    <?php if($agendum?->room): ?>
                                        <span class="text-xs text-gray-400"><?php echo e($agendum->room); ?></span>
                                    <?php endif; ?>
                                    <?php if($agendum): ?>
                                        <span class="text-xs text-gray-400"><?php echo e($agendum->timeLabel()); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 mt-1"><?php echo e($displayTitle); ?></h3>
                                <p class="text-[11px] text-gray-400 mt-0.5">Submitted <?php echo e($fb->created_at ? $fb->created_at->format('d M Y, H:i') : '—'); ?></p>
                            </div>
                            <?php if($agendum): ?>
                            <a href="<?php echo e(route('feedback.form', $agendum)); ?>" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 flex-shrink-0">
                                Open feedback page
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <?php if(!Auth::user()->canWrite()): ?>
                                <p class="text-xs text-gray-400">Feedback answers are hidden for this role.</p>
                            <?php elseif($fb->answers->count() > 0): ?>
                                <div class="space-y-2.5">
                                    <?php $__currentLoopData = $fb->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $q = $answer->question; ?>
                                        <div class="text-sm">
                                            <span class="text-xs font-semibold text-gray-500"><?php echo e($q?->question_text ?? 'Answer'); ?>:</span>
                                            <?php if($q?->question_type === 'rating'): ?>
                                                <span class="inline-flex items-center gap-0.5 ml-1 align-middle">
                                                    <?php for($i = 1; $i <= ($q->rating_max ?: 5); $i++): ?>
                                                        <svg class="w-3.5 h-3.5 <?php echo e($i <= (int) $answer->answer_value ? 'text-yellow-400' : 'text-gray-200'); ?>" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    <?php endfor; ?>
                                                    <span class="text-xs text-gray-400 ml-1">(<?php echo e((int) $answer->answer_value); ?>/<?php echo e($q->rating_max ?: 5); ?>)</span>
                                                </span>
                                            <?php elseif($q?->question_type === 'yes_no'): ?>
                                                <span class="ml-1 font-medium <?php echo e(strtolower((string) $answer->answer_value) === 'yes' ? 'text-emerald-600' : 'text-red-500'); ?>">
                                                    <?php echo e(ucfirst((string) $answer->answer_value)); ?>

                                                </span>
                                            <?php elseif($q?->question_type === 'multi_choice'): ?>
                                                <span class="text-gray-700 ml-1">
                                                    <?php $selected = json_decode($answer->answer_value, true); ?>
                                                    <?php echo e(is_array($selected) ? implode(', ', $selected) : $answer->answer_value); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-700 ml-1"><?php echo e($answer->answer_value); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-400">No answers recorded for this session.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <p class="text-sm text-gray-400 mt-3">This registrant has not submitted any session feedback.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
    // Sidebar toggle (mobile / tablet)
    function toggleSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (!sidebar || !overlay) return;
        const isOpen = sidebar.classList.contains('-translate-x-full');
        if (isOpen) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
    document.getElementById('sidebarToggle')?.addEventListener('click', toggleSidebar);
    document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/feedback-registrants/show.blade.php ENDPATH**/ ?>