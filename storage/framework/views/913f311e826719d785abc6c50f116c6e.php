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
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('admin.workshops.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshop</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Registrants: <?php echo e($workshop->title); ?></h1>
    </div>
    <form action="<?php echo e(route('admin.workshops.send-reminder', $workshop)); ?>" method="POST" class="inline"
          onsubmit="return confirm('Send Workshop Gentle Reminder to all approved registrants?')">
        <?php echo csrf_field(); ?>
        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl bg-fuchsia-500 text-white hover:bg-fuchsia-600 shadow-sm shadow-fuchsia-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Send Reminder
        </button>
    </form>
</div></header>
<div class="p-4 sm:p-6 lg:p-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500"><?php echo e($workshop->date->format('d M Y')); ?> • <?php echo e($workshop->timeRange()); ?> • <?php echo e($workshop->room ?? '—'); ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Total registrants: <strong><?php echo e($registrants->count()); ?></strong></p>
        </div>
        <?php if($registrants->isEmpty()): ?>
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No registrants yet.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Company</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Status</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900"><?php echo e($r->display_name); ?></p></td>
                                <td class="px-5 py-4"><span class="text-sm text-gray-600"><?php echo e($r->email); ?></span></td>
                                <td class="px-5 py-4 hidden sm:table-cell"><span class="text-sm text-gray-600"><?php echo e($r->company ?? '—'); ?></span></td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <?php if($r->status === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> <?php echo e(ucfirst($r->status)); ?></span>
                                    <?php endif; ?>
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
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/workshops/registrants.blade.php ENDPATH**/ ?>