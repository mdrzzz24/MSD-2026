<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Logs: <?php echo e($template->name); ?> — <?php echo e(config('app.name')); ?></title>
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
                <a href="<?php echo e(route('admin.templates.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Templates
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Logs: <?php echo e($template->name); ?></h1>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="mb-4 flex items-center gap-3">
                <?php $color = \App\Models\EmailTemplate::typeColor($template->type); ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-700 border border-<?php echo e($color); ?>-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-<?php echo e($color); ?>-500"></span>
                    <?php echo e(\App\Models\EmailTemplate::typeLabel($template->type)); ?>

                </span>
                <span class="text-xs text-gray-400">Subject: "<?php echo e($template->subject); ?>"</span>
                <span class="text-xs text-gray-400 ml-auto"><?php echo e($logs->total()); ?> total</span>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50/50 transition cursor-pointer" onclick="showEmail(<?php echo e($log->id); ?>)">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($log->sent_at->format('d M Y, H:i')); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo e($log->sent_at->diffForHumans()); ?></p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($log->recipient_name); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo e($log->recipient_email); ?></p>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-gray-600"><?php echo e($log->subject); ?></td>
                                    <td class="px-5 py-3.5 text-center">
                                        <?php if($log->status === 'sent'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sent
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="<?php echo e($log->error_message); ?>">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-gray-400 font-medium">No email logs yet</p>
                                            <p class="text-xs text-gray-400">Emails sent using this template will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($logs->hasPages()): ?>
                    <div class="px-5 py-3 border-t border-gray-100"><?php echo e($logs->links()); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>


<div id="emailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm" onclick="closeModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col mx-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900" id="modalSubject">Email Preview</h3>
            <button onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 bg-gray-100">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-2 bg-gray-100 border-b border-gray-200 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    <span class="text-xs text-gray-500 ml-2" id="modalRecipient"></span>
                </div>
                <div id="emailContent" class="p-0 overflow-auto" style="max-height: 55vh;"></div>
            </div>
        </div>
    </div>
</div>


<script>
    const emailLogs = <?php echo json_encode($logs->getCollection()->map(fn($l) => [
        'id'        => $l->id,
        'subject'   => $l->subject,
        'recipient' => $l->recipient_name . ' <' . $l->recipient_email . '>',
        'html'      => $l->html_content,
        'status'    => $l->status,
    ])->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;

    function showEmail(id) {
        const log = emailLogs.find(l => l.id === id);
        if (!log || !log.html) return;
        document.getElementById('modalSubject').textContent = log.subject;
        document.getElementById('modalRecipient').textContent = log.recipient;
        document.getElementById('emailContent').innerHTML = log.html;
        document.getElementById('emailModal').classList.remove('hidden');
        document.getElementById('emailModal').classList.add('flex');
    }

    function closeModal(e) {
        if (e && e.target !== e.currentTarget) return;
        document.getElementById('emailModal').classList.add('hidden');
        document.getElementById('emailModal').classList.remove('flex');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/templates/logs.blade.php ENDPATH**/ ?>