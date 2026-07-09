<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workshop Registrants — <?php echo e(config('app.name')); ?></title>
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
            <h1 class="text-lg font-bold text-gray-900">Workshop Registrants</h1>
            <p class="text-xs text-gray-500">View registrants for each workshop</p>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    <?php if(session('success')): ?>
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm"><?php echo session('success'); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl mb-6">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">All Workshops</h2>
                <p class="text-xs text-gray-500">Click a workshop to view its registrants</p>
            </div>
            <div class="text-xs text-gray-400">Total: <strong><?php echo e($workshops->count()); ?></strong> workshops</div>
        </div>

        <?php if($workshops->isEmpty()): ?>
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No workshops available.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Workshop</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Linked Agenda</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Approved</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Pending</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Waitlist</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($w->title); ?></p>
                                    <?php if(!$w->registration_open): ?>
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Registration Closed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <?php $linked = $w->agendaItems; ?>
                                    <?php if($linked->isNotEmpty()): ?>
                                        <?php $__currentLoopData = $linked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mb-1"><?php echo e($ai->title); ?> (<?php echo e($ai->timeLabel()); ?>)</span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold <?php echo e($w->approved_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-400'); ?>">
                                        <?php echo e($w->approved_count); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if($w->pending_count > 0): ?>
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-amber-50 text-amber-700"><?php echo e($w->pending_count); ?></span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if($w->waitlist_count > 0): ?>
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-amber-50 text-amber-700"><?php echo e($w->waitlist_count); ?></span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="<?php echo e(route('admin.workshops.registrants', $w)); ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/workshop-registrants/index.blade.php ENDPATH**/ ?>