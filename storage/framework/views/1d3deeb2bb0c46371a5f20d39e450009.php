<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Time Slots — <?php echo e(config('app.name')); ?></title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8">
                <div><h1 class="text-lg font-bold text-gray-900">Time Slots</h1><p class="text-xs text-gray-500">Manage schedule rows</p></div>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <table class="w-full">
            <thead><tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                <th class="px-4 py-3">Start</th><th class="px-4 py-3">End</th><th class="px-4 py-3">Order</th><th class="px-4 py-3 text-center">Actions</th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-sm font-mono"><?php echo e($s->start_time); ?></td>
                    <td class="px-4 py-3 text-sm font-mono"><?php echo e($s->end_time); ?></td>
                    <td class="px-4 py-3 text-sm"><?php echo e($s->order); ?></td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-1">
                            <form action="<?php echo e(route('admin.time-slots.update', $s)); ?>" method="POST" class="flex items-center gap-1">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <input type="time" name="start_time" value="<?php echo e($s->start_time); ?>" required class="w-24 px-2 py-1 text-xs border rounded-lg">
                                <input type="time" name="end_time" value="<?php echo e($s->end_time); ?>" required class="w-24 px-2 py-1 text-xs border rounded-lg">
                                <input type="number" name="order" value="<?php echo e($s->order); ?>" min="0" class="w-14 px-2 py-1 text-xs border rounded-lg">
                                <button class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">Save</button>
                            </form>
                            <form action="<?php echo e(route('admin.time-slots.destroy', $s)); ?>" method="POST" onsubmit="return confirm('Delete this time slot?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No time slots.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Add New Time Slot</h3>
        <form action="<?php echo e(route('admin.time-slots.store')); ?>" method="POST" class="flex items-end gap-2">
            <?php echo csrf_field(); ?>
            <div><label class="block text-xs text-gray-500 mb-1">Start</label><input type="time" name="start_time" required class="px-3 py-2 border rounded-xl text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">End</label><input type="time" name="end_time" required class="px-3 py-2 border rounded-xl text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Order</label><input type="number" name="order" value="0" min="0" class="px-3 py-2 border rounded-xl text-sm w-20"></div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">Add</button>
        </form>
    </div>
</div>
</main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/time-slots/index.blade.php ENDPATH**/ ?>