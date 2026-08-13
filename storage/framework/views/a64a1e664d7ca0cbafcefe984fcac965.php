<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Send Reminder — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Gentle Reminder</h1>
                    <p class="text-xs text-gray-500">Send reminders to approved registrants</p>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">

            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Template Active</p>
                        <?php if($activeTemplate): ?>
                            <p class="text-xs text-gray-400 mt-0.5">"<?php echo e($activeTemplate->name); ?>" — <?php echo e($activeTemplate->subject); ?></p>
                        <?php else: ?>
                            <p class="text-xs text-amber-600 mt-0.5">No active template for Gentle Reminder. Create a template first.</p>
                        <?php endif; ?>
                    </div>
                    <?php if($activeTemplate): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inactive
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search registrants <span class="text-gray-400 font-normal">(real-time)</span></label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="searchRegistrants" placeholder="Search name / email / unique code..."
                               class="w-full pl-9 pr-8 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="button" onclick="clearSearch()" class="px-3 py-2 text-xs font-medium rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Clear</button>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('admin.email-logs.send-reminder')); ?>" id="reminderForm">
                <?php echo csrf_field(); ?>

                
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="toggleAll(true)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                            Select All
                        </button>
                        <button type="button" onclick="toggleAll(false)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                            Deselect All
                        </button>
                        <span class="text-xs text-gray-400" id="selectedCount">0 selected</span>
                    </div>
                    <button type="submit" <?php echo e(!$activeTemplate ? 'disabled' : ''); ?>

                            class="px-5 py-2.5 <?php echo e($activeTemplate ? 'bg-violet-500 hover:bg-violet-600 shadow-sm shadow-violet-200' : 'bg-gray-300 cursor-not-allowed'); ?> text-white text-sm font-semibold rounded-xl transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Send Reminder
                    </button>
                </div>

                
                <?php $__empty_1 = true; $__currentLoopData = $dateGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $remindedCount = $group->filter(fn ($r) => in_array($r->id, $remindedIds ?? [], true))->count();
                        $notRemindedCount = $group->count() - $remindedCount;
                    ?>
                    <details class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4" <?php echo e($loop->first ? 'open' : ''); ?>>
                        <summary class="px-5 py-3.5 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition select-none list-none">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-900">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                                    <?php echo e($date === 'Unknown' ? 'Unknown date' : \Carbon\Carbon::parse($date)->format('l, d M Y')); ?>

                                </span>
                                <span class="text-xs text-gray-400 grp-count"><?php echo e($group->count()); ?> registrant<?php echo e($group->count() === 1 ? '' : 's'); ?></span>
                                <span class="text-xs text-gray-300">·</span>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span class="grp-reminded"><?php echo e($remindedCount); ?></span> reminded</span>
                                <span class="text-xs text-gray-300">·</span>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span><span class="grp-not"><?php echo e($notRemindedCount); ?></span> not reminded</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="overflow-x-auto border-t border-gray-100">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50/80">
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10"></th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reminder</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $approvedViaReminder = in_array($r->id, $approveReminderIds ?? [], true);
                                            $reminderSent = in_array($r->id, $remindedIds ?? [], true);
                                            $alreadyReminded = $reminderSent || $approvedViaReminder;
                                            $reminderFailed = !$alreadyReminded && in_array($r->id, $failedReminderIds ?? [], true);
                                        ?>
                                        <tr class="hover:bg-gray-50/50 transition <?php echo e($alreadyReminded ? 'bg-emerald-50/50' : ''); ?>"
                                            data-search="<?php echo e(strtolower($r->name)); ?>|<?php echo e(strtolower($r->email)); ?>|<?php echo e(strtolower($r->unique_code ?? '')); ?>"
                                            data-reminded="<?php echo e($alreadyReminded ? '1' : '0'); ?>">
                                            <td class="px-5 py-3">
                                                <input type="checkbox" name="registrant_ids[]" value="<?php echo e($r->id); ?>"
                                                       class="cb-item w-4 h-4 rounded border-gray-300 text-indigo-600"
                                                       onchange="updateCount()" <?php echo e($alreadyReminded ? 'disabled' : ''); ?>>
                                            </td>
                                            <td class="px-5 py-3 text-sm font-medium text-gray-900"><?php echo e($r->display_name); ?></td>
                                            <td class="px-5 py-3 text-sm text-gray-600"><?php echo e($r->email); ?></td>
                                            <td class="px-5 py-3">
                                                <?php if($approvedViaReminder): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span> Approved + Reminder
                                                    </span>
                                                <?php elseif($reminderSent): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Reminded
                                                    </span>
                                                <?php elseif($reminderFailed): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="Reminder email failed to send">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Reminder Failed
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Not Reminded
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                        <div class="px-5 py-16 text-center">
                            <p class="text-gray-400 font-medium">No approved registrants yet.</p>
                        </div>
                    </div>
                <?php endif; ?>

            </form>
        </div>
    </main>
</div>

<script>
function toggleAll(checked) {
    // Skip disabled (already reminded) checkboxes
    document.querySelectorAll('.cb-item:not(:disabled)').forEach(cb => cb.checked = checked);
    updateCount();
    // Toggle header checkbox if present
    const header = document.querySelector('thead input[type="checkbox"]');
    if (header) header.checked = checked;
}
function updateCount() {
    const count = document.querySelectorAll('.cb-item:checked').length;
    document.getElementById('selectedCount').textContent = count + ' selected';
}

// ── Real-time search (client-side filter across all date groups) ──
function applyFilter() {
    const input = document.getElementById('searchRegistrants');
    const q = input ? input.value.trim().toLowerCase() : '';
    document.querySelectorAll('#reminderForm details').forEach(function(group, i) {
        let visible = 0, reminded = 0;
        group.querySelectorAll('tr[data-search]').forEach(function(tr) {
            const match = !q || (tr.getAttribute('data-search') || '').indexOf(q) !== -1;
            tr.style.display = match ? '' : 'none';
            if (match) {
                visible++;
                if (tr.getAttribute('data-reminded') === '1') reminded++;
            }
        });
        group.style.display = visible ? '' : 'none';
        const countEl = group.querySelector('.grp-count');
        if (countEl) countEl.textContent = visible + ' registrant' + (visible === 1 ? '' : 's');
        const remindedEl = group.querySelector('.grp-reminded');
        if (remindedEl) remindedEl.textContent = reminded;
        const notEl = group.querySelector('.grp-not');
        if (notEl) notEl.textContent = visible - reminded;
        // While searching, expand groups that have matches; otherwise keep first open.
        group.open = q ? visible > 0 : i === 0;
    });
    updateCount();
}
function clearSearch() {
    const input = document.getElementById('searchRegistrants');
    if (input) input.value = '';
    applyFilter();
}
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchRegistrants');
    if (input) input.addEventListener('input', applyFilter);
    applyFilter();
});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/email-logs/send-reminder.blade.php ENDPATH**/ ?>