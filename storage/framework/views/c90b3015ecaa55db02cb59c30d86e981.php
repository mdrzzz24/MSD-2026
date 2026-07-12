<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>UTM Links — <?php echo e(config('app.name')); ?></title>
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
<button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>
<div><h1 class="text-lg font-bold text-gray-900">UTM Links</h1><p class="text-xs text-gray-500">Create, manage & monitor UTM campaign links</p></div>
<div class="flex items-center gap-2">
    <a href="<?php echo e(route('admin.management.utm.export-csv')); ?>"
       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
</div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
<?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php if($utmLinks->count()): ?>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between"><h2 class="text-base font-bold text-gray-900"><?php echo e(Auth::user()->role === 'super_admin' ? 'All UTM Links' : 'My UTM Links'); ?></h2><button onclick="openLinkModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Link</button></div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">UTM Parameters</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Full URL</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Regs</th>
<?php if(Auth::user()->isClient()): ?>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</th>
<?php else: ?>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked</th>
<?php endif; ?>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__currentLoopData = $utmLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $regs = $link->registrationsCount(); $checked = $link->checkedInCount(); $approved = $link->approvedCount(); $pending = $link->pendingCount(); $rejected = $link->rejectedCount(); ?>
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><span class="text-sm font-semibold text-gray-900"><?php echo e($link->name); ?></span></td>
<td class="px-5 py-4">
<div class="flex flex-wrap gap-1">
<span class="text-[10px] font-medium bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">source:<?php echo e($link->utm_source); ?></span>
<span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">medium:<?php echo e($link->utm_medium); ?></span>
<span class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">campaign:<?php echo e($link->utm_campaign); ?></span>
<?php if($link->utm_content): ?><span class="text-[10px] font-medium bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded">content:<?php echo e($link->utm_content); ?></span><?php endif; ?>
</div>
</td>
<td class="px-5 py-4 max-w-[200px]">
<input type="text" value="<?php echo e($link->full_url); ?>" readonly onclick="this.select()" class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded w-full cursor-text border-0">
</td>
<td class="px-5 py-4">
<span class="text-xs text-gray-500"><?php echo e($link->creator?->name ?? '—'); ?></span>
</td>
<td class="px-5 py-4 text-center">
<?php if($regs > 0): ?>
<a href="<?php echo e(route('admin.registrants.index', ['utm_source' => $link->utm_source, 'utm_medium' => $link->utm_medium, 'utm_campaign' => $link->utm_campaign])); ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline"><?php echo e($regs); ?></a>
<?php else: ?>
<span class="text-sm text-gray-400">0</span>
<?php endif; ?>
</td>
<?php if(Auth::user()->isClient()): ?>
<td class="px-5 py-4 text-center"><span class="text-sm <?php echo e($approved > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($approved); ?></span></td>
<td class="px-5 py-4 text-center"><span class="text-sm <?php echo e($pending > 0 ? 'text-amber-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($pending); ?></span></td>
<td class="px-5 py-4 text-center"><span class="text-sm <?php echo e($rejected > 0 ? 'text-red-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($rejected); ?></span></td>
<?php else: ?>
<td class="px-5 py-4 text-center"><span class="text-sm <?php echo e($checked > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($checked); ?></span></td>
<?php endif; ?>
<td class="px-5 py-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<?php if($regs > 0): ?>
<a href="<?php echo e(route('admin.registrants.index', ['utm_source' => $link->utm_source, 'utm_medium' => $link->utm_medium, 'utm_campaign' => $link->utm_campaign])); ?>" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View Registrants">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
</a>
<?php endif; ?>
<button onclick="editLink(<?php echo e($link->id); ?>, '<?php echo e(addslashes($link->name)); ?>', '<?php echo e($link->base_url); ?>', '<?php echo e($link->utm_source); ?>', '<?php echo e($link->utm_medium); ?>', '<?php echo e($link->utm_campaign); ?>', '<?php echo e($link->utm_content ?? ''); ?>')" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</button>
<form action="<?php echo e(route('admin.management.utm-links.destroy', $link)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete <?php echo e(addslashes($link->name)); ?>?')">
<?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
<button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
</button></form></div>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
</table>
</div>


<div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-6 text-xs text-gray-500">
<span>Total links: <strong class="text-gray-700"><?php echo e($utmLinks->count()); ?></strong></span>
<span>Total registrations: <strong class="text-gray-700"><?php echo e($utmLinks->sum(fn($l) => $l->registrationsCount())); ?></strong></span>
</div>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
<svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
<p class="text-gray-400 font-medium">No UTM links yet</p>
<p class="text-xs text-gray-400 mt-1">Click the button below to create your first UTM link.</p>
<button onclick="openLinkModal()" class="mt-4 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ Create UTM Link</button>
</div>
<?php endif; ?>


<?php if($sources->count()): ?>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100"><h2 class="text-base font-bold text-gray-900">All Sources (Auto-tracked)</h2><p class="text-xs text-gray-500 mt-0.5"><?php echo e($totals['all']); ?> total<?php echo e(Auth::user()->isClient() ? '' : ' · ' . $totals['checked'] . ' checked in'); ?></p></div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Source</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrants</th>
<?php if(Auth::user()->isClient()): ?>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</th>
<?php else: ?>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked In</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate</th>
<?php endif; ?>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<?php if($src->utm_source): ?>
<a href="<?php echo e(route('admin.registrants.index', ['utm_source' => $src->utm_source])); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"><?php echo e($src->utm_source); ?></a>
<?php else: ?>
<a href="<?php echo e(route('admin.registrants.index', ['direct' => 1])); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">Direct</a>
<?php endif; ?>
</td>
<td class="px-5 py-4">
<?php if($src->total > 0): ?>
<a href="<?php echo e($src->utm_source ? route('admin.registrants.index', ['utm_source' => $src->utm_source]) : route('admin.registrants.index', ['direct' => 1])); ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline"><?php echo e($src->total); ?></a>
<?php else: ?>
<span class="text-sm text-gray-400">0</span>
<?php endif; ?>
</td>
<?php if(Auth::user()->isClient()): ?>
<td class="px-5 py-4"><span class="text-sm <?php echo e(($src->approved_count ?? 0) > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($src->approved_count ?? 0); ?></span></td>
<td class="px-5 py-4"><span class="text-sm <?php echo e(($src->pending_count ?? 0) > 0 ? 'text-amber-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($src->pending_count ?? 0); ?></span></td>
<td class="px-5 py-4"><span class="text-sm <?php echo e(($src->rejected_count ?? 0) > 0 ? 'text-red-600 font-bold' : 'text-gray-400'); ?>"><?php echo e($src->rejected_count ?? 0); ?></span></td>
<?php else: ?>
<td class="px-5 py-4"><span class="text-sm text-gray-600"><?php echo e($src->checked_in); ?></span></td>
<td class="px-5 py-4">
<div class="flex items-center gap-2">
<div class="h-2 w-20 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-400 rounded-full" style="width:<?php echo e($src->total > 0 ? $src->checked_in/$src->total*100 : 0); ?>%"></div></div>
<span class="text-xs text-gray-500"><?php echo e($src->total > 0 ? round($src->checked_in/$src->total*100) : 0); ?>%</span>
</div>
</td>
<?php endif; ?>
<td class="px-5 py-4 text-center">
<?php if($src->total > 0): ?>
<a href="<?php echo e($src->utm_source ? route('admin.registrants.index', ['utm_source' => $src->utm_source]) : route('admin.registrants.index', ['direct' => 1])); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="View Registrants">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
View
</a>
<?php else: ?>
<span class="text-xs text-gray-400">—</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
</table>
</div>
</div>
<?php endif; ?>
</div>
</main>
</div>


<div id="linkModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="linkModalTitle">Create UTM Link</h3></div>
<form id="linkForm" method="POST" action="<?php echo e(route('admin.management.utm-links.store')); ?>">
<?php echo csrf_field(); ?>
<input type="hidden" name="_method" id="linkFormMethod" value="POST">
<input type="hidden" name="link_id" id="linkId">
<div class="p-6 space-y-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Link Name <span class="text-red-500">*</span></label>
<input type="text" id="linkName" name="name" required placeholder="e.g. Metrodata LinkedIn Campaign" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">A recognizable name for this link, e.g. "Metrodata LinkedIn Ads" or "MSD Email Blast July".</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Base URL <span class="text-red-500">*</span></label>
<input type="url" id="linkBaseUrl" name="base_url" value="https://event.metrodata.co.id/home1" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">The registration page URL. UTM parameters will be automatically appended to this URL.</p></div>
<div class="grid grid-cols-3 gap-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Source <span class="text-red-500">*</span></label>
<input type="text" id="linkSource" name="utm_source" required placeholder="metrodata" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">Traffic origin: metrodata, linkedin, newsletter, partner_email, etc.</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Medium <span class="text-red-500">*</span></label>
<input type="text" id="linkMedium" name="utm_medium" required placeholder="social" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">Marketing medium: cpc, social, email, banner, referral, event_booth.</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Campaign <span class="text-red-500">*</span></label>
<input type="text" id="linkCampaign" name="utm_campaign" required placeholder="msd2026" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">Campaign identifier: msd2026, metrodata_summit, soltius_webinar, etc.</p></div>
</div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Content (optional)</label>
<input type="text" id="linkContent" name="utm_content" placeholder="e.g. hero-banner, sidebar-cta" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">Differentiate ad variations: hero-banner, sidebar-cta, email-button-a, etc.</p></div>
</div>
<div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
<button type="button" onclick="closeLinkModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Save Link</button>
</div>
</form>
</div>
</div>

<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
function openLinkModal() {
document.getElementById('linkModalTitle').textContent = 'Create UTM Link';
document.getElementById('linkForm').action = '<?php echo e(route("admin.management.utm-links.store")); ?>';
document.getElementById('linkFormMethod').value = 'POST';
['linkId','linkName','linkBaseUrl','linkSource','linkMedium','linkCampaign','linkContent'].forEach(id => document.getElementById(id).value = '');
document.getElementById('linkBaseUrl').value = 'https://event.metrodata.co.id/home1';
document.getElementById('linkModal').classList.remove('hidden');
document.getElementById('linkModal').classList.add('flex');
}
function editLink(id, name, base, source, medium, campaign, content) {
document.getElementById('linkModalTitle').textContent = 'Edit UTM Link';
document.getElementById('linkForm').action = '/admin/management/utm-links/' + id;
document.getElementById('linkFormMethod').value = 'PUT';
document.getElementById('linkId').value = id;
document.getElementById('linkName').value = name;
document.getElementById('linkBaseUrl').value = base;
document.getElementById('linkSource').value = source;
document.getElementById('linkMedium').value = medium;
document.getElementById('linkCampaign').value = campaign;
document.getElementById('linkContent').value = content;
document.getElementById('linkModal').classList.remove('hidden');
document.getElementById('linkModal').classList.add('flex');
}
function closeLinkModal() {
document.getElementById('linkModal').classList.add('hidden');
document.getElementById('linkModal').classList.remove('flex');
}
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/management/utm.blade.php ENDPATH**/ ?>