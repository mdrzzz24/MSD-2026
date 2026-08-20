<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UTM Registrants: <?php echo e($utmLink->name); ?> — <?php echo e(config('app.name')); ?></title>
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
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.workshops.utm-links')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Workshop UTM Links
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-lg font-bold text-gray-900 truncate"><?php echo e($utmLink->name); ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.workshops.utm-links.registrants-export', $utmLink)); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="col-span-2 sm:col-span-1">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Workshop</p>
                <p class="text-sm font-semibold text-gray-900"><?php echo e($workshop ? ($workshop->name ?: $workshop->title) : '—'); ?></p>
                <?php if($utmLink->track): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200 mt-1">🎵 <?php echo e($utmLink->track->name); ?></span>
                <?php endif; ?>
            </div>
            <div class="col-span-2 sm:col-span-2">
                <p class="text-xs text-gray-400 uppercase tracking-wider">UTM Parameters</p>
                <div class="flex flex-wrap gap-1 mt-1">
                    <span class="text-[10px] font-medium bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">source:<?php echo e($utmLink->utm_source); ?></span>
                    <span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">medium:<?php echo e($utmLink->utm_medium); ?></span>
                    <span class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">campaign:<?php echo e($utmLink->utm_campaign); ?></span>
                    <?php if($utmLink->utm_content): ?><span class="text-[10px] font-medium bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded">content:<?php echo e($utmLink->utm_content); ?></span><?php endif; ?>
                </div>
                <p class="text-[11px] text-gray-400 font-mono mt-1 break-all"><?php echo e($utmLink->full_url); ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total</p>
                <p class="text-sm font-bold text-gray-900"><?php echo e($registrants->count()); ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Approved</p>
                <p class="text-sm font-bold text-emerald-600"><?php echo e($registrants->where('pivot_status', 'approved')->count()); ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Pending</p>
                <p class="text-sm font-bold text-amber-600"><?php echo e($registrants->where('pivot_status', 'pending')->count()); ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Rejected</p>
                <p class="text-sm font-bold text-red-600"><?php echo e($registrants->where('pivot_status', 'rejected')->count()); ?></p>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
            <div><h2 class="text-base font-bold text-gray-900">Registrants from this UTM link</h2><p class="text-xs text-gray-500">Registrations attributed to <strong><?php echo e($utmLink->name); ?></strong></p></div>
        </div>

        <?php if($registrants->isEmpty()): ?>
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No registrations attributed to this UTM link yet.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase w-48">Email</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell w-32">Phone</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell w-36">Company</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden xl:table-cell w-32">Job Title</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-24">Track</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-28">WS Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-24">Reg Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-32">Joined Workshop</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase w-24">Check-in</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $wsStatus = $r->pivot_status; ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3.5 max-w-0"><a href="<?php echo e(route('admin.registrants.show', $r)); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline truncate block" title="<?php echo e($r->display_name); ?>"><?php echo e($r->display_name); ?></a></td>
                                <td class="px-4 py-3.5 max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->email); ?>"><?php echo e($r->email); ?></span></td>
                                <td class="px-4 py-3.5 hidden md:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->phone ?? ''); ?>"><?php echo e($r->phone ?? '—'); ?></span></td>
                                <td class="px-4 py-3.5 hidden lg:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->company ?? ''); ?>"><?php echo e($r->company ?? '—'); ?></span></td>
                                <td class="px-4 py-3.5 hidden xl:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="<?php echo e($r->job_title ?? ''); ?>"><?php echo e($r->job_title ?? '—'); ?></span></td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($r->registered_track_name): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700 border border-teal-200"><?php echo e($r->registered_track_name); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($wsStatus === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>Approved</span>
                                    <?php elseif($wsStatus === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="<?php echo e($r->pivot_admin_notes ?? ''); ?>"><span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>Rejected</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($r->status === 'approved'): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Approved</span>
                                    <?php elseif($r->status === 'rejected'): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                                    <?php else: ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($r->pivot_created_at): ?>
                                        <span class="text-xs text-gray-600" title="<?php echo e($r->pivot_created_at->copy()->addHours(7)->format('d M Y, H:i')); ?> WIB"><?php echo e($r->pivot_created_at->copy()->addHours(7)->format('d M Y')); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <?php if($r->checked_in_at): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ <?php echo e(\Carbon\Carbon::parse($r->checked_in_at)->copy()->addHours(7)->format('H:i')); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
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
<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/workshops/utm-link-registrants.blade.php ENDPATH**/ ?>