<?php $__empty_2 = true; $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
    <tr class="hover:bg-gray-50/50 transition">
        <td class="px-5 py-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    <?php echo e(strtoupper(mb_substr($r->display_name ?: $r->name, 0, 1))); ?>

                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($r->display_name ?: $r->name); ?></p>
                    <p class="text-xs text-gray-500 truncate"><?php echo e($r->email); ?></p>
                    <?php if($r->phone): ?>
                        <p class="text-xs text-gray-400 truncate"><?php echo e($r->phone); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </td>
        <td class="px-3 py-3 hidden md:table-cell">
            <p class="text-sm text-gray-700 truncate" title="<?php echo e($r->company); ?>"><?php echo e($r->company ?: '—'); ?></p>
        </td>
        <td class="px-3 py-3">
            <?php $sessions = $r->feedbacks->pluck('agendaItem')->filter(); ?>
            <p class="text-xs text-gray-500 mb-1">
                <span class="font-semibold text-indigo-600"><?php echo e($sessions->count()); ?></span>
                session<?php echo e($sessions->count() === 1 ? '' : 's'); ?>

            </p>
            <div class="flex flex-wrap gap-1">
                <?php $__currentLoopData = $sessions->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agendum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100 max-w-full">
                        <span class="truncate"><?php echo e($agendum->title); ?></span>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($sessions->count() > 3): ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">+<?php echo e($sessions->count() - 3); ?></span>
                <?php endif; ?>
            </div>
        </td>
        <td class="px-3 py-3 hidden lg:table-cell">
            <?php $last = $r->feedbacks->sortByDesc('created_at')->first(); ?>
            <p class="text-xs text-gray-500"><?php echo e($last && $last->created_at ? $last->created_at->format('d M Y, H:i') : '—'); ?></p>
        </td>
        <td class="px-3 py-3 text-center">
            <a href="<?php echo e(route('admin.feedback-registrants.show', $r)); ?>"
               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View
            </a>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
    <tr>
        <td colspan="5" class="px-5 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-sm text-gray-400 mt-3">No registrants found with submitted feedback.</p>
        </td>
    </tr>
<?php endif; ?>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/feedback-registrants/_rows.blade.php ENDPATH**/ ?>