<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrants: <?php echo e($workshop->name ?: $workshop->title); ?> — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <style>.truncate-cell{max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}</style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.workshops.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Workshops
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-lg font-bold text-gray-900 truncate"><?php echo e($workshop->name ?: $workshop->title); ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.workshops.registrants.export-csv', $workshop)); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php $linkedAgenda = $workshop->agendaItems->first(); ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Time</p><p class="text-sm font-semibold text-gray-900"><?php echo e($workshop->timeRange() !== '—' ? $workshop->timeRange() : ($linkedAgenda ? $linkedAgenda->timeLabel() : '—')); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Room</p><p class="text-sm font-semibold text-gray-900"><?php echo e($workshop->room ?? $linkedAgenda?->room ?? '—'); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Capacity</p><p class="text-sm font-semibold text-gray-900"><?php echo e($workshop->capacity > 0 ? $workshop->capacity : 'Unlimited'); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Approved</p><p class="text-sm font-bold text-indigo-600"><?php echo e($registrants->where('pivot.status', 'approved')->count()); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Status</p>
                <?php if($workshop->registration_open): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Open</span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Closed</span>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Last Reminder</p>
                <?php if($lastReminderLog): ?>
                    <p class="text-sm font-semibold text-gray-900"><?php echo e($lastReminderLog->sent_at->format('d M Y, H:i:s')); ?></p>
                    <p class="text-xs text-gray-400"><?php echo e($lastReminderLog->sent_at->diffForHumans()); ?></p>
                <?php else: ?>
                    <p class="text-sm text-gray-400">—</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <form id="reminderForm" method="POST" action="<?php echo e(route('admin.workshops.send-reminder', $workshop)); ?>"
          onsubmit="return confirmReminder()">
        <?php echo csrf_field(); ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
            <div><h2 class="text-base font-bold text-gray-900">Registrant List</h2><p class="text-xs text-gray-500">Total: <strong><?php echo e($registrants->count()); ?></strong> registrant(s)</p></div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">App: <strong class="text-emerald-600"><?php echo e($registrants->where('pivot.status', 'approved')->count()); ?></strong></span>
                <span class="text-xs text-gray-400">Pend: <strong class="text-amber-600"><?php echo e($registrants->where('pivot.status', 'pending')->count()); ?></strong></span>
                <span class="text-xs text-gray-400">Rej: <strong class="text-red-600"><?php echo e($registrants->where('pivot.status', 'rejected')->count()); ?></strong></span>
            </div>
        </div>
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-3">
            <button type="button" onclick="toggleAll(true)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">Select All</button>
            <button type="button" onclick="toggleAll(false)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Deselect All</button>
            <span class="text-xs text-gray-400" id="selectedCount">0 selected</span>
            <button type="submit" id="sendReminderBtn"
                    class="ml-auto inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-fuchsia-500 text-white hover:bg-fuchsia-600 shadow-sm shadow-fuchsia-200 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Send Reminder
            </button>
        </div>

        <?php if($registrants->isEmpty()): ?>
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No registrants yet for this workshop.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase w-10">
                            <input type="checkbox" onchange="toggleAll(this.checked)" class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                        </th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase w-48">Email</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell w-32">Phone</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell w-36">Company</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden xl:table-cell w-36">Job Title</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-28">WS Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-24">Reg Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-24">Check-in</th>
                        <?php if(Auth::user()->canWrite()): ?>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-24">Action</th>
                        <?php endif; ?>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $wsStatus = $r->pivot->status ?? 'pending'; ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3.5">
                                    <input type="checkbox" name="registrant_ids[]" value="<?php echo e($r->id); ?>"
                                           class="cb-item w-4 h-4 rounded border-gray-300 text-indigo-600"
                                           onchange="updateReminderCount()"
                                           <?php echo e($wsStatus !== 'approved' ? 'disabled' : ''); ?>>
                                </td>
                                <td class="px-4 py-3.5 max-w-0"><a href="<?php echo e(route('admin.registrants.show', $r)); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline truncate block" title="<?php echo e($r->display_name); ?>"><?php echo e($r->display_name); ?></a></td>
                                <td class="px-4 py-3.5 max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->email); ?>"><?php echo e($r->email); ?></span></td>
                                <td class="px-4 py-3.5 hidden md:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->phone ?? ''); ?>"><?php echo e($r->phone ?? '—'); ?></span></td>
                                <td class="px-4 py-3.5 hidden lg:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->company ?? ''); ?>"><?php echo e($r->company ?? '—'); ?></span></td>
                                <td class="px-4 py-3.5 hidden xl:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->job_title ?? ''); ?>"><?php echo e($r->job_title ?? '—'); ?></span></td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($wsStatus === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span><span class="truncate">Approved</span></span>
                                    <?php elseif($wsStatus === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="<?php echo e($r->pivot->admin_notes ?? ''); ?>"><span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span><span class="truncate">Rejected</span></span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span><span class="truncate">Pending</span></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($r->status === 'approved'): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><span class="truncate">Approved</span></span>
                                    <?php elseif($r->status === 'rejected'): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><span class="truncate">Rejected</span></span>
                                    <?php else: ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><span class="truncate">Pending</span></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($r->checked_in_at): ?>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600" title="<?php echo e($r->checked_in_at->format('d M Y H:i')); ?>">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?php echo e($r->checked_in_at->format('H:i')); ?>

                                        </span>
                                    <?php else: ?><span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user()->canWrite()): ?>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <?php if($wsStatus === 'pending'): ?>
                                            <form action="<?php echo e(route('admin.workshops.registrants.approve', [$workshop, $r->id])); ?>" method="POST" class="inline"><?php echo csrf_field(); ?><button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition" title="Approve">✓</button></form>
                                            <button onclick="showRejectModal(<?php echo e($r->id); ?>,'<?php echo e(e($r->display_name)); ?>')" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Reject">✕</button>
                                        <?php elseif($wsStatus === 'approved'): ?>
                                            <?php if(Auth::user()->isSuperAdmin()): ?>
                                            <button onclick="showRejectModal(<?php echo e($r->id); ?>,'<?php echo e(e($r->display_name)); ?>')" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Reject">✕</button>
                                            <?php endif; ?>
                                        <?php elseif($wsStatus === 'rejected'): ?>
                                            <form action="<?php echo e(route('admin.workshops.registrants.approve', [$workshop, $r->id])); ?>" method="POST" class="inline"><?php echo csrf_field(); ?><button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition" title="Approve">✓</button></form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    </form>
</div>


<div id="rejectModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);width:100%;max-width:400px;overflow:hidden;">
    <form id="rejectForm" method="POST">
      <?php echo csrf_field(); ?>
      <div style="padding:24px;text-align:center;">
        <div style="width:48px;height:48px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
          <svg style="width:24px;height:24px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h3 style="font-size:18px;font-weight:700;color:#111827;margin-bottom:4px;">Reject Registration</h3>
        <p style="font-size:14px;color:#6b7280;margin-bottom:16px;">Reject <strong id="rejectName"></strong>'s workshop registration? A rejection email will be sent.</p>
        <div style="display:flex;gap:8px;margin-top:16px;">
          <button type="button" onclick="closeRejectModal()" style="flex:1;padding:10px 0;background:#f3f4f6;color:#374151;font-weight:600;font-size:14px;border:none;border-radius:12px;cursor:pointer;">Cancel</button>
          <button type="submit" style="flex:1;padding:10px 0;background:#ef4444;color:#fff;font-weight:600;font-size:14px;border:none;border-radius:12px;cursor:pointer;">Reject</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function toggleAll(checked) {
    document.querySelectorAll('.cb-item:not(:disabled)').forEach(cb => cb.checked = checked);
    document.querySelector('thead input[type="checkbox"]').checked = checked;
    updateReminderCount();
}
function updateReminderCount() {
    var count = document.querySelectorAll('.cb-item:checked').length;
    document.getElementById('selectedCount').textContent = count + ' selected';
    document.getElementById('sendReminderBtn').disabled = count === 0;
}
function confirmReminder() {
    var count = document.querySelectorAll('.cb-item:checked').length;
    if (count === 0) {
        alert('Please select at least one registrant.');
        return false;
    }
    return confirm('Send Workshop Gentle Reminder to ' + count + ' selected registrant(s)?');
}
function showRejectModal(registrantId, name) {
    document.getElementById('rejectName').textContent = name;
    document.getElementById('rejectForm').action = '<?php echo e(route('admin.workshops.registrants.reject', [$workshop, '__ID__'])); ?>'.replace('__ID__', registrantId);
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

</main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/workshop-registrants/detail.blade.php ENDPATH**/ ?>