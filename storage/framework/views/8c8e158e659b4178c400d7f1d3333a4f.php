<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Detail — <?php echo e(config('app.name')); ?></title>
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
                <div class="flex items-center gap-4">
                    <a href="<?php echo e(route('admin.email-logs.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Logs
                    </a>
                    <span class="text-gray-300">/</span>
                    <h1 class="text-lg font-bold text-gray-900">Log Detail</h1>
                </div>
                <form action="<?php echo e(route('admin.email-logs.resend', $emailLog)); ?>" method="POST" class="inline"
                      onsubmit="return confirm('Resend this email to <?php echo e(addslashes($emailLog->recipient_email)); ?>?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition">
                        📧 Resend
                    </button>
                </form>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                
                <div class="p-6 border-b border-gray-100">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sent At</p>
                            <p class="text-sm font-medium text-gray-900 mt-1"><?php echo e($emailLog->sent_at ? $emailLog->sent_at->format('d F Y H:i:s') : '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</p>
                            <?php if($emailLog->status === 'sent'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sent
                                </span>
                            <?php elseif($emailLog->status === 'bounced'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Bounced
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Template Type</p>
                            <?php $color = \App\Models\EmailTemplate::typeColor($emailLog->template_type); $label = \App\Models\EmailTemplate::typeLabel($emailLog->template_type); ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-700 border border-<?php echo e($color); ?>-200 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-<?php echo e($color); ?>-500"></span> <?php echo e($label); ?>

                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Template</p>
                            <p class="text-sm font-medium text-gray-900 mt-1"><?php echo e($emailLog->template?->name ?? '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Recipient</p>
                            <p class="text-sm font-medium text-gray-900 mt-1"><?php echo e($emailLog->recipient_name ?: '-'); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($emailLog->recipient_email); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Subject</p>
                            <p class="text-sm font-medium text-gray-900 mt-1"><?php echo e($emailLog->subject); ?></p>
                        </div>
                    </div>
                    <?php if($emailLog->error_message): ?>
                        <div class="mt-4 p-3 bg-red-50 rounded-xl border border-red-200">
                            <p class="text-xs font-semibold text-red-500 uppercase tracking-wider">Error</p>
                            <p class="text-sm text-red-700 mt-1"><?php echo e($emailLog->error_message); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Email Content</p>
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex items-center gap-2">
                            <button onclick="document.getElementById('htmlPreview').style.display='block';document.getElementById('rawPreview').style.display='none';this.classList.add('bg-indigo-100','text-indigo-700');document.getElementById('rawBtn').classList.remove('bg-indigo-100','text-indigo-700')"
                                    class="px-3 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-700 transition" id="htmlBtn">Preview</button>
                            <button onclick="document.getElementById('rawPreview').style.display='block';document.getElementById('htmlPreview').style.display='none';this.classList.add('bg-indigo-100','text-indigo-700');document.getElementById('htmlBtn').classList.remove('bg-indigo-100','text-indigo-700')"
                                    class="px-3 py-1 text-xs font-medium rounded-lg text-gray-600 hover:bg-gray-200 transition" id="rawBtn">Raw HTML</button>
                        </div>
                        <div id="htmlPreview" class="p-4 max-h-[500px] overflow-y-auto">
                            <?php echo $emailLog->html_content ?? '<p class="text-gray-400 text-sm">No HTML content stored.</p>'; ?>

                        </div>
                        <div id="rawPreview" class="p-4 max-h-[500px] overflow-y-auto" style="display:none;">
                            <pre class="text-xs text-gray-700 whitespace-pre-wrap break-all"><?php echo e($emailLog->html_content ?? 'No HTML content stored.'); ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('rawBtn').classList.remove('bg-indigo-100', 'text-indigo-700');
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/email-logs/show.blade.php ENDPATH**/ ?>