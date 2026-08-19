<?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $type = $s->agenda_type === 'workshop' || !empty($s->workshop_id) ? 'Workshop' : ($s->agenda_type === 'track' || !empty($s->track_id) ? 'Track' : 'General');
        $typeClass = $type === 'Workshop' ? 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200' : ($type === 'Track' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-gray-100 text-gray-600 border-gray-200');
        $company = null;
        if (in_array($s->agenda_type, ['track', 'workshop'], true) || !empty($s->track_id) || !empty($s->workshop_id)) {
            $company = $s->workshop ? trim((string) $s->workshop->name) : null;
            if ((empty($company) || $company === '-') && $s->track) {
                $company = trim((string) ($s->track->name ?: $s->track->title));
            }
            if (empty($company) || $company === '-') { $company = null; }
        }
        $displayTitle = $company ? $company . ' - ' . $s->title : $s->title;
        $rowId = 'sess-' . $s->id;
    ?>
    <tr class="hover:bg-gray-50/50 transition cursor-pointer session-main-row"
        onclick="toggleSessionDetail(event, '<?php echo e($rowId); ?>')"
        data-expanded="0">
        <td class="px-5 py-3">
            <div class="flex items-start gap-2">
                <span class="session-chevron mt-0.5 text-gray-400 flex-shrink-0 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border <?php echo e($typeClass); ?>"><?php echo e($type); ?></span>
                        <?php if($s->room): ?>
                            <span class="text-xs text-gray-400"><?php echo e($s->room); ?></span>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400"><?php echo e($s->timeLabel()); ?></span>
                    </div>
                    <p class="text-sm font-semibold text-gray-900 mt-1 truncate" title="<?php echo e($displayTitle); ?>"><?php echo e($displayTitle); ?></p>
                </div>
            </div>
        </td>
        <td class="px-3 py-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                <?php echo e($s->feedback_count); ?> response<?php echo e($s->feedback_count === 1 ? '' : 's'); ?>

            </span>
        </td>
        <td class="px-3 py-3 hidden lg:table-cell">
            <?php $last = $s->feedback->sortByDesc('created_at')->first(); ?>
            <p class="text-xs text-gray-500"><?php echo e($last && $last->created_at ? $last->created_at->format('d M Y, H:i') : '—'); ?></p>
        </td>
        <td class="px-3 py-3 text-center">
            <a href="<?php echo e(route('feedback.form', $s)); ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()"
               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Feedback
            </a>
        </td>
    </tr>
    <tr class="hidden session-detail-row" id="<?php echo e($rowId); ?>">
        <td colspan="4" class="px-5 py-4 bg-gray-50/60 border-t border-gray-100">
            <div class="space-y-3">
                <?php $__empty_2 = true; $__currentLoopData = $s->feedback->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <?php $reg = $fb->registrant; ?>
                    <div class="bg-white rounded-xl border border-gray-100 p-4">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    <?php echo e(strtoupper(mb_substr($reg?->display_name ?: $reg?->name ?: ($fb->name ?: '?'), 0, 1))); ?>

                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        <?php echo e($reg?->display_name ?: $reg?->name ?: $fb->name); ?>

                                    </p>
                                    <p class="text-xs text-gray-500 truncate"><?php echo e($fb->email); ?></p>
                                    <?php if($reg?->phone || $reg?->company): ?>
                                        <p class="text-xs text-gray-400 truncate">
                                            <?php echo e(collect([$reg->phone, $reg->company])->filter()->implode(' · ')); ?>

                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 flex items-center gap-2">
                                <span class="text-[11px] text-gray-400"><?php echo e($fb->created_at ? $fb->created_at->format('d M Y, H:i') : ''); ?></span>
                                <?php if(Auth::user()->canWrite()): ?>
                                    <a href="<?php echo e(route('admin.feedback-registrants.show', $reg)); ?>"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <?php if(Auth::user()->canWrite() && $fb->answers->count() > 0): ?>
                            <div class="mt-3 space-y-1.5 bg-gray-50 rounded-xl p-3">
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
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    <p class="text-sm text-gray-400 text-center py-4">No feedback responses for this session.</p>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="4" class="px-5 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-sm text-gray-400 mt-3">No sessions found with submitted feedback.</p>
            <?php if(request('search')): ?>
                <p class="text-xs text-gray-400 mt-1">No sessions found where this registrant filled feedback.</p>
            <?php endif; ?>
        </td>
    </tr>
<?php endif; ?>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/feedback-registrants/_session_rows.blade.php ENDPATH**/ ?>