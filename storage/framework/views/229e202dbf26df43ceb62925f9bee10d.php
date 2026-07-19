<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Logs — <?php echo e(config('app.name')); ?></title>
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
                    <h1 class="text-lg font-bold text-gray-900">Email Logs</h1>
                    <p class="text-xs text-gray-500">All email sending history</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('admin.email-logs.export-csv', request()->query())); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">

            
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($totalSent); ?></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sent</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e($totalSuccess); ?></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Failed</p>
                    <p class="text-2xl font-bold text-red-600 mt-1"><?php echo e($totalFailed); ?></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bounced</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1"><?php echo e($totalBounced); ?></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Unique Recipients</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1"><?php echo e($uniqueRecipients); ?></p>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email Type</label>
                        <select name="type" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All types</option>
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($info['label']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All statuses</option>
                            <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>Sent</option>
                            <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>Failed</option>
                            <option value="bounced" <?php echo e(request('status') == 'bounced' ? 'selected' : ''); ?>>Bounced</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From date</label>
                        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                               class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To date</label>
                        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                               class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                               placeholder="Email / Name / Subject"
                               class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500 w-48">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 transition shadow-sm shadow-indigo-200">
                            Filter
                        </button>
                        <a href="<?php echo e(route('admin.email-logs.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Recipient</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $color = \App\Models\EmailTemplate::typeColor($log->template_type); $label = \App\Models\EmailTemplate::typeLabel($log->template_type); ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        <?php echo e($log->sent_at ? $log->sent_at->format('d M H:i:s') : '-'); ?>

                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-700 border border-<?php echo e($color); ?>-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-<?php echo e($color); ?>-500"></span>
                                            <?php echo e($label); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($log->recipient_name ?: '-'); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo e($log->recipient_email); ?></div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 max-w-xs truncate"><?php echo e($log->subject); ?></td>
                                    <td class="px-5 py-4 text-center">
                                        <?php if($log->status === 'sent'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sent
                                            </span>
                                        <?php elseif($log->status === 'bounced'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200" title="<?php echo e($log->error_message); ?>">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Bounced
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="<?php echo e($log->error_message); ?>">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="<?php echo e(route('admin.email-logs.show', $log)); ?>"
                                               class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 transition">
                                                Detail
                                            </a>
                                            <form action="<?php echo e(route('admin.email-logs.resend', $log)); ?>" method="POST" class="inline"
                                                  onsubmit="return confirm('Resend this email to <?php echo e(addslashes($log->recipient_email)); ?>?')">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                                                    Resend
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <p class="text-gray-400 font-medium">No logs yet</p>
                                            <p class="text-xs text-gray-400">Logs will appear after emails are sent.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <?php echo e($logs->withQueryString()->links()); ?>

            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/email-logs/index.blade.php ENDPATH**/ ?>