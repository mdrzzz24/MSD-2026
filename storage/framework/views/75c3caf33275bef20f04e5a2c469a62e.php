<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Data Cleaning — <?php echo e(config('app.name')); ?></title>
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
<div class="flex items-center gap-3">
<button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>
<div>
<h1 class="text-lg font-bold text-gray-900">Data Cleaning <span class="text-indigo-500">(Company Names)</span></h1>
<p class="text-xs text-gray-500">Registrants grouped by email domain — set the standard company name and apply it to the whole group</p>
</div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
<?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-xs text-indigo-900 leading-relaxed">
<span class="font-semibold">How it works:</span> registrants are grouped by their <strong>email domain</strong>.
For each group, type the <strong>standard company name</strong> you want (pre-filled with the most common current value) and click <strong>Apply</strong> to update every registrant in that domain — or fill in several rows and use <strong>Apply All</strong>.
Click <strong>View members</strong> to inspect who is in a group before applying.
</div>


<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Registrants</p>
<p class="mt-1 text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['registrants'])); ?></p>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">With Email</p>
<p class="mt-1 text-2xl font-bold text-indigo-600"><?php echo e(number_format($stats['withEmail'])); ?></p>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Without Email</p>
<p class="mt-1 text-2xl font-bold text-amber-600"><?php echo e(number_format($stats['withoutEmail'])); ?></p>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Domain Groups</p>
<p class="mt-1 text-2xl font-bold text-emerald-600"><?php echo e(number_format($stats['domains'])); ?></p>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Company Variants</p>
<p class="mt-1 text-2xl font-bold text-rose-600"><?php echo e(number_format($stats['companyVariants'])); ?></p>
</div>
</div>


<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
<div class="relative w-full sm:w-80">
<input id="domainSearch" type="text" placeholder="Filter by domain or company…"
       class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
</div>
<p class="text-xs text-gray-500">
<strong><?php echo e(number_format($stats['domains'])); ?></strong> domain group(s) · <strong><?php echo e(number_format($stats['companyVariants'])); ?></strong> company name variant(s) to clean up
</p>
</div>


<form id="dataCleaningForm" method="POST" action="<?php echo e(route('admin.data-cleaning.apply')); ?>">
<?php echo csrf_field(); ?>
<?php $oldStandards = old('standard', []); ?>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/5">Email Domain</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Current Company Names</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Standard Company Name</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Apply</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__empty_1 = true; $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr class="dc-row hover:bg-gray-50/50" data-idx="<?php echo e($loop->index); ?>" data-domain="<?php echo e($d['domain']); ?>">
<td class="px-5 py-3.5 align-top">
<div class="flex items-center gap-2">
<span class="text-sm font-semibold text-gray-900 font-mono"><?php echo e($d['domain']); ?></span>
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200"><?php echo e($d['total']); ?> reg</span>
</div>
<button type="button" class="dc-toggle mt-1.5 inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800"
        data-idx="<?php echo e($loop->index); ?>" data-domain="<?php echo e($d['domain']); ?>">
<span class="dc-toggle-caret">▸</span> View members
</button>
</td>
<td class="px-5 py-3.5 align-top">
<div class="flex flex-wrap gap-1.5 max-w-md">
<?php $__currentLoopData = array_slice($d['companyCounts'], 0, 8, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $isBlank = trim($name) === ''; ?>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold <?php echo e($isBlank ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'); ?>"
      title="<?php echo e($isBlank ? 'No company value set' : $name); ?>">
<?php echo e($isBlank ? '(empty)' : \Illuminate\Support\Str::limit($name, 38)); ?>

<span class="opacity-60">×<?php echo e($count); ?></span>
</span>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php if(count($d['companyCounts']) > 8): ?>
<span class="text-[10px] text-gray-400 self-center">+ <?php echo e(count($d['companyCounts']) - 8); ?> more</span>
<?php endif; ?>
</div>
</td>
<td class="px-5 py-3.5 align-top">
<input type="text" name="standard[<?php echo e($d['domain']); ?>]" value="<?php echo e($oldStandards[$d['domain']] ?? $d['suggested']); ?>"
       placeholder="Standard company name"
       class="dc-standard w-full sm:w-72 px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
</td>
<td class="px-5 py-3.5 align-top text-center whitespace-nowrap">
<button type="submit" name="domain" value="<?php echo e($d['domain']); ?>"
        class="dc-apply px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">Apply</button>
</td>
</tr>
<tr class="hidden dc-members-row" id="dc-members-<?php echo e($loop->index); ?>">
<td colspan="4" class="px-5 py-0">
<div class="py-3">
<div class="dc-members-box rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-3" id="dc-members-<?php echo e($loop->index); ?>-content">
<p class="text-sm text-gray-400">Click "View members" to load the registrants in this domain.</p>
</div>
</div>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400">No registrants with an email address yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50/50">
<p class="text-xs text-gray-500"><strong><?php echo e(number_format($stats['domains'])); ?></strong> domain group(s) · <strong><?php echo e(number_format($stats['registrants'])); ?></strong> registrant(s) total</p>
<button type="submit" name="apply_all" value="1" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 transition shadow-sm">Apply All</button>
</div>
</div>
</form>
</div>
</main>
</div>

<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});

function escapeHtml(s) {
return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
});
}

// Live filter by domain / current company values
const domainSearch = document.getElementById('domainSearch');
if (domainSearch) domainSearch.addEventListener('input', function () {
const q = this.value.trim().toLowerCase();
document.querySelectorAll('#dataCleaningForm tr.dc-row').forEach(function (row) {
const domain = (row.getAttribute('data-domain') || '').toLowerCase();
const chips = (row.querySelector('td:nth-child(2)')?.textContent || '').toLowerCase();
const match = !q || domain.includes(q) || chips.includes(q);
row.style.display = match ? '' : 'none';
const idx = row.getAttribute('data-idx');
const memRow = document.getElementById('dc-members-' + idx);
if (memRow && !match) memRow.style.display = 'none';
});
});

// Expandable member drill-down (lazy AJAX)
document.querySelectorAll('.dc-toggle').forEach(function (btn) {
btn.addEventListener('click', function () {
const idx = btn.getAttribute('data-idx');
const domain = btn.getAttribute('data-domain');
const row = document.getElementById('dc-members-' + idx);
const caret = btn.querySelector('.dc-toggle-caret');
if (!row) return;
const opening = row.classList.contains('hidden');
row.classList.toggle('hidden');
caret.textContent = opening ? '▾' : '▸';
btn.childNodes[2].textContent = opening ? ' Hide members' : ' View members';
if (opening) loadMembers(idx, domain);
});
});

async function loadMembers(idx, domain) {
const box = document.getElementById('dc-members-' + idx + '-content');
if (!box) return;
box.innerHTML = '<p class="text-sm text-gray-400">Loading…</p>';
try {
const res = await fetch('<?php echo e(route('admin.data-cleaning.members')); ?>', {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'Accept': 'application/json',
'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
},
body: JSON.stringify({ domain: domain })
});
const data = await res.json();
if (!Array.isArray(data) || data.length === 0) {
box.innerHTML = '<p class="text-sm text-gray-400">No registrants with this email domain.</p>';
return;
}
let html = '<div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">' + data.length + ' registrant(s)</div>'
+ '<div class="grid gap-1.5 max-h-80 overflow-y-auto pr-1">';
data.forEach(function (m) {
html += '<div class="flex items-center justify-between gap-3 px-3 py-1.5 rounded-lg bg-white border border-gray-100">'
+ '<div class="min-w-0">'
+ '<span class="text-sm font-medium text-gray-800">' + escapeHtml(m.name) + '</span>'
+ '<span class="text-xs text-gray-400 block truncate">' + escapeHtml(m.email) + '</span>'
+ '</div>'
+ '<span class="text-xs text-gray-600 shrink-0 max-w-[200px] truncate">' + (m.company ? escapeHtml(m.company) : '<span class="text-gray-400">(empty)</span>') + '</span>'
+ '</div>';
});
html += '</div>';
box.innerHTML = html;
} catch (err) {
box.innerHTML = '<p class="text-sm text-red-500">Failed to load members.</p>';
}
}

// Submit confirmations
document.getElementById('dataCleaningForm')?.addEventListener('submit', function (e) {
const btn = e.submitter;
if (!btn) return;
if (btn.name === 'apply_all') {
let n = 0;
document.querySelectorAll('#dataCleaningForm .dc-standard').forEach(function (i) { if (i.value.trim()) n++; });
if (n === 0) { e.preventDefault(); alert('Please fill in at least one standard company name.'); return; }
if (!confirm('Apply standard company names for all ' + n + ' filled domain(s)? This updates every registrant in those domains.')) e.preventDefault();
} else if (btn.name === 'domain') {
const row = btn.closest('tr.dc-row');
const input = row ? row.querySelector('.dc-standard') : null;
const company = input ? input.value.trim() : '';
if (!company) { e.preventDefault(); alert('Please fill in the standard company name for this domain first.'); return; }
if (!confirm('Set company to "' + company + '" for all registrants in ' + btn.value + '?')) e.preventDefault();
}
});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/data-cleaning/index.blade.php ENDPATH**/ ?>