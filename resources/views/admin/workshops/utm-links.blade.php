<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Workshop UTM Links — {{ config('app.name') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
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
<a href="{{ route('admin.workshops.utm-links.export') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
Export CSV
</a>
<button onclick="openWorkshopLinkModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Workshop UTM Link</button>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@include('admin.partials.notification')

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
<h2 class="text-base font-bold text-gray-900">{{ Auth::user()->role === 'super_admin' ? 'All Workshop UTM Links' : 'Workshop UTM Links' }}</h2>
<span class="text-xs text-gray-400">Separate from event registration UTM</span>
</div>
@if ($utmLinks->count())
@php
    $grouped = $utmLinks->groupBy(fn($l) => $l->workshop_id ?: 'none')->sortKeys();
@endphp
<div class="divide-y divide-gray-100">
@foreach ($grouped as $wsId => $groupLinks)
@php
    $gWorkshop = $groupLinks->first()->workshop;
    $gName = $gWorkshop ? ($gWorkshop->name ?: $gWorkshop->title) : 'Unassigned';
    $gRegs = $groupLinks->sum(fn($l) => $l->workshopRegistrationsCount());
@endphp
<div>
<div class="px-5 py-3 bg-gray-50/60 border-b border-gray-100 flex items-center justify-between gap-2 cursor-pointer select-none ws-group-toggle" data-target="ws-body-{{ $loop->index }}">
<div class="flex items-center gap-2">
@if ($gWorkshop)
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">🎯 {{ $gName }}</span>
@else
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600">Unassigned</span>
@endif
<span class="text-xs text-gray-500">{{ $groupLinks->count() }} link{{ $groupLinks->count() > 1 ? 's' : '' }} · <strong class="text-gray-700">{{ $gRegs }}</strong> registration{{ $gRegs === 1 ? '' : 's' }}</span>
</div>
<svg class="ws-chevron w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200" style="transform:rotate(0deg)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"/></svg>
</div>
<div id="ws-body-{{ $loop->index }}" class="ws-group-body hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">UTM Parameters</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Full URL</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Regs</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($groupLinks as $link)
@php $regs = $link->workshopRegistrationsCount(); @endphp
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<span class="text-sm font-semibold text-gray-900">{{ $link->name }}</span>
@if ($link->track)
<span class="block text-[10px] font-medium text-gray-500 mt-0.5">Track: {{ $link->track->name }}</span>
@endif
</td>
<td class="px-5 py-4">
<div class="flex flex-wrap gap-1">
<span class="text-[10px] font-medium bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">source:{{ $link->utm_source }}</span>
<span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">medium:{{ $link->utm_medium }}</span>
<span class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">campaign:{{ $link->utm_campaign }}</span>
@if ($link->utm_content)<span class="text-[10px] font-medium bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded">content:{{ $link->utm_content }}</span>@endif
</div>
</td>
<td class="px-5 py-4 max-w-[220px]">
<div class="flex items-center gap-1">
<input type="text" id="url-{{ $link->id }}" value="{{ $link->full_url }}" readonly onclick="this.select()" class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded flex-1 min-w-0 border-0 cursor-text">
<button onclick="copyUrl(this, 'url-{{ $link->id }}')" class="flex-shrink-0 p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Copy URL">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
</button>
</div>
</td>
<td class="px-5 py-4">
<span class="text-xs text-gray-500">{{ $link->creator?->name ?? '—' }}</span>
</td>
<td class="px-5 py-4 text-center">
@if ($regs > 0)
<a href="{{ route('admin.workshops.utm-links.registrants', $link) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">{{ $regs }}</a>
@else
<span class="text-sm text-gray-400">0</span>
@endif
</td>
<td class="px-5 py-4 text-center">
<div class="flex items-center justify-center gap-1.5">
@if ($regs > 0)
<a href="{{ route('admin.workshops.utm-links.registrants', $link) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View Registrants">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
</a>
@endif
@if (Auth::user()->role === 'super_admin' || $link->created_by === Auth::id())
<button onclick="editWorkshopLink({{ $link->id }}, '{{ addslashes($link->name) }}', '{{ $link->workshop_id }}', '{{ $link->utm_source }}', '{{ $link->utm_medium }}', '{{ $link->utm_campaign }}', '{{ $link->utm_content ?? '' }}', '{{ $link->workshop_invitation_id ?? '' }}', '{{ $link->track_id ?? '' }}')" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</button>
<form action="{{ route('admin.workshops.utm-links.destroy', $link) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($link->name) }}?')">
@csrf @method('DELETE')
<button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
</button></form>
@endif
</div>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
@endforeach
</div>
<div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-6 text-xs text-gray-500">
<span>Total workshop UTM links: <strong class="text-gray-700">{{ $utmLinks->count() }}</strong></span>
<span>Total registrations: <strong class="text-gray-700">{{ $utmLinks->sum(fn($l) => $l->workshopRegistrationsCount()) }}</strong></span>
</div>
@else
<div class="px-6 py-12 text-center">
<svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
<p class="text-gray-400 font-medium">No workshop UTM links yet</p>
<p class="text-xs text-gray-400 mt-1">Create UTM links for workshop invitations (newsletter, sales, meta, ...)</p>
<button onclick="openWorkshopLinkModal()" class="mt-4 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ Create Workshop UTM Link</button>
</div>
@endif
</div>
</div>
</main>
</div>

{{-- Create/Edit Modal --}}
<div id="workshopUtmModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeWorkshopLinkModal()"></div>
<div class="fixed inset-0 flex items-center justify-center p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="wUtmModalTitle">Create Workshop UTM Link</h3></div>
<form id="workshopUtmForm" method="POST" action="{{ route('admin.workshops.utm-links.store') }}">
@csrf
<input type="hidden" name="_method" id="wUtmFormMethod" value="POST">
<input type="hidden" name="link_id" id="wUtmLinkId">
<div class="p-6 space-y-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Link Name <span class="text-red-500">*</span></label>
<input type="text" id="wUtmName" name="name" required placeholder="e.g. Workshop AWS - Newsletter" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Workshop <span class="text-red-500">*</span></label>
<select id="wUtmWorkshop" name="workshop_id" onchange="populateWorkshopOptions()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="">— Select workshop —</option>
@foreach ($workshops as $w)
@php
$inv = $w->invitations()->whereNotNull('slug')->whereNull('track_id')->first()
    ?? $w->invitations()->whereNotNull('slug')->first()
    ?? $w->invitations()->where('is_active', true)->first();
$invUrl = $inv ? rtrim(\App\Models\UtmLink::BASE_URL, '/') . '/invitation/workshop/' . ($inv->slug ?: $inv->token) : '';
@endphp
<option value="{{ $w->id }}" data-invite-url="{{ $invUrl }}">{{ $w->name ?: $w->title }}</option>
@endforeach
</select>
<p class="text-xs text-gray-400 mt-1">Link will point to the workshop invitation (custom slug if available).</p></div>
<div id="wUtmTrackGroup" style="display:none"><label class="block text-sm font-semibold text-gray-700 mb-1">Track <span class="text-gray-400 font-normal">(optional)</span></label>
<select id="wUtmTrack" onchange="populateInvitationOptions()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="">— All tracks —</option>
</select>
<p class="text-xs text-gray-400 mt-1">Filter the invitation list by track.</p></div>
<div id="wUtmInvitationGroup"><label class="block text-sm font-semibold text-gray-700 mb-1">Invitation / Custom Slug <span class="text-gray-400 font-normal">(optional)</span></label>
<select id="wUtmInvitation" name="workshop_invitation_id" onchange="updateWorkshopUrlPreview()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="">— Auto (invitation default) —</option>
</select>
<p class="text-xs text-gray-400 mt-1">Pick a specific custom slug / invitation if the workshop has more than one.</p></div>
<input type="hidden" name="track_id" id="wUtmTrackId">
<div class="grid grid-cols-3 gap-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Source <span class="text-red-500">*</span></label>
<input type="text" id="wUtmSource" name="utm_source" required placeholder="newsletter" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">newsletter, sales, meta, etc.</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Medium <span class="text-gray-400 font-normal">(optional)</span></label>
<input type="text" id="wUtmMedium" name="utm_medium" placeholder="email" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Campaign <span class="text-gray-400 font-normal">(optional)</span></label>
<input type="text" id="wUtmCampaign" name="utm_campaign" placeholder="msd2026" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
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

@include('admin.partials.mobile-sidebar')
<script>
var wInvitations = @json($invitations);
var wTracks = @json($tracks);
var wBaseUrl = '{{ rtrim(\App\Models\UtmLink::BASE_URL, '/') }}';

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
// Only show the Invitation / Custom Slug picker when there is more than one custom slug to choose from.
var slugGroup = document.getElementById('wUtmInvitationGroup');
var customSlugs = invs.filter(function(i){ return i.slug; });
if (slugGroup) {
    if (customSlugs.length > 1) {
        slugGroup.style.display = '';
    } else {
        slugGroup.style.display = 'none';
        sel.value = customSlugs.length === 1 ? String(customSlugs[0].id) : '';
    }
}
// Store the chosen track on the link — the invitation page resolves the session from the UTM link
if (document.getElementById('wUtmTrackId')) document.getElementById('wUtmTrackId').value = trackId || '';
updateWorkshopUrlPreview();
}

function openWorkshopLinkModal() {
document.getElementById('wUtmModalTitle').textContent = 'Create Workshop UTM Link';
document.getElementById('workshopUtmForm').action = '{{ route("admin.workshops.utm-links.store") }}';
document.getElementById('wUtmFormMethod').value = 'POST';
['wUtmLinkId','wUtmName','wUtmSource','wUtmMedium','wUtmCampaign','wUtmContent','wUtmTrackId'].forEach(id => document.getElementById(id).value = '');
document.getElementById('wUtmWorkshop').value = '';
populateWorkshopOptions();
document.getElementById('wUtmInvitation').value = '';
updateWorkshopUrlPreview();
document.getElementById('workshopUtmModal').classList.remove('hidden');
}
const wUtmUpdateUrl = '{{ route("admin.workshops.utm-links.update", ["utmLink" => "LINK_ID"]) }}';
function editWorkshopLink(id, name, workshopId, source, medium, campaign, content, invitationId, trackId) {
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
document.getElementById('wUtmTrack').value = invData ? invData.track_id : (trackId || '');
populateInvitationOptions();
document.getElementById('wUtmInvitation').value = invitationId || '';
document.getElementById('wUtmTrackId').value = trackId || '';
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
var params = [];
var pairs = [['utm_source','wUtmSource'],['utm_medium','wUtmMedium'],['utm_campaign','wUtmCampaign'],['utm_content','wUtmContent']];
pairs.forEach(function(p){
    var v = document.getElementById(p[1]).value.trim();
    if (v) params.push(p[0] + '=' + encodeURIComponent(v));
});
var preview = document.getElementById('wUtmUrlPreview');
preview.textContent = params.length ? (base + '?' + params.join('&')) : base;
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
['wUtmSource','wUtmMedium','wUtmCampaign','wUtmContent'].forEach(id => document.getElementById(id)?.addEventListener('input', updateWorkshopUrlPreview));
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
// Workshop group accordion (expandable folders)
document.querySelectorAll('.ws-group-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = document.getElementById(btn.getAttribute('data-target'));
        if (!target) return;
        var chevron = btn.querySelector('.ws-chevron');
        var isHidden = target.classList.toggle('hidden');
        if (chevron) chevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(90deg)';
    });
});

// Close on Escape
document.addEventListener('keydown', function(e) {
if (e.key === 'Escape') closeWorkshopLinkModal();
});
</script>
<style>.animate-fade-in{animation:fadeIn .2s ease-out}@keyframes fadeIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}</style>
</body>
</html>
