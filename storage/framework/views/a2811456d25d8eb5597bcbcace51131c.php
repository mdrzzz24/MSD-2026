<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Floors — <?php echo e(config('app.name')); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans">
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div><h1 class="text-xl font-bold text-gray-900">Floors</h1><p class="text-sm text-gray-500">Manage floor groupings (e.g. Second Floor, First Floor)</p></div>
        <a href="<?php echo e(route('admin.agenda.index')); ?>" class="text-sm text-indigo-600 hover:underline">← Back to Agenda</a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-4 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <table class="w-full">
            <thead><tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                <th class="px-4 py-3">Name</th><th class="px-4 py-3">Order</th><th class="px-4 py-3">Rooms</th><th class="px-4 py-3 text-center">Actions</th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_2 = true; $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-sm font-semibold"><?php echo e($f->name); ?></td>
                    <td class="px-4 py-3 text-sm"><?php echo e($f->order); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($f->rooms->count()); ?></td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-1">
                            <form action="<?php echo e(route('admin.floors.update', $f)); ?>" method="POST" class="flex items-center gap-1">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <input type="text" name="name" value="<?php echo e($f->name); ?>" required class="w-32 px-2 py-1 text-xs border rounded-lg">
                                <input type="number" name="order" value="<?php echo e($f->order); ?>" min="0" class="w-14 px-2 py-1 text-xs border rounded-lg">
                                <button class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">Save</button>
                            </form>
                            <form action="<?php echo e(route('admin.floors.destroy', $f)); ?>" method="POST" onsubmit="return confirm('Delete floor <?php echo e($f->name); ?>? Rooms under it will be unassigned.')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No floors.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Add New Floor</h3>
        <form action="<?php echo e(route('admin.floors.store')); ?>" method="POST" class="flex items-end gap-2">
            <?php echo csrf_field(); ?>
            <div><label class="block text-xs text-gray-500 mb-1">Name</label><input type="text" name="name" required class="px-3 py-2 border rounded-xl text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Order</label><input type="number" name="order" value="0" min="0" class="px-3 py-2 border rounded-xl text-sm w-20"></div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">Add</button>
        </form>
    </div>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/floors/index.blade.php ENDPATH**/ ?>