<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mail Settings — <?php echo e(config('app.name')); ?></title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8">
                <h1 class="text-lg font-bold text-gray-900">⚙️ Mail Configuration</h1>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            <?php if(session('success')): ?>
                <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm"><?php echo session('success'); ?></span>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm"><?php echo session('error'); ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-base font-bold text-gray-900">SMTP Configuration</h2>
                            <p class="text-xs text-gray-500">Configure outgoing email server settings</p>
                        </div>
                        <form action="<?php echo e(route('admin.mail-settings.update')); ?>" method="POST" class="p-6 space-y-4">
                            <?php echo csrf_field(); ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mailer</label>
                                    <select name="mailer" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                        <option value="smtp" <?php echo e($config['mailer'] === 'smtp' ? 'selected' : ''); ?>>SMTP</option>
                                        <option value="ses" <?php echo e($config['mailer'] === 'ses' ? 'selected' : ''); ?>>Amazon SES</option>
                                        <option value="mailgun" <?php echo e($config['mailer'] === 'mailgun' ? 'selected' : ''); ?>>Mailgun</option>
                                        <option value="postmark" <?php echo e($config['mailer'] === 'postmark' ? 'selected' : ''); ?>>Postmark</option>
                                        <option value="sendmail" <?php echo e($config['mailer'] === 'sendmail' ? 'selected' : ''); ?>>Sendmail</option>
                                        <option value="log" <?php echo e($config['mailer'] === 'log' ? 'selected' : ''); ?>>Log Only</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Encryption</label>
                                    <select name="encryption" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                        <option value="tls" <?php echo e($config['encryption'] === 'tls' ? 'selected' : ''); ?>>TLS</option>
                                        <option value="ssl" <?php echo e($config['encryption'] === 'ssl' ? 'selected' : ''); ?>>SSL</option>
                                        <option value="null" <?php echo e($config['encryption'] === 'null' || !$config['encryption'] ? 'selected' : ''); ?>>None</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">SMTP Host</label>
                                    <input type="text" name="host" value="<?php echo e($config['host']); ?>" placeholder="smtp.gmail.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">SMTP Port</label>
                                    <input type="text" name="port" value="<?php echo e($config['port']); ?>" placeholder="587" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username / Email</label>
                                    <input type="text" name="username" value="<?php echo e($config['username']); ?>" placeholder="noreply@example.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password / App Password</label>
                                    <input type="password" name="password" value="<?php echo e($config['password']); ?>" placeholder="••••••••" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">From Address <span class="text-red-500">*</span></label>
                                    <input type="email" name="from_address" value="<?php echo e($config['from_address']); ?>" required placeholder="noreply@metrodatasolutionday.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">From Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="from_name" value="<?php echo e($config['from_name']); ?>" required placeholder="Metrodata Solution Day 2026" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">
                                💾 Save Configuration
                            </button>
                        </form>
                    </div>
                </div>

                
                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">🧪 Test Email</h3>
                        </div>
                        <form action="<?php echo e(route('admin.mail-settings.test')); ?>" method="POST" class="p-5 space-y-3">
                            <?php echo csrf_field(); ?>
                            <input type="email" name="test_email" placeholder="Enter test email address…" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <button type="submit" class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                🚀 Send Test Email
                            </button>
                        </form>
                    </div>

                    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5">
                        <h3 class="text-sm font-bold text-amber-800 mb-2">ℹ️ Info</h3>
                        <ul class="text-xs text-amber-700 space-y-1.5">
                            <li>• Konfigurasi disimpan ke file <code class="bg-amber-100 px-1 rounded">.env</code></li>
                            <li>• Gunakan <strong>App Password</strong> untuk Gmail (bukan password biasa)</li>
                            <li>• Port umum: <strong>587</strong> (TLS), <strong>465</strong> (SSL)</li>
                            <li>• Setelah save, selalu <strong>test</strong> kirim email</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">📊 Current Status</h3>
                        </div>
                        <div class="p-5 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Mailer</span>
                                <span class="font-semibold text-gray-900"><?php echo e($config['mailer']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Host</span>
                                <span class="font-semibold text-gray-900"><?php echo e($config['host'] ?: '—'); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">From</span>
                                <span class="font-semibold text-gray-900"><?php echo e($config['from_address']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Templates</span>
                                <span class="font-semibold text-gray-900"><?php echo e(\App\Models\EmailTemplate::count()); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Logs</span>
                                <span class="font-semibold text-gray-900"><?php echo e(\App\Models\EmailLog::count()); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/mail-settings.blade.php ENDPATH**/ ?>