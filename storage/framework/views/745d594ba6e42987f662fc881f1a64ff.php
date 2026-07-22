<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Workshops — <?php echo e(config('app.name')); ?></title>
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
        <div><h1 class="text-lg font-bold text-gray-900">Manage Workshops</h1><p class="text-xs text-gray-500">Manage schedules, open/close registration</p></div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.workshop-registrants.export-csv')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <?php if(Auth::user()->canWrite()): ?>
            <a href="<?php echo e(route('admin.workshops.create')); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Create Workshop</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50/80">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Workshop</th>
                    <?php if(Auth::user()->canWrite()): ?>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Linked Agenda</th>
                    <?php endif; ?>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Registrants</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900"><?php echo e($w->name ?: $w->title); ?></p>
                                <?php if($w->name): ?><p class="text-xs text-gray-500 mt-0.5"><?php echo e($w->title); ?></p><?php endif; ?>
                                <?php if($w->description): ?><p class="text-xs text-gray-400 mt-0.5 truncate max-w-[250px]"><?php echo e($w->description); ?></p><?php endif; ?>
                            </td>
                            <?php if(Auth::user()->canWrite()): ?>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                <?php $linked = $w->agendaItems; ?>
                                <?php if($linked->isNotEmpty()): ?>
                                    <?php $__currentLoopData = $linked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mb-1"><?php echo e($ai->title); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <div class="flex items-center gap-2 text-xs">
                                    <a href="<?php echo e(route('admin.workshops.registrants', $w)); ?>" class="font-bold text-indigo-600 hover:text-indigo-800">
                                        <?php echo e(($w->approved_count ?? 0) + ($w->pending_count ?? 0) + ($w->rejected_count ?? 0)); ?> total
                                    </a>
                                    <span class="text-emerald-600">✓<?php echo e($w->approved_count ?? 0); ?></span>
                                    <span class="text-amber-600">⏳<?php echo e($w->pending_count ?? 0); ?></span>
                                    <span class="text-red-500">✕<?php echo e($w->rejected_count ?? 0); ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <?php if($w->registration_open): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Open</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Closed</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-1.5">
                                    <a href="<?php echo e(route('admin.workshops.registrants', $w)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">View</a>
                                    <?php if(Auth::user()->canWrite()): ?>
                                    <a href="<?php echo e(route('admin.workshops.edit', $w)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition">Edit</a>
                                    <a href="<?php echo e(route('admin.workshops.invitations', $w)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition">Invite</a>
                                    <form action="<?php echo e(route('admin.workshops.toggle', $w)); ?>" method="POST"><?php echo csrf_field(); ?>
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg <?php echo e($w->registration_open ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200'); ?> transition">
                                            <?php echo e($w->registration_open ? 'Close' : 'Open'); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('admin.workshops.destroy', $w)); ?>" method="POST" onsubmit="return confirm('Delete workshop <?php echo e($w->title); ?>?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Delete</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-5 py-16 text-center"><p class="text-gray-400 font-medium">No workshops yet</p><p class="text-xs text-gray-400">Create your first workshop</p></td></tr>
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
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/workshops/index.blade.php ENDPATH**/ ?>