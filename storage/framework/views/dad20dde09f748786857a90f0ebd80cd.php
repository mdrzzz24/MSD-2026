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
            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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

                            
                            <div class="border-t border-gray-100 pt-4 mt-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <p class="text-sm font-bold text-gray-800">General Login Password (Emergency)</p>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">Used by committee to help registrants who forgot their password on event day.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">General Password</label>
                                        <input type="text" name="general_password" value="<?php echo e($config['general_password']); ?>" placeholder="Set a shared password" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div class="flex items-end pb-2.5">
                                        <p class="text-xs text-gray-400">Leave empty to disable. Any registrant can log in using this password + their email.</p>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="border-t border-gray-100 pt-4 mt-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <p class="text-sm font-bold text-gray-800">Bounce Email Detection (IMAP)</p>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">System will check this mailbox to detect bounced emails.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">IMAP Host</label>
                                        <input type="text" name="bounce_host" value="<?php echo e($config['bounce_host']); ?>" placeholder="mail.indomarketservices.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">IMAP Port</label>
                                        <input type="text" name="bounce_port" value="<?php echo e($config['bounce_port']); ?>" placeholder="993" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                                        <input type="text" name="bounce_username" value="<?php echo e($config['bounce_username']); ?>" placeholder="bounce@indomarketservices.com" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                                        <input type="password" name="bounce_password" value="<?php echo e($config['bounce_password']); ?>" placeholder="••••••••" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mailbox</label>
                                        <input type="text" name="bounce_mailbox" value="<?php echo e($config['bounce_mailbox']); ?>" placeholder="INBOX" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-3">
                                    <form action="<?php echo e(route('admin.bounce-check.run')); ?>" method="POST" onsubmit="return confirm('Run bounce check now?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            🔄 Run Bounce Check Now
                                        </button>
                                    </form>
                                    <p class="text-xs text-gray-400">Atau jalankan <code class="bg-gray-100 px-1 rounded">php artisan email:check-bounces</code> via terminal.</p>
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
                            <li>• Configuration saved to <code class="bg-amber-100 px-1 rounded">.env</code> file</li>
                            <li>• Use <strong>App Password</strong> for Gmail (not regular password)</li>
                            <li>• Common ports: <strong>587</strong> (TLS), <strong>465</strong> (SSL)</li>
                            <li>• After saving, always <strong>test</strong> sending email</li>
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