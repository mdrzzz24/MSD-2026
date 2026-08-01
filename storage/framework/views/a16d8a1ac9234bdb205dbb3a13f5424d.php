<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Workshop UTM Links — <?php echo e(config('app.name')); ?></title>
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
<div><h1 class="text-lg font-bold text-gray-900">Workshop UTM Links</h1><p class="text-xs text-gray-500">Create & monitor UTM links for workshop invitation registration</p></div>
</div>
<div class="flex items-center gap-2">
<a href="<?php echo e(route('admin.workshops.utm-links.export')); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
Export CSV
</a>
<button onclick="openWorkshopLinkModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Workshop UTM Link</button>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
<?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
<h2 class="text-base font-bold text-gray-900"><?php echo e(Auth::user()->role === 'super_admin' ? 'All Workshop UTM Links' : 'Workshop UTM Links'); ?></h2>
<span class="text-xs text-gray-400">Separate from event registration UTM</span>
</div>
<?php if($utmLinks->count()): ?>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Workshop</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">UTM Parameters</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Full URL</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Regs</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__currentLoopData = $utmLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $regs = $link->workshopRegistrationsCount(); ?>
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><span class="text-sm font-semibold text-gray-900"><?php echo e($link->name); ?></span></td>
<td class="px-5 py-4">
<?php if($link->workshop): ?>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">🎯 <?php echo e($link->workshop->name ?: $link->workshop->title); ?></span>
<?php else: ?>
<span class="text-xs text-gray-400">—</span>
<?php endif; ?>
</td>
<td class="px-5 py-4">
<div class="flex flex-wrap gap-1">
<span class="text-[10px] font-medium bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">source:<?php echo e($link->utm_source); ?></span>
<span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">medium:<?php echo e($link->utm_medium); ?></span>
<span class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">campaign:<?php echo e($link->utm_campaign); ?></span>
<?php if($link->utm_content): ?><span class="text-[10px] font-medium bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded">content:<?php echo e($link->utm_content); ?></span><?php endif; ?>
</div>
</td>
<td class="px-5 py-4 max-w-[220px]">
<div class="flex items-center gap-1">
<input type="text" id="url-<?php echo e($link->id); ?>" value="<?php echo e($link->full_url); ?>" readonly onclick="this.select()" class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded flex-1 min-w-0 border-0 cursor-text">
<button onclick="copyUrl(this, 'url-<?php echo e($link->id); ?>')" class="flex-shrink-0 p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Copy URL">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
</button>
</div>
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
<td class="px-5 py-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<?php if($regs > 0): ?>
<a href="<?php echo e(route('admin.registrants.index', ['utm_source' => $link->utm_source, 'utm_medium' => $link->utm_medium, 'utm_campaign' => $link->utm_campaign])); ?>" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View Registrants">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
</a>
<?php endif; ?>
<?php if(Auth::user()->role === 'super_admin' || $link->created_by === Auth::id()): ?>
<button onclick="editWorkshopLink(<?php echo e($link->id); ?>, '<?php echo e(addslashes($link->name)); ?>', '<?php echo e($link->workshop_id); ?>', '<?php echo e($link->utm_source); ?>', '<?php echo e($link->utm_medium); ?>', '<?php echo e($link->utm_campaign); ?>', '<?php echo e($link->utm_content ?? ''); ?>', '<?php echo e($link->workshop_invitation_id ?? ''); ?>')" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</button>
<form action="<?php echo e(route('admin.workshops.utm-links.destroy', $link)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete <?php echo e(addslashes($link->name)); ?>?')">
<?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
<button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
</button></form>
<?php endif; ?>
</div>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
</table>
</div>
<div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-6 text-xs text-gray-500">
<span>Total workshop UTM links: <strong class="text-gray-700"><?php echo e($utmLinks->count()); ?></strong></span>
<span>Total registrations: <strong class="text-gray-700"><?php echo e($utmLinks->sum(fn($l) => $l->workshopRegistrationsCount())); ?></strong></span>
</div>
<?php else: ?>
<div class="px-6 py-12 text-center">
<svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
<p class="text-gray-400 font-medium">No workshop UTM links yet</p>
<p class="text-xs text-gray-400 mt-1">Create UTM links for workshop invitations (newsletter, sales, meta, ...)</p>
<button onclick="openWorkshopLinkModal()" class="mt-4 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ Create Workshop UTM Link</button>
</div>
<?php endif; ?>
</div>
</div>
</main>
</div>


<div id="workshopUtmModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeWorkshopLinkModal()"></div>
<div class="fixed inset-0 flex items-center justify-center p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="wUtmModalTitle">Create Workshop UTM Link</h3></div>
<form id="workshopUtmForm" method="POST" action="<?php echo e(route('admin.workshops.utm-links.store')); ?>">
<?php echo csrf_field(); ?>
<input type="hidden" name="_method" id="wUtmFormMethod" value="POST">
<input type="hidden" name="link_id" id="wUtmLinkId">
<div class="p-6 space-y-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Link Name <span class="text-red-500">*</span></label>
<input type="text" id="wUtmName" name="name" required placeholder="e.g. Workshop AWS - Newsletter" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Workshop <span class="text-red-500">*</span></label>
<select id="wUtmWorkshop" name="workshop_id" onchange="populateWorkshopOptions()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="">— Select workshop —</option>
<?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
$inv = $w->invitations()->whereNotNull('slug')->first() ?? $w->invitations()->where('is_active', true)->first();
$invUrl = $inv ? rtrim(\App\Models\UtmLink::BASE_URL, '/') . '/invitation/workshop/' . ($inv->slug ?: $inv->token) : '';
?>
<option value="<?php echo e($w->id); ?>" data-invite-url="<?php echo e($invUrl); ?>"><?php echo e($w->name ?: $w->title); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<p class="text-xs text-gray-400 mt-1">Link will point to the workshop invitation (custom slug if available).</p></div>
<div id="wUtmTrackGroup" style="display:none"><label class="block text-sm font-semibold text-gray-700 mb-1">Track <span class="text-gray-400 font-normal">(optional)</span></label>
<select id="wUtmTrack" onchange="populateInvitationOptions()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="">— All tracks —</option>
</select>
<p class="text-xs text-gray-400 mt-1">Filter the invitation list by track.</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Invitation / Custom Slug <span class="text-gray-400 font-normal">(optional)</span></label>
<select id="wUtmInvitation" name="workshop_invitation_id" onchange="updateWorkshopUrlPreview()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="">— Auto (invitation default) —</option>
</select>
<p class="text-xs text-gray-400 mt-1">Pick a specific custom slug / invitation if the workshop has more than one.</p></div>
<div class="grid grid-cols-3 gap-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Source <span class="text-red-500">*</span></label>
<input type="text" id="wUtmSource" name="utm_source" required placeholder="newsletter" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">newsletter, sales, meta, etc.</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Medium <span class="text-red-500">*</span></label>
<input type="text" id="wUtmMedium" name="utm_medium" required placeholder="email" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Campaign <span class="text-red-500">*</span></label>
<input type="text" id="wUtmCampaign" name="utm_campaign" required placeholder="msd2026" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
</div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Content (optional)</label>
<input type="text" id="wUtmContent" name="utm_content" placeholder="e.g. newsletter-banner-a" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
<p class="text-xs text-gray-500 mb-1">URL Preview</p>
<p class="text-[11px] text-indigo-600 break-all font-mono" id="wUtmUrlPreview">https://metrodatasolutionday.com/2026/invitation/workshop/{slug}?utm_source=...</p>
</div>
</div>
<div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
<button type="button" onclick="closeWorkshopLinkModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Save Link</button>
</div>
</form>
</div>
</div>
</div>

<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
var wInvitations = <?php echo json_encode($invitations, 15, 512) ?>;
var wTracks = <?php echo json_encode($tracks, 15, 512) ?>;
var wBaseUrl = '<?php echo e(rtrim(\App\Models\UtmLink::BASE_URL, '/')); ?>';

function populateWorkshopOptions() {
var wsId = document.getElementById('wUtmWorkshop').value;
var trackSel = document.getElementById('wUtmTrack');
var tracks = wTracks.filter(function(t){ return String(t.workshop_id) === String(wsId); });
var opts = '<option value="">— All tracks —</option>';
tracks.forEach(function(t){ opts += '<option value="'+t.id+'">'+t.name+'</option>'; });
trackSel.innerHTML = opts;
document.getElementById('wUtmTrackGroup').style.display = (wsId && tracks.length) ? '' : 'none';
populateInvitationOptions();
}

function populateInvitationOptions() {
var wsId = document.getElementById('wUtmWorkshop').value;
var trackId = document.getElementById('wUtmTrack').value;
var invs = wInvitations.filter(function(i){
    if (String(i.workshop_id) !== String(wsId)) return false;
    if (trackId && String(i.track_id) !== String(trackId)) return false;
    return true;
}).sort(function(a,b){
    // Prefer custom-slug invitations (nicer URL) when auto-selecting a track
    var aSlug = a.slug ? 0 : 1, bSlug = b.slug ? 0 : 1;
    if (aSlug !== bSlug) return aSlug - bSlug;
    return a.id - b.id;
});
var sel = document.getElementById('wUtmInvitation');
var opts = '<option value="">— Auto (invitation default) —</option>';
invs.forEach(function(i){
    var label = (i.slug ? i.slug : '(random) ' + i.token.slice(0,8));
    if (i.track_name) label += ' · ' + i.track_name;
    opts += '<option value="'+i.id+'">'+label+'</option>';
});
sel.innerHTML = opts;
// When a track is chosen, auto-select its invitation so the link points to that track's session
if (trackId && invs.length) {
    sel.value = String(invs[0].id);
}
updateWorkshopUrlPreview();
}

function openWorkshopLinkModal() {
document.getElementById('wUtmModalTitle').textContent = 'Create Workshop UTM Link';
document.getElementById('workshopUtmForm').action = '<?php echo e(route("admin.workshops.utm-links.store")); ?>';
document.getElementById('wUtmFormMethod').value = 'POST';
['wUtmLinkId','wUtmName','wUtmSource','wUtmMedium','wUtmCampaign','wUtmContent'].forEach(id => document.getElementById(id).value = '');
document.getElementById('wUtmWorkshop').value = '';
populateWorkshopOptions();
document.getElementById('wUtmInvitation').value = '';
updateWorkshopUrlPreview();
document.getElementById('workshopUtmModal').classList.remove('hidden');
}
const wUtmUpdateUrl = '<?php echo e(route("admin.workshops.utm-links.update", ["utmLink" => "LINK_ID"])); ?>';
function editWorkshopLink(id, name, workshopId, source, medium, campaign, content, invitationId) {
document.getElementById('wUtmModalTitle').textContent = 'Edit Workshop UTM Link';
document.getElementById('workshopUtmForm').action = wUtmUpdateUrl.replace('LINK_ID', id);
document.getElementById('wUtmFormMethod').value = 'PUT';
document.getElementById('wUtmLinkId').value = id;
document.getElementById('wUtmName').value = name;
document.getElementById('wUtmWorkshop').value = workshopId;
document.getElementById('wUtmSource').value = source;
document.getElementById('wUtmMedium').value = medium;
document.getElementById('wUtmCampaign').value = campaign;
document.getElementById('wUtmContent').value = content;
var invData = invitationId ? wInvitations.find(function(i){ return String(i.id) === String(invitationId); }) : null;
populateWorkshopOptions();
document.getElementById('wUtmTrack').value = invData ? invData.track_id : '';
populateInvitationOptions();
document.getElementById('wUtmInvitation').value = invitationId || '';
updateWorkshopUrlPreview();
document.getElementById('workshopUtmModal').classList.remove('hidden');
}
function closeWorkshopLinkModal() {
document.getElementById('workshopUtmModal').classList.add('hidden');
}
function updateWorkshopUrlPreview() {
var invSel = document.getElementById('wUtmInvitation');
var inv = invSel.selectedOptions[0];
var base;
if (inv && inv.value) {
    var data = wInvitations.find(function(i){ return String(i.id) === String(inv.value); });
    base = data ? (wBaseUrl + '/invitation/workshop/' + (data.slug || data.token)) : (wBaseUrl + '/invitation/workshop/{slug}');
} else {
    var sel = document.getElementById('wUtmWorkshop');
    var opt = sel.selectedOptions[0];
    base = opt ? (opt.getAttribute('data-invite-url') || wBaseUrl + '/invitation/workshop/{slug}') : (wBaseUrl + '/invitation/workshop/{slug}');
}
var source = document.getElementById('wUtmSource').value || '...';
var preview = document.getElementById('wUtmUrlPreview');
preview.textContent = base + '?utm_source=' + source;
}
function copyUrl(btn, inputId) {
const input = document.getElementById(inputId);
navigator.clipboard.writeText(input.value).then(() => {
const orig = btn.innerHTML;
btn.innerHTML = '<svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
btn.classList.add('pointer-events-none');
setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('pointer-events-none'); }, 1500);
});
}
document.getElementById('wUtmSource')?.addEventListener('input', updateWorkshopUrlPreview);
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
// Close on Escape
document.addEventListener('keydown', function(e) {
if (e.key === 'Escape') closeWorkshopLinkModal();
});
</script>
<style>.animate-fade-in{animation:fadeIn .2s ease-out}@keyframes fadeIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}</style>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/workshops/utm-links.blade.php ENDPATH**/ ?>