<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check-ins — <?php echo e($agendum->title); ?> — <?php echo e(config('app.name')); ?></title>
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
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.agenda.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Agenda
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-lg font-bold text-gray-900">Check-ins — <?php echo e($agendum->title); ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.agenda.scan', $agendum)); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scan QR
            </a>
            <a href="<?php echo e(route('admin.agenda.visitors.export-csv', $agendum)); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Check-in Records</h2>
                <p class="text-xs text-gray-500">Total: <strong><?php echo e($visits->total()); ?></strong> check-in(s)</p>
            </div>
        </div>

        <?php if($visits->isEmpty()): ?>
            <div class="px-5 py-16 text-center text-gray-400 text-sm"><p>No check-ins yet for this session.</p></div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Phone</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Company</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Checked In At</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-400"><?php echo e($visits->firstItem() + $i); ?></span></td>
                            <td class="px-4 py-3.5">
                                <a href="<?php echo e(route('admin.registrants.show', $visit->registrant)); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    <?php echo e($visit->registrant->display_name ?: $visit->registrant->name); ?>

                                </a>
                            </td>
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-600"><?php echo e($visit->registrant->email); ?></span></td>
                            <td class="px-4 py-3.5 hidden md:table-cell"><span class="text-sm text-gray-600"><?php echo e($visit->registrant->phone ?? '—'); ?></span></td>
                            <td class="px-4 py-3.5 hidden lg:table-cell"><span class="text-sm text-gray-600"><?php echo e($visit->registrant->company ?? '—'); ?></span></td>
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-600"><?php echo e($visit->visited_at ? $visit->visited_at->format('d M Y, H:i') : '—'); ?></span></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100"><?php echo e($visits->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
</main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/agenda/visitors.blade.php ENDPATH**/ ?>