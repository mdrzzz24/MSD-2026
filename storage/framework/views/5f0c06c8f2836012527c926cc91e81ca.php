<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Template Email — <?php echo e(config('app.name')); ?></title>
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
                    <h1 class="text-lg font-bold text-gray-900">Template Email</h1>
                    <p class="text-xs text-gray-500">Manage all email templates — super admin only</p>
                </div>
                <div class="flex items-center gap-2">
                    <?php $autoEmail = \Illuminate\Support\Facades\Cache::get('auto_registration_email', true); ?>
                    <form action="<?php echo e(route('admin.templates.toggle-auto-email')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl transition <?php echo e($autoEmail ? 'bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm shadow-emerald-200' : 'bg-gray-300 text-gray-600 hover:bg-gray-400'); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($autoEmail ? 'bg-white animate-pulse' : 'bg-gray-500'); ?>"></span>
                            Auto-Email: <?php echo e($autoEmail ? 'ON' : 'OFF'); ?>

                        </button>
                    </form>
                    <a href="<?php echo e(route('admin.templates.send-reminder')); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-violet-500 text-white rounded-xl hover:bg-violet-600 shadow-sm shadow-violet-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Send Reminder
                    </a>
                    <a href="<?php echo e(route('admin.templates.upload')); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 shadow-sm shadow-emerald-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload HTML
                    </a>
                    <a href="<?php echo e(route('admin.templates.create')); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Manual
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            <?php if(session('success')): ?>
                <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm"><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            
            <div class="flex flex-wrap gap-2 mb-4">
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-<?php echo e($info['color']); ?>-50 text-<?php echo e($info['color']); ?>-700 border border-<?php echo e($info['color']); ?>-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-<?php echo e($info['color']); ?>-500"></span>
                        <?php echo e($info['label']); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $color = \App\Models\EmailTemplate::typeColor($t->type); $label = \App\Models\EmailTemplate::typeLabel($t->type); ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 bg-<?php echo e($color); ?>-100 text-<?php echo e($color); ?>-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <span class="text-sm font-semibold text-gray-900"><?php echo e($t->name); ?></span>
                                                <?php if($t->description): ?>
                                                    <p class="text-xs text-gray-400"><?php echo e($t->description); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-700 border border-<?php echo e($color); ?>-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-<?php echo e($color); ?>-500"></span> <?php echo e($label); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600"><?php echo e($t->subject); ?></td>
                                    <td class="px-5 py-4">
                                        <?php if($t->is_active): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-center gap-1.5">
                                            <a href="<?php echo e(route('admin.templates.preview', $t)); ?>" target="_blank" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 transition" title="Preview">👁</a>
                                            <a href="<?php echo e(route('admin.templates.logs', $t)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition" title="Logs">📋</a>
                                            <a href="<?php echo e(route('admin.templates.edit', $t)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition">Edit</a>
                                            <form action="<?php echo e(route('admin.templates.toggle', $t)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                                    <?php echo e($t->is_active ? 'Deactivate' : 'Activate'); ?>

                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.templates.destroy', $t)); ?>" method="POST" onsubmit="return confirm('Hapus template ini?')">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <p class="text-gray-400 font-medium">No templates yet</p>
                                            <p class="text-xs text-gray-400">Upload or create an email template to get started</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/templates/index.blade.php ENDPATH**/ ?>