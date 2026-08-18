<?php
$sortUrl = function (string $key) use ($sort, $direction) {
    $dir = ($sort === $key && $direction === 'asc') ? 'desc' : 'asc';
    return route('admin.onsite', array_merge(request()->except(['sort','direction','page']), ['sort' => $key, 'direction' => $dir]));
};
$sortArrow = function (string $key) use ($sort, $direction) {
    if ($sort !== $key) return '';
    return $direction === 'asc' ? ' ↑' : ' ↓';
};
?>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 w-10"><input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></th>
<th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-52"><a href="<?php echo e($sortUrl('name')); ?>" class="inline-flex items-center gap-1 hover:text-indigo-600">Name<span class="text-indigo-600"><?php echo e($sortArrow('name')); ?></span></a></th>
<th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell"><a href="<?php echo e($sortUrl('company')); ?>" class="inline-flex items-center gap-1 hover:text-indigo-600">Company / Title<span class="text-indigo-600"><?php echo e($sortArrow('company')); ?></span></a></th>
<th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell w-24"><a href="<?php echo e($sortUrl('utm_source')); ?>" class="inline-flex items-center gap-1 hover:text-indigo-600">Source<span class="text-indigo-600"><?php echo e($sortArrow('utm_source')); ?></span></a></th>
<th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24"><a href="<?php echo e($sortUrl('status')); ?>" class="inline-flex items-center gap-1 hover:text-indigo-600">Status<span class="text-indigo-600"><?php echo e($sortArrow('status')); ?></span></a></th>
<th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell w-24"><a href="<?php echo e($sortUrl('checked_in')); ?>" class="inline-flex items-center gap-1 hover:text-indigo-600">Checked-in<span class="text-indigo-600"><?php echo e($sortArrow('checked_in')); ?></span></a></th>
<th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Badge</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__empty_1 = true; $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr class="hover:bg-gray-50/50 transition">
    <td class="px-5 py-3">
        <input type="checkbox" class="onsite-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="<?php echo e($r->id); ?>" data-name="<?php echo e($r->name); ?>">
    </td>
    <td class="px-3 py-4 max-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"><?php echo e(strtoupper(substr($r->name, 0, 1))); ?></div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($r->name); ?></p>
                <p class="text-[11px] text-gray-500 truncate"><?php echo e($r->email); ?></p>
                <?php if($r->unique_code): ?><p class="text-[10px] text-gray-400 font-mono truncate">#<?php echo e($r->unique_code); ?></p><?php endif; ?>
            </div>
        </div>
    </td>
    <td class="px-3 py-3 hidden md:table-cell max-w-0">
        <div class="min-w-0 truncate">
            <?php if($r->company): ?><p class="text-sm font-medium text-gray-800 truncate"><?php echo e($r->company); ?></p><?php endif; ?>
            <?php if($r->job_title): ?><p class="text-[11px] text-gray-500 truncate"><?php echo e($r->job_title); ?></p><?php endif; ?>
            <?php if(!$r->company && !$r->job_title): ?><span class="text-sm text-gray-400">—</span><?php endif; ?>
        </div>
    </td>
    <td class="px-3 py-3 hidden lg:table-cell max-w-0">
        <?php if($r->utm_source): ?>
            <span class="inline-flex items-center gap-1 text-[11px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full truncate max-w-full"><?php echo e($r->utm_source); ?></span>
        <?php else: ?>
            <span class="text-xs text-gray-400">Direct</span>
        <?php endif; ?>
    </td>
    <td class="px-3 py-3">
        <?php if($r->status === 'approved'): ?>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved</span>
        <?php elseif($r->status === 'rejected'): ?>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected</span>
        <?php else: ?>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending</span>
        <?php endif; ?>
    </td>
    <td id="checkin-<?php echo e($r->id); ?>" class="px-3 py-3 hidden sm:table-cell">
        <?php if($r->checked_in_at): ?>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo e($r->checked_in_at->copy()->addHours(7)->format('H:i')); ?>

            </span>
        <?php else: ?>
            <span class="text-xs text-gray-400">—</span>
        <?php endif; ?>
    </td>
    <td class="px-3 py-3 text-center">
        <div class="flex items-center justify-center gap-1.5">
            <span data-checkin-indicator="<?php echo e($r->id); ?>"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-lg <?php echo e($r->checked_in_at ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-300'); ?>"
                  title="<?php echo e($r->checked_in_at ? 'Sudah check-in' : 'Belum check-in'); ?>">
                <?php if($r->checked_in_at): ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"/><rect x="4" y="4" width="16" height="16" rx="3" fill="none"/></svg>
                <?php else: ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3" fill="none" stroke-width="2"/></svg>
                <?php endif; ?>
            </span>
            <button onclick="printOne(<?php echo e($r->id); ?>, this)"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Kirim badge ke printer (MQTT)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="<?php echo e(route('admin.onsite.badges', ['ids' => $r->id])); ?>" target="_blank"
               class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" title="Preview badge di browser">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
        </div>
    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="7" class="text-center py-16 text-gray-400">No participants found for this filter.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<?php if($registrants->hasPages()): ?>
<div class="px-5 py-4 border-t border-gray-100"><?php echo e($registrants->links()); ?></div>
<?php endif; ?>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/onsite/_table.blade.php ENDPATH**/ ?>