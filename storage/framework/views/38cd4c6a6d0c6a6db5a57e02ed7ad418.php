<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrants: <?php echo e($track->title); ?> — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
        <a href="<?php echo e(route('admin.tracks.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Tracks</a>
        <span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900 truncate"><?php echo e($track->title); ?></h1>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php if(session('success')): ?>
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6"><?php echo session('success'); ?></div>
    <?php endif; ?>

    <?php $firstAgenda = $track->agendaItems()->first(); ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div><p class="text-xs text-gray-400 uppercase">Time</p><p class="text-sm font-semibold text-gray-900"><?php echo e($firstAgenda ? $firstAgenda->timeLabel() : '—'); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase">Room</p><p class="text-sm font-semibold text-gray-900"><?php echo e($firstAgenda?->room ?? '—'); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase">Capacity</p><p class="text-sm font-semibold text-gray-900"><?php echo e($firstAgenda?->capacity ?: 'Unlimited'); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase">Approved</p><p class="text-sm font-bold text-indigo-600"><?php echo e($allRegistrants->where('pivot.status','approved')->count()); ?></p></div>
            <div><p class="text-xs text-gray-400 uppercase">Status</p>
                <?php if($firstAgenda): ?>
                    <?php if($firstAgenda->registration_open): ?><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Open</span><?php else: ?><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Closed</span><?php endif; ?>
                <?php else: ?>
                    <span class="text-sm text-gray-400">—</span>
                <?php endif; ?></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <div><h2 class="text-base font-bold text-gray-900">Registrants</h2><p class="text-xs text-gray-500">Total: <?php echo e($allRegistrants->count()); ?></p></div>
            <div class="flex gap-3 text-xs">
                <span>App: <strong class="text-emerald-600"><?php echo e($allRegistrants->where('pivot.status','approved')->count()); ?></strong></span>
                <span>Pend: <strong class="text-amber-600"><?php echo e($allRegistrants->where('pivot.status','pending')->count()); ?></strong></span>
                <span>Rej: <strong class="text-red-600"><?php echo e($allRegistrants->where('pivot.status','rejected')->count()); ?></strong></span>
            </div>
        </div>
        <?php if($allRegistrants->isEmpty()): ?>
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No registrants yet.</div>
        <?php else: ?>
        <div class="overflow-x-auto"><table class="w-full">
            <thead><tr class="bg-gray-50/80">
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Company</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Agenda Item</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <?php if (! (Auth::user()->isClient())): ?>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                <?php endif; ?>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__currentLoopData = $allRegistrants->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $ws = $r->pivot->status ?? 'pending'; ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3"><span class="text-sm text-gray-400"><?php echo e($i+1); ?></span></td>
                    <td class="px-4 py-3"><a href="<?php echo e(route('admin.registrants.show', $r)); ?>" class="text-sm font-semibold text-indigo-600 hover:underline"><?php echo e($r->display_name); ?></a></td>
                    <td class="px-4 py-3"><span class="text-sm text-gray-600"><?php echo e($r->email); ?></span></td>
                    <td class="px-4 py-3 hidden md:table-cell"><span class="text-sm text-gray-600"><?php echo e($r->company ?? '—'); ?></span></td>
                    <td class="px-4 py-3"><span class="text-xs text-gray-600"><?php echo e($r->agenda_item_title ?? '—'); ?></span></td>
                    <td class="px-4 py-3 text-center">
                        <?php if($ws==='approved'): ?><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Approved</span>
                        <?php elseif($ws==='rejected'): ?><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Rejected</span>
                        <?php else: ?><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span><?php endif; ?>
                    </td>
                    <?php if (! (Auth::user()->isClient())): ?>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-1">
                            <?php if($ws==='pending'): ?>
                                <form action="<?php echo e(route('admin.tracks.registrants.approve', [$track, $r->id])); ?>" method="POST"><?php echo csrf_field(); ?><input type="hidden" name="agenda_item_id" value="<?php echo e($r->agenda_item_id); ?>"><button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100" title="Approve">✓</button></form>
                                <button onclick="showReject(<?php echo e($r->id); ?>,'<?php echo e(e($r->display_name)); ?>',<?php echo e($r->agenda_item_id); ?>)" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100" title="Reject">✕</button>
                            <?php elseif($ws==='approved'): ?>
                                <button onclick="showReject(<?php echo e($r->id); ?>,'<?php echo e(e($r->display_name)); ?>',<?php echo e($r->agenda_item_id); ?>)" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100" title="Reject">✕</button>
                            <?php elseif($ws==='rejected'): ?>
                                <form action="<?php echo e(route('admin.tracks.registrants.approve', [$track, $r->id])); ?>" method="POST"><?php echo csrf_field(); ?><input type="hidden" name="agenda_item_id" value="<?php echo e($r->agenda_item_id); ?>"><button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100" title="Approve">✓</button></form>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table></div>
        <?php endif; ?>
    </div>
</div>
</main>
</div>

<div id="rejectModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;width:100%;max-width:440px;padding:24px;">
    <h3 class="text-lg font-bold text-gray-900 mb-2">Reject Registration</h3>
    <p class="text-sm text-gray-500 mb-4">Reject <strong id="rejectName"></strong>?</p>
    <form id="rejectForm" method="POST"><?php echo csrf_field(); ?>
        <input type="hidden" name="agenda_item_id" id="rejectAgendaId">
        <textarea name="admin_notes" required rows="3" placeholder="Reason..." class="w-full px-3 py-2 border rounded-lg text-sm mb-4"></textarea>
        <div class="flex gap-2">
            <button type="button" onclick="closeReject()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg">Reject</button>
        </div>
    </form>
  </div>
</div>
<script>
function showReject(id,name,agendaId){
    document.getElementById('rejectName').textContent=name;
    document.getElementById('rejectAgendaId').value=agendaId;
    document.getElementById('rejectForm').action='<?php echo e(route('admin.tracks.registrants.reject', [$track, '__ID__'])); ?>'.replace('__ID__',id);
    document.getElementById('rejectModal').style.display='flex';
}
function closeReject(){document.getElementById('rejectModal').style.display='none';}
document.getElementById('rejectModal').addEventListener('click',function(e){if(e.target===this)closeReject();});
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/tracks/registrants.blade.php ENDPATH**/ ?>