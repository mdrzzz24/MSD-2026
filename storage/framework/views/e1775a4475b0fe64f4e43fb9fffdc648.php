<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda Registrants — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <h1 class="text-lg font-bold text-gray-900">
                <?php if(isset($track)): ?> Track Registrants: <?php echo e($track->title); ?> <?php else: ?> Agenda Registrants <?php endif; ?>
            </h1>
            <p class="text-xs text-gray-500">
                <?php if(isset($track)): ?> Agenda sessions linked to this track <?php else: ?> Track & Workshop registrations from agenda <?php endif; ?>
            </p>
        </div>
        <?php if(isset($track)): ?>
        <a href="<?php echo e(route('admin.tracks.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Back to Tracks</a>
        <?php endif; ?>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php if(session('success')): ?>
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6"><?php echo session('success'); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead><tr class="bg-gray-50/80">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Session</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Capacity</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Approved</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Pending</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900"><?php echo e($item->title); ?></p></td>
                    <td class="px-5 py-4">
                        <?php
                            $type = $item->agenda_type
                                ?: ($item->category === 'workshop' ? 'workshop' : null)
                                ?: ($item->track_id ? 'track' : null)
                                ?: ($item->workshop_id ? 'workshop' : null)
                                ?: 'session';
                        ?>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($type==='workshop'?'bg-amber-50 text-amber-700':'bg-indigo-50 text-indigo-700'); ?>"><?php echo e(ucfirst($type)); ?></span>
                    </td>
                    <td class="px-5 py-4"><span class="text-sm text-gray-600"><?php echo e($item->timeLabel()); ?></span></td>
                    <td class="px-5 py-4 text-center"><span class="text-sm text-gray-600"><?php echo e($item->capacity > 0 ? $item->capacity : '∞'); ?></span></td>
                    <td class="px-5 py-4 text-center"><span class="text-sm font-bold text-emerald-600"><?php echo e($item->approved_count); ?></span></td>
                    <td class="px-5 py-4 text-center"><span class="text-sm font-bold <?php echo e($item->pending_count > 0 ? 'text-amber-600' : 'text-gray-400'); ?>"><?php echo e($item->pending_count); ?></span></td>
                    <td class="px-5 py-4 text-center">
                        <a href="<?php echo e(route('admin.agenda-registrants.detail', $item)); ?>" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">View</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No registrable agenda items yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</main>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/agenda-registrants/index.blade.php ENDPATH**/ ?>