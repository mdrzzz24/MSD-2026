<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>QR Codes — <?php echo e(config('app.name')); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div id="copyToast" class="fixed top-4 right-4 z-50 hidden items-center gap-2 bg-gray-900 text-white px-4 py-2.5 rounded-xl shadow-lg text-sm transition-opacity duration-300 opacity-0">
<svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Copied!</span>
</div>
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
<div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
<div class="flex items-center gap-4">
<button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>
<div><h1 class="text-lg font-bold text-gray-900">QR Codes</h1><p class="text-xs text-gray-500">All approved registrants with QR codes</p></div>
</div>
<div class="flex items-center gap-2">
    <a href="<?php echo e(route('admin.management.qr.export-csv')); ?>"
       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unique Code</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">QR Code</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__empty_1 = true; $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<a href="<?php echo e(route('admin.registrants.show', $r)); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"><?php echo e($r->name); ?></a>
</td>
<td class="px-5 py-4">
<div class="flex items-center gap-2">
<span class="font-mono text-sm font-bold text-gray-900 bg-gray-100 px-3 py-1.5 rounded-lg tracking-wider"><?php echo e($r->unique_code ?? '—'); ?></span>
<button onclick="copyText('<?php echo e($r->unique_code); ?>', this)" class="shrink-0 p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Copy Code">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
</button>
</div>
</td>
<td class="px-5 py-4">
<div class="flex items-center gap-2">
<img src="<?php echo e($r->qr_code_url); ?>" alt="QR" class="w-16 h-16 rounded border border-gray-200">
<button onclick="copyUrl('<?php echo e($r->qr_checkin_url); ?>', this)" class="shrink-0 p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Copy URL">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
</button>
</div>
</td>
<td class="px-5 py-4">
<?php if($r->checked_in_at): ?>
<span class="text-xs font-semibold text-emerald-600">✓ <?php echo e($r->checked_in_at->format('d M H:i')); ?></span>
<?php else: ?>
<span class="text-xs text-gray-400">—</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="4" class="text-center py-16 text-gray-400">No registrants found</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php if($registrants->hasPages()): ?>
<div class="px-5 py-4 border-t border-gray-100"><?php echo e($registrants->links()); ?></div>
<?php endif; ?>
</div>
</div>
</main>
</div>
<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
function showToast(msg) {
    const toast = document.getElementById('copyToast');
    toast.querySelector('span').textContent = msg;
    toast.classList.remove('hidden', 'opacity-0');
    toast.classList.add('opacity-100');
    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 2000);
}
function copyUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('URL copied!');
    }).catch(() => {
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('URL copied!');
    });
}
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Code copied!');
    }).catch(() => {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('Code copied!');
    });
}
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/management/qr-codes.blade.php ENDPATH**/ ?>