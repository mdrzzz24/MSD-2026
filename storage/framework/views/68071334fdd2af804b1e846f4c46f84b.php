<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unbalanced Data — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<?php
    $reasonLabels = [
        'approve_vs_rejected'  => ['Approved by client, but status = Rejected', 'bg-red-100 text-red-700'],
        'reject_vs_approved'   => ['Rejected by client, but status = Approved', 'bg-amber-100 text-amber-700'],
        'waitlist_vs_status'   => ['Waiting List, but status = Approved/Rejected', 'bg-orange-100 text-orange-700'],
        'flag_not_waitlist'    => ['Waiting List flag ON, but action is not waiting list', 'bg-purple-100 text-purple-700'],
        'waitlist_flag_false'  => ['Action is Waiting List, but flag is OFF', 'bg-purple-100 text-purple-700'],
    ];
?>
<div class="flex min-h-screen">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Dashboard
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Unbalanced Data</h1>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">
            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-2xl flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <p class="text-sm leading-relaxed">
                    Registrants where the <strong>actual status</strong> (Registrants page) contradicts the
                    <strong>client recommendation</strong> (Regist Confirmation page), or where the
                    <strong>waiting-list flag</strong> disagrees with the recommendation.
                </p>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', ['reason' => 'all'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-red-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($reasonFilter === 'all' ? 'ring-2 ring-red-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">Total Unbalanced</span>
                        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-red-600"><?php echo e($stats['total']); ?></p>
                    <p class="text-xs text-red-400 mt-1">All inconsistencies</p>
                </a>
                <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', ['reason' => 'status'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-amber-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($reasonFilter === 'status' ? 'ring-2 ring-amber-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Status Conflict</span>
                        <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-amber-700"><?php echo e($stats['status']); ?></p>
                    <p class="text-xs text-amber-500 mt-1">Status vs recommendation</p>
                </a>
                <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', ['reason' => 'waitlist_flag'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-purple-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($reasonFilter === 'waitlist_flag' ? 'ring-2 ring-purple-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Waitlist Flag</span>
                        <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-purple-700"><?php echo e($stats['waitlist_flag']); ?></p>
                    <p class="text-xs text-purple-500 mt-1">Waitlist flag mismatch</p>
                </a>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Unbalanced Registrants</h3>
                            <p class="text-xs text-gray-500">Inconsistent status / recommendation / waitlist flag</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:ml-auto">
                            <form method="GET" action="<?php echo e(route('admin.regist-confirmation.unbalanced')); ?>" class="flex items-center gap-2 mr-1">
                                <input type="hidden" name="reason" value="<?php echo e($reasonFilter); ?>">
                                <div class="relative">
                                    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search name / email…"
                                           class="w-52 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-1.5 pl-3 pr-8 transition">
                                    <?php if($search !== ''): ?>
                                        <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', array_merge(request()->except(['search', 'page']), ['reason' => $reasonFilter]))); ?>"
                                           class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Clear search">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Search
                                </button>
                            </form>
                            <div class="flex items-center gap-1.5 text-xs">
                                <span class="text-gray-400 font-medium">Reason:</span>
                                <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', array_merge(request()->except(['reason', 'page']), ['reason' => 'all']))); ?>" class="px-2.5 py-1 rounded-lg font-semibold transition <?php echo e($reasonFilter === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">All</a>
                                <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', array_merge(request()->except(['reason', 'page']), ['reason' => 'status']))); ?>" class="px-2.5 py-1 rounded-lg font-semibold transition <?php echo e($reasonFilter === 'status' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'); ?>">Status Conflict</a>
                                <a href="<?php echo e(route('admin.regist-confirmation.unbalanced', array_merge(request()->except(['reason', 'page']), ['reason' => 'waitlist_flag']))); ?>" class="px-2.5 py-1 rounded-lg font-semibold transition <?php echo e($reasonFilter === 'waitlist_flag' ? 'bg-purple-500 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100'); ?>">Waitlist Flag</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Client Recommendation</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reason (unbalanced)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_2 = true; $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-4 max-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-400 to-pink-500 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                                <?php echo e(strtoupper(substr($r->name, 0, 1))); ?>

                                            </div>
                                            <div class="min-w-0 truncate">
                                                <a href="<?php echo e(route('admin.registrants.show', $r)); ?>" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition truncate block"><?php echo e($r->name); ?></a>
                                                <p class="text-[11px] text-gray-500 truncate"><?php echo e($r->email); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <?php if($r->status === 'approved'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Approved</span>
                                        <?php elseif($r->status === 'rejected'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">Rejected</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] <?php echo e($r->client_remark_action === 'approve' ? 'text-emerald-600' : ($r->client_remark_action === 'reject' ? 'text-red-600' : 'text-orange-600')); ?>">
                                                <?php if($r->client_remark_action === 'approve'): ?>
                                                    ✅ Approve
                                                <?php elseif($r->client_remark_action === 'reject'): ?>
                                                    ❌ Reject
                                                <?php else: ?>
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Waiting List
                                                <?php endif; ?>
                                                <?php if($r->clientRemarkedBy): ?>
                                                    by <?php echo e($r->clientRemarkedBy->name); ?>

                                                <?php endif; ?>
                                            </span>
                                            <?php if($r->client_remark): ?>
                                                <span class="text-[10px] text-gray-500"><?php echo e($r->client_remark); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <?php $reasons = $r->unbalancedReasons(); ?>
                                            <?php $__empty_3 = true; $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_3 = false; ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold <?php echo e($reasonLabels[$key][1] ?? 'bg-gray-100 text-gray-600'); ?>"><?php echo e($reasonLabels[$key][0] ?? $key); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_3): ?>
                                                <span class="text-[10px] text-gray-400">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <tr>
                                    <td colspan="4" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <p class="text-gray-400 font-medium">No unbalanced data 🎉</p>
                                            <p class="text-xs text-gray-400">All statuses match the client recommendations</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($registrants->hasPages()): ?>
                    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                        <?php echo e($registrants->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/regist-confirmation-unbalanced.blade.php ENDPATH**/ ?>