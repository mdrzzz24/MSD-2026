<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Walk-in Registered — <?php echo e(config('app.name')); ?></title>
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
        <a href="<?php echo e(route('admin.walkin.form')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Walk-in
        </a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Registration Complete</h1>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Registered Successfully!</h2>
            <p class="text-sm text-gray-500 mb-6"><?php echo e($registrant->name); ?> has been registered and checked in.</p>

            
            <div class="bg-gray-50 rounded-xl p-4 text-left text-sm space-y-2 mb-6">
                <div class="flex justify-between"><span class="text-gray-400">Name</span><span class="font-semibold text-gray-900"><?php echo e($registrant->name); ?></span></div>
                <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-gray-700"><?php echo e($registrant->email); ?></span></div>
                <?php if($registrant->phone): ?><div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-gray-700"><?php echo e($registrant->phone); ?></span></div><?php endif; ?>
                <?php if($registrant->company): ?><div class="flex justify-between"><span class="text-gray-400">Company</span><span class="text-gray-700"><?php echo e($registrant->company); ?></span></div><?php endif; ?>
                <?php if($registrant->job_title): ?><div class="flex justify-between"><span class="text-gray-400">Job Title</span><span class="text-gray-700"><?php echo e($registrant->job_title); ?></span></div><?php endif; ?>
                <div class="flex justify-between"><span class="text-gray-400">Unique Code</span><span class="font-mono text-sm font-semibold text-indigo-600"><?php echo e($registrant->unique_code); ?></span></div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 inline-block">
                <img src="<?php echo e($registrant->qr_code_url); ?>" alt="QR Code" class="w-48 h-48 mx-auto" id="qrCodeImg">
            </div>

            
            <div class="bg-gray-50 rounded-xl p-3 mb-6">
                <p class="text-xs text-gray-400 mb-1">QR Check-in URL</p>
                <p class="text-sm font-mono text-indigo-600 break-all"><?php echo e($registrant->qr_checkin_url); ?></p>
            </div>

            
            <div class="flex gap-3">
                <a href="<?php echo e(route('admin.walkin.form')); ?>" class="flex-1 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition text-sm text-center">
                    Register Another
                </a>
                <a href="<?php echo e(route('admin.registrants.show', $registrant)); ?>" class="flex-1 py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm text-center">
                    View Detail
                </a>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<script>
// Auto-print QR code or allow download
function printQr() {
    const img = document.getElementById('qrCodeImg');
    const win = window.open('', '_blank');
    win.document.write('<html><head><title>QR Code - <?php echo e($registrant->name); ?></title></head><body style="text-align:center;padding:40px;"><h2><?php echo e($registrant->name); ?></h2><img src="'+img.src+'" style="width:300px;"><p><?php echo e($registrant->unique_code); ?></p></body></html>');
    win.document.close();
    win.print();
}
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/walkin-result.blade.php ENDPATH**/ ?>