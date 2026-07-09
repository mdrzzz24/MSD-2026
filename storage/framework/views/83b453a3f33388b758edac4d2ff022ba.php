<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Scan — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <?php if(session('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-4 text-sm">
                <?php echo session('success'); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl mb-4 text-sm">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
                    <?php echo e(strtoupper(substr($registrant->name, 0, 1))); ?>

                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900"><?php echo e($registrant->name); ?></h2>
                    <p class="text-xs text-gray-500"><?php echo e($registrant->email); ?></p>
                </div>
                <div class="ml-auto">
                    <?php if($registrant->checked_in_at): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Checked In
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Not Checked In
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Company</p>
                        <p class="text-sm font-semibold text-gray-900"><?php echo e($registrant->company ?? '—'); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Job Title</p>
                        <p class="text-sm font-semibold text-gray-900"><?php echo e($registrant->job_title ?? '—'); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Status</p>
                        <p class="text-sm font-semibold capitalize text-gray-900"><?php echo e($registrant->status); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Unique Code</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono"><?php echo e($registrant->unique_code ?? '—'); ?></p>
                    </div>
                </div>

                <?php if($registrant->checked_in_at): ?>
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        <p class="text-sm font-semibold text-emerald-700">✓ Checked in at <?php echo e($registrant->checked_in_at->format('H:i, d M Y')); ?></p>
                    </div>
                <?php elseif($registrant->isApproved()): ?>
                    <form action="<?php echo e(route('registrant.qr-checkin', $registrant->qr_token)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm">
                            ✓ Confirm Check-In
                        </button>
                    </form>
                <?php else: ?>
                    <div class="bg-red-50 rounded-xl p-4 text-center">
                        <p class="text-sm font-semibold text-red-600">Registrant has not been approved yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4">MSD 2026 — Registration System</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/qr-scan.blade.php ENDPATH**/ ?>