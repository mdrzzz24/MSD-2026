<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(isset($group) ? 'Edit' : 'Create'); ?> Group — <?php echo e(config('app.name')); ?></title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="<?php echo e(route('admin.management.groups.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Groups
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900"><?php echo e(isset($group) ? 'Edit Group' : 'Create Group'); ?></h1>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8 max-w-2xl">
            <?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl mb-6">
                    <ul class="text-sm list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <form action="<?php echo e(isset($group) ? route('admin.management.groups.update', $group) : route('admin.management.groups.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php if(isset($group)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Group Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $group->name ?? '')); ?>" required placeholder="e.g. Event Partners"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Permissions</label>
                        <p class="text-xs text-gray-400 mb-3">Members of this group will inherit these permissions.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php
                                $currentPerms = old('permissions', []);
                                if (empty($currentPerms) && isset($group)) {
                                    $currentPerms = $group->permissions ?? [];
                                }
                            ?>
                            <?php $__currentLoopData = $permissionList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 cursor-pointer transition border border-transparent hover:border-gray-100">
                                    <input type="checkbox" name="permissions[<?php echo e($key); ?>]" value="1"
                                           <?php echo e(is_array($currentPerms) && isset($currentPerms[$key]) && $currentPerms[$key] ? 'checked' : ''); ?>

                                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-800"><?php echo e($label); ?></span>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 font-bold text-sm tracking-wide"
                            style="background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;border-radius:999px;border:none;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:transform 0.25s,box-shadow 0.25s;"
                            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,0.5)'"
                            onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,0.35)'">
                        <?php echo e(isset($group) ? 'Update Group' : 'Create Group'); ?>

                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/groups/form.blade.php ENDPATH**/ ?>