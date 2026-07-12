<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrants — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1 lg:ml-64">
        
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Registrants</h1>
                        <p class="text-xs text-gray-500">Manage all registrant applications</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('admin.registrants.export-csv', request()->only(['status', 'utm_source', 'utm_medium', 'utm_campaign', 'direct']))); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                    <a href="<?php echo e(route('admin.dashboard')); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php if(request('utm_source') || request('direct')): ?>
            <div class="flex items-center gap-3 bg-indigo-50 border border-indigo-200 text-indigo-800 px-5 py-3 rounded-2xl">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span class="text-sm">
                    Filtered by UTM:
                    <?php if(request('direct')): ?>
                        <strong>Direct</strong> (no UTM source)
                    <?php else: ?>
                        <strong><?php echo e(request('utm_source')); ?></strong>
                        <?php if(request('utm_medium')): ?> / <strong><?php echo e(request('utm_medium')); ?></strong> <?php endif; ?>
                        <?php if(request('utm_campaign')): ?> / <strong><?php echo e(request('utm_campaign')); ?></strong> <?php endif; ?>
                    <?php endif; ?>
                </span>
                <a href="<?php echo e(route('admin.registrants.index')); ?>" class="ml-auto text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">Clear filter</a>
            </div>
            <?php endif; ?>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <a href="<?php echo e(route('admin.registrants.index', ['status' => 'all'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($status === 'all' ? 'ring-2 ring-indigo-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</span>
                        <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900" data-stat="total"><?php echo e($total); ?></p>
                    <p class="text-xs text-gray-500 mt-1">All registrants</p>
                </a>

                <a href="<?php echo e(route('admin.registrants.index', ['status' => 'pending'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-yellow-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($status === 'pending' ? 'ring-2 ring-yellow-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Pending</span>
                        <div class="w-9 h-9 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-200 transition">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-yellow-700" data-stat="pending"><?php echo e($pending); ?></p>
                    <p class="text-xs text-yellow-500 mt-1">Awaiting review</p>
                </a>

                <a href="<?php echo e(route('admin.registrants.index', ['status' => 'approved'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($status === 'approved' ? 'ring-2 ring-emerald-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Approved</span>
                        <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-emerald-700" data-stat="approved"><?php echo e($approved); ?></p>
                    <p class="text-xs text-emerald-500 mt-1">Approved</p>
                </a>

                <a href="<?php echo e(route('admin.registrants.index', ['status' => 'rejected'])); ?>"
                   class="group bg-white rounded-2xl p-5 border border-red-100 shadow-sm hover:shadow-md transition-shadow <?php echo e($status === 'rejected' ? 'ring-2 ring-red-500' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">Rejected</span>
                        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-red-600" data-stat="rejected"><?php echo e($rejected); ?></p>
                    <p class="text-xs text-red-400 mt-1">Rejected</p>
                </a>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <h2 class="text-base font-bold text-gray-900">
                            <?php if($status === 'pending'): ?>
                                Pending Registrants
                            <?php elseif($status === 'approved'): ?>
                                Approved Registrants
                            <?php elseif($status === 'rejected'): ?>
                                Rejected Registrants
                            <?php else: ?>
                                All Registrants
                            <?php endif; ?>
                        </h2>
                        <span class="text-xs text-gray-400">(<?php echo e($registrants->total()); ?>)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        
                        <?php if(Auth::user()->canWrite()): ?>
                        <div id="bulkActions" class="hidden items-center gap-2">
                            <span class="text-xs text-gray-500" id="selectedCount">0 selected</span>
                            <button onclick="bulkApprove()"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition">
                                Approve Selected
                            </button>
                            <button onclick="openBulkRejectModal()"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                                Reject Selected
                            </button>
                        </div>
                        <?php endif; ?>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="tableSearch" placeholder="Search name or email..."
                                   class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white w-full sm:w-64 transition">
                        </div>
                    </div>
                </div>

                
                <div class="overflow-x-auto">
                    <table class="w-full" id="registrantTable">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <?php if(Auth::user()->canWrite()): ?>
                                <th class="px-5 py-3.5 text-left w-10">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <?php endif; ?>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Phone</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Job Title</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden 2xl:table-cell">Job Role</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Company</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Source</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50/50 transition search-row">
                                    <?php if(Auth::user()->canWrite()): ?>
                                    <td class="px-5 py-4">
                                        <input type="checkbox" class="registrant-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="<?php echo e($r->id); ?>">
                                    </td>
                                    <?php endif; ?>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                <?php echo e(strtoupper(substr($r->name, 0, 1))); ?>

                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('admin.registrants.show', $r)); ?>" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition search-name">
                                                    <?php echo e($r->name); ?>

                                                </a>
                                                <?php if($r->unique_code): ?>
                                                    <p class="text-xs text-gray-400">#<?php echo e($r->unique_code); ?></p>
                                                <?php endif; ?>
                                                <p class="text-xs text-gray-500 md:hidden search-email"><?php echo e($r->email); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 hidden md:table-cell">
                                        <span class="text-sm text-gray-600"><?php echo e($r->email); ?></span>
                                    </td>
                                    <td class="px-5 py-4 hidden lg:table-cell">
                                        <span class="text-sm text-gray-600"><?php echo e($r->phone ?? '—'); ?></span>
                                    </td>
                                    <td class="px-5 py-4 hidden xl:table-cell">
                                        <span class="text-sm text-gray-600"><?php echo e($r->job_title ?? '—'); ?></span>
                                    </td>
                                    <td class="px-5 py-4 hidden 2xl:table-cell">
                                        <span class="text-sm text-gray-600"><?php echo e($r->job_role ?? '—'); ?></span>
                                    </td>
                                    <td class="px-5 py-4 hidden xl:table-cell">
                                        <span class="text-sm text-gray-600"><?php echo e($r->company ?? '—'); ?></span>
                                    </td>
                                    <td class="px-5 py-4 hidden sm:table-cell">
                                        <?php if($r->utm_source): ?>
                                            <span class="inline-flex items-center gap-1 text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                <?php echo e($r->utm_source); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Direct</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <?php if($r->status === 'approved'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                            </span>
                                        <?php elseif($r->status === 'rejected'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 hidden sm:table-cell">
                                        <span class="text-sm text-gray-500"><?php echo e($r->created_at->format('d M Y')); ?></span>
                                    </td>
                                    <td class="px-5 py-4 text-center hidden sm:table-cell">
                                        <?php if($r->email_logs_count > 0): ?>
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200" title="<?php echo e($r->email_logs_count); ?> email(s) sent">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <?php echo e($r->email_logs_count); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-400" title="No emails sent">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span>0</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="<?php echo e(route('admin.registrants.show', $r)); ?>"
                                               title="View Detail"
                                               class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <?php if(Auth::user()->canWrite()): ?>
                                            <a href="<?php echo e(route('admin.registrants.edit', $r)); ?>"
                                               title="Edit"
                                               class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <?php if($r->isPending()): ?>
                                                <form action="<?php echo e(route('admin.registrants.approve', $r)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit"
                                                            onclick="return confirm('Approve <?php echo e(addslashes($r->name)); ?>?')"
                                                            title="Approve"
                                                            class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <button onclick="openRejectModal('<?php echo e($r->id); ?>', '<?php echo e(addslashes($r->name)); ?>')"
                                                        title="Reject"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                            <?php if($r->status === 'approved'): ?>
                                                <button onclick="resendCredentials('<?php echo e($r->id); ?>', '<?php echo e(addslashes($r->name)); ?>')"
                                                        title="Resend Credentials"
                                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <div class="relative inline-block" x-data="{ open: false }">

                                            </div>
                                            <form action="<?php echo e(route('admin.registrants.destroy', $r)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete <?php echo e(addslashes($r->name)); ?> permanently?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                        title="Delete"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-400 font-medium">No registrants found</p>
                                            <p class="text-xs text-gray-400">No registrants match the current filter</p>
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


<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Rejection Confirmation</h3>
                    <p class="text-xs text-gray-500">A rejection email will be sent automatically</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                You are about to reject <strong id="rejectName" class="text-red-600"></strong>'s registration.
                A rejection email will be sent automatically.
            </p>
            <form id="rejectForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="flex justify-end gap-2.5">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-500 text-white hover:bg-red-600 shadow-sm shadow-red-200 transition">Yes, Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="bulkRejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Bulk Rejection</h3>
                    <p class="text-xs text-gray-500">Reject <span id="bulkRejectCount" class="font-bold">0</span> selected registrants</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Reject <strong id="bulkRejectCount" class="font-bold text-red-600">0</strong> selected registrants?
                A rejection email will be sent automatically to each.
            </p>
            <form id="bulkRejectForm" method="POST" action="<?php echo e(route('admin.registrants.bulk-reject')); ?>">
                <?php echo csrf_field(); ?>
                <div id="bulkRejectIds"></div>
                <div class="flex justify-end gap-2.5">
                    <button type="button" onclick="closeBulkRejectModal()"
                            class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-500 text-white hover:bg-red-600 shadow-sm shadow-red-200 transition">Yes, Reject All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
    // ---- Reject Modal (Single) ----
    function openRejectModal(id, name) {
        document.getElementById('rejectName').textContent = name;
        document.getElementById('rejectForm').action = '/admin/registrants/' + id + '/reject';
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ---- Bulk Reject Modal ----
    function openBulkRejectModal() {
        const checked = document.querySelectorAll('.registrant-checkbox:checked');
        if (checked.length === 0) {
            alert('Please select at least one registrant.');
            return;
        }
        document.getElementById('bulkRejectCount').textContent = checked.length;
        const container = document.getElementById('bulkRejectIds');
        container.innerHTML = '';
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        const modal = document.getElementById('bulkRejectModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeBulkRejectModal() {
        const modal = document.getElementById('bulkRejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('bulk_admin_notes').value = '';
    }

    // ---- Bulk Approve ----
    function bulkApprove() {
        const checked = document.querySelectorAll('.registrant-checkbox:checked');
        if (checked.length === 0) {
            alert('Please select at least one registrant.');
            return;
        }
        if (!confirm('Approve ' + checked.length + ' selected registrant(s)?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("admin.registrants.bulk-approve")); ?>';
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrf);

        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // ---- Resend Credentials ----
    function resendCredentials(id, name) {
        if (!confirm('Resend credentials to ' + name + '?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/registrants/' + id + '/resend-credentials';
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }

    // ---- Select All ----
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.registrant-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('registrant-checkbox')) {
            updateBulkActions();
        }
    });
    function updateBulkActions() {
        const checked = document.querySelectorAll('.registrant-checkbox:checked').length;
        const el = document.getElementById('bulkActions');
        const count = document.getElementById('selectedCount');
        if (checked > 0) {
            el.classList.remove('hidden');
            el.classList.add('flex');
            count.textContent = checked + ' selected';
        } else {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }
    }

    // ---- Table Search ----
    document.getElementById('tableSearch')?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.search-row').forEach(row => {
            const name = (row.querySelector('.search-name')?.textContent || '').toLowerCase();
            const email = (row.querySelector('.search-email')?.textContent || '').toLowerCase();
            row.style.display = name.includes(query) || email.includes(query) ? '' : 'none';
        });
    });

    // ---- Close modals on Esc ----
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejectModal();
            closeBulkRejectModal();
        }
    });

    // ---- Mobile Sidebar ----
    function toggleSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const isOpen = sidebar.classList.contains('-translate-x-full');
        if (isOpen) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
    document.getElementById('sidebarToggle')?.addEventListener('click', toggleSidebar);
    document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .realtime-updated { animation: pulse-green 0.6s ease-out; }
    @keyframes pulse-green {
        0% { background-color: rgba(16, 185, 129, 0.3); }
        100% { background-color: transparent; }
    }
</style>


<script>
(function(){
    var pollUrl = '<?php echo e(route("admin.dashboard.data")); ?>';
    setInterval(function() {
        fetch(pollUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.querySelectorAll('[data-stat]').forEach(function(el) {
                    var key = el.getAttribute('data-stat');
                    var val = data[key];
                    if (val === undefined) return;
                    var oldVal = el.textContent.trim();
                    var numVal = Number(val);
                    var oldNum = Number(oldVal);
                    if (!isNaN(numVal) && oldNum !== numVal) {
                        el.textContent = numVal;
                        el.classList.remove('realtime-updated');
                        void el.offsetWidth;
                        el.classList.add('realtime-updated');
                    }
                });
            })
            .catch(function() {});
    }, 10000);
})();
</script>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/registrants/index.blade.php ENDPATH**/ ?>