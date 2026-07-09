<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Rooms &amp; Floors — <?php echo e(config('app.name')); ?></title>
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
                <div><h1 class="text-lg font-bold text-gray-900">Rooms &amp; Floors</h1><p class="text-xs text-gray-500">Manage floors and their rooms</p></div>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8">
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-4 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form action="<?php echo e(route('admin.rooms.floor.store')); ?>" method="POST" class="flex items-end gap-2">
            <?php echo csrf_field(); ?>
            <div class="flex-1"><label class="block text-xs font-semibold text-gray-600 mb-1">New Floor Name</label><input type="text" name="name" required placeholder="e.g. Third Floor" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Order</label><input type="number" name="order" value="0" min="0" class="w-20 px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 font-medium">+ Add Floor</button>
        </form>
    </div>

    
    <?php $__empty_1 = true; $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
        
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50/80 border-b">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-gray-900"><?php echo e($floor->name); ?></span>
                <span class="text-xs text-gray-400">(<?php echo e($floor->rooms->count()); ?> rooms)</span>
            </div>
            <div class="flex items-center gap-2">
                
                <form action="<?php echo e(route('admin.rooms.floor.update', $floor)); ?>" method="POST" class="flex items-center gap-1">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="text" name="name" value="<?php echo e($floor->name); ?>" required class="w-28 px-2 py-1 text-xs border border-gray-200 rounded-lg">
                    <input type="number" name="order" value="<?php echo e($floor->order); ?>" min="0" class="w-14 px-2 py-1 text-xs border border-gray-200 rounded-lg">
                    <button class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition font-medium">Save</button>
                </form>
                <form action="<?php echo e(route('admin.rooms.floor.destroy', $floor)); ?>" method="POST" onsubmit="return confirm('Delete floor &quot;<?php echo e($floor->name); ?>&quot;? Rooms will be unassigned.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">× Floor</button>
                </form>
            </div>
        </div>

        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    <th class="px-5 py-2">Room Name</th>
                    <th class="px-5 py-2">Order</th>
                    <th class="px-5 py-2 text-center">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_2 = true; $__currentLoopData = $floor->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-2.5 text-sm font-medium text-gray-800"><?php echo e($r->name); ?></td>
                        <td class="px-5 py-2.5 text-sm text-gray-500"><?php echo e($r->order); ?></td>
                        <td class="px-5 py-2.5 text-center">
                            <div class="flex justify-center gap-1">
                                <form action="<?php echo e(route('admin.rooms.update', $r)); ?>" method="POST" class="flex items-center gap-1">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <input type="text" name="name" value="<?php echo e($r->name); ?>" required class="w-24 px-2 py-1 text-xs border border-gray-200 rounded-lg">
                                    <input type="hidden" name="floor_id" value="<?php echo e($floor->id); ?>">
                                    <input type="number" name="order" value="<?php echo e($r->order); ?>" min="0" class="w-14 px-2 py-1 text-xs border border-gray-200 rounded-lg">
                                    <button class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition">Save</button>
                                </form>
                                <form action="<?php echo e(route('admin.rooms.destroy', $r)); ?>" method="POST" onsubmit="return confirm('Delete room &quot;<?php echo e($r->name); ?>&quot;?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">×</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400 text-sm">No rooms in this floor. Add one below.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="px-5 py-3 border-t border-dashed border-gray-200 bg-gray-50/30">
            <form action="<?php echo e(route('admin.rooms.store')); ?>" method="POST" class="flex items-center gap-2">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="floor_id" value="<?php echo e($floor->id); ?>">
                <input type="text" name="name" required placeholder="Room name..." class="flex-1 px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <input type="number" name="order" value="<?php echo e($floor->rooms->count() + 1); ?>" min="0" class="w-16 px-2 py-1.5 text-xs border border-gray-200 rounded-lg">
                <button class="px-3 py-1.5 text-xs font-medium bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition">+ Add Room</button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-400">
        No floors yet. Create one above.
    </div>
    <?php endif; ?>

    
    <?php if($roomsWithoutFloor->isNotEmpty()): ?>
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 shadow-sm overflow-hidden mt-4">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <span class="text-sm font-bold text-gray-500">Unassigned Rooms</span>
            <span class="text-xs text-gray-400 ml-2">(<?php echo e($roomsWithoutFloor->count()); ?>)</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y">
                <?php $__currentLoopData = $roomsWithoutFloor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-2.5 text-sm"><?php echo e($r->name); ?></td>
                    <td class="px-5 py-2.5 text-sm text-gray-500"><?php echo e($r->order); ?></td>
                    <td class="px-5 py-2.5 text-center">
                        <form action="<?php echo e(route('admin.rooms.update', $r)); ?>" method="POST" class="flex items-center gap-1 justify-center">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="text" name="name" value="<?php echo e($r->name); ?>" class="w-24 px-2 py-1 text-xs border rounded-lg">
                            <select name="floor_id" class="px-2 py-1 text-xs border rounded-lg">
                                <option value="">— Floor —</option>
                                <?php $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($fl->id); ?>"><?php echo e($fl->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input type="number" name="order" value="<?php echo e($r->order); ?>" class="w-14 px-2 py-1 text-xs border rounded-lg">
                            <button class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg">Save</button>
                        </form>
                        <form action="<?php echo e(route('admin.rooms.destroy', $r)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg">×</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 shadow-sm p-4 mt-4">
        <h3 class="text-xs font-semibold text-gray-500 mb-2">Add Standalone Room (no floor)</h3>
        <form action="<?php echo e(route('admin.rooms.store')); ?>" method="POST" class="flex items-end gap-2">
            <?php echo csrf_field(); ?>
            <div><input type="text" name="name" required placeholder="Room name" class="px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            <div><input type="number" name="order" value="0" min="0" class="w-20 px-3 py-2 border border-gray-200 rounded-xl text-sm"></div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">Add Room</button>
        </form>
    </div>
</div>
</main>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/rooms/index.blade.php ENDPATH**/ ?>