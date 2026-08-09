<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booths — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <style>.truncate-cell{max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}</style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Booths</h1>
            <p class="text-xs text-gray-500">Manage exhibition booths & track visitor visits</p>
        </div>
        <div class="flex items-center gap-2">
            <?php if(Auth::user()->canWrite() && Auth::user()->hasPermission('booths')): ?>
            <a href="<?php echo e(route('admin.booths.create')); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Booth
            </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Total Booths</p>
            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($booths->count()); ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Active</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e($booths->where('is_active', true)->count()); ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Total Visits</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1"><?php echo e($totalVisits); ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Inactive</p>
            <p class="text-2xl font-bold text-gray-400 mt-1"><?php echo e($booths->where('is_active', false)->count()); ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">All Booths</h2>
        </div>

        <?php if($booths->isEmpty()): ?>
            <div class="px-5 py-16 text-center text-gray-400 text-sm">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <p>No booths yet. Create your first booth to get started.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Description</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Visits</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Scan QR</th>
                        <?php if(Auth::user()->canWrite()): ?>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        <?php endif; ?>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $booths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $booth): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-400"><?php echo e($i + 1); ?></span></td>
                            <td class="px-4 py-3.5"><span class="text-sm font-semibold text-gray-900"><?php echo e($booth->name); ?></span></td>
                            <td class="px-4 py-3.5 hidden lg:table-cell"><span class="text-sm text-gray-600 truncate-cell block"><?php echo e($booth->description ?? '—'); ?></span></td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if($booth->is_active): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if(Auth::user()->hasPermission('booth_visits')): ?>
                                <a href="<?php echo e(route('admin.booths.visitors', $booth)); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    <?php echo e($booth->visits_count ?? 0); ?>

                                </a>
                                <?php else: ?>
                                    <span class="text-sm text-gray-600"><?php echo e($booth->visits_count ?? 0); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if(Auth::user()->hasPermission('booth_visits')): ?>
                                <a href="<?php echo e(route('admin.booths.scan', $booth)); ?>" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Scan
                                </a>
                                <?php endif; ?>
                            </td>
                            <?php if(Auth::user()->canWrite()): ?>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="<?php echo e(route('admin.booths.edit', $booth)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="<?php echo e(route('admin.booths.toggle', $booth)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg <?php echo e($booth->is_active ? 'bg-gray-50 text-gray-600 hover:bg-gray-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'); ?> transition" title="<?php echo e($booth->is_active ? 'Deactivate' : 'Activate'); ?>">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('admin.booths.destroy', $booth)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete booth <?php echo e($booth->name); ?>? This will also delete all visit records.')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
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
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/booths/index.blade.php ENDPATH**/ ?>