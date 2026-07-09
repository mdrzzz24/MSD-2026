<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>UTM Links — {{ config('app.name') }}</title>
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
<div><h1 class="text-lg font-bold text-gray-900">UTM Links</h1><p class="text-xs text-gray-500">Create, manage & monitor UTM campaign links</p></div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@if (session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm">{!! session('success') !!}</div>
@endif

{{-- UTM Links Table --}}
@if ($utmLinks->count())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between"><h2 class="text-base font-bold text-gray-900">{{ Auth::user()->role === 'super_admin' ? 'All UTM Links' : 'My UTM Links' }}</h2><button onclick="openLinkModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Link</button></div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">UTM Parameters</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Full URL</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Regs</th>
@if (Auth::user()->isClient())
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</th>
@else
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked</th>
@endif
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($utmLinks as $link)
@php $regs = $link->registrationsCount(); $checked = $link->checkedInCount(); $approved = $link->approvedCount(); $pending = $link->pendingCount(); $rejected = $link->rejectedCount(); @endphp
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><span class="text-sm font-semibold text-gray-900">{{ $link->name }}</span></td>
<td class="px-5 py-4">
<div class="flex flex-wrap gap-1">
<span class="text-[10px] font-medium bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">source:{{ $link->utm_source }}</span>
<span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">medium:{{ $link->utm_medium }}</span>
<span class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">campaign:{{ $link->utm_campaign }}</span>
@if ($link->utm_content)<span class="text-[10px] font-medium bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded">content:{{ $link->utm_content }}</span>@endif
</div>
</td>
<td class="px-5 py-4 max-w-[200px]">
<input type="text" value="{{ $link->full_url }}" readonly onclick="this.select()" class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded w-full cursor-text border-0">
</td>
<td class="px-5 py-4">
<span class="text-xs text-gray-500">{{ $link->creator?->name ?? '—' }}</span>
</td>
<td class="px-5 py-4 text-center">
@if ($regs > 0)
<a href="{{ route('admin.registrants.index', ['utm_source' => $link->utm_source, 'utm_medium' => $link->utm_medium, 'utm_campaign' => $link->utm_campaign]) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">{{ $regs }}</a>
@else
<span class="text-sm text-gray-400">0</span>
@endif
</td>
@if (Auth::user()->isClient())
<td class="px-5 py-4 text-center"><span class="text-sm {{ $approved > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">{{ $approved }}</span></td>
<td class="px-5 py-4 text-center"><span class="text-sm {{ $pending > 0 ? 'text-amber-600 font-bold' : 'text-gray-400' }}">{{ $pending }}</span></td>
<td class="px-5 py-4 text-center"><span class="text-sm {{ $rejected > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">{{ $rejected }}</span></td>
@else
<td class="px-5 py-4 text-center"><span class="text-sm {{ $checked > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">{{ $checked }}</span></td>
@endif
<td class="px-5 py-4 text-center">
<div class="flex items-center justify-center gap-1.5">
@if ($regs > 0)
<a href="{{ route('admin.registrants.index', ['utm_source' => $link->utm_source, 'utm_medium' => $link->utm_medium, 'utm_campaign' => $link->utm_campaign]) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View Registrants">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
</a>
@endif
<button onclick="editLink({{ $link->id }}, '{{ addslashes($link->name) }}', '{{ $link->base_url }}', '{{ $link->utm_source }}', '{{ $link->utm_medium }}', '{{ $link->utm_campaign }}', '{{ $link->utm_content ?? '' }}')" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</button>
<form action="{{ route('admin.management.utm-links.destroy', $link) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($link->name) }}?')">
@csrf @method('DELETE')
<button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
</button></form></div>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>

{{-- Total bar --}}
<div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-6 text-xs text-gray-500">
<span>Total links: <strong class="text-gray-700">{{ $utmLinks->count() }}</strong></span>
<span>Total registrations: <strong class="text-gray-700">{{ $utmLinks->sum(fn($l) => $l->registrationsCount()) }}</strong></span>
</div>
</div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
<svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
<p class="text-gray-400 font-medium">No UTM links yet</p>
<p class="text-xs text-gray-400 mt-1">Click the button below to create your first UTM link.</p>
<button onclick="openLinkModal()" class="mt-4 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ Create UTM Link</button>
</div>
@endif

{{-- Source breakdown --}}
@if ($sources->count())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100"><h2 class="text-base font-bold text-gray-900">All Sources (Auto-tracked)</h2><p class="text-xs text-gray-500 mt-0.5">{{ $totals['all'] }} total{{ Auth::user()->isClient() ? '' : ' · ' . $totals['checked'] . ' checked in' }}</p></div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Source</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrants</th>
@if (Auth::user()->isClient())
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</th>
@else
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked In</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate</th>
@endif
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($sources as $src)
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
@if ($src->utm_source)
<a href="{{ route('admin.registrants.index', ['utm_source' => $src->utm_source]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">{{ $src->utm_source }}</a>
@else
<a href="{{ route('admin.registrants.index', ['direct' => 1]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">Direct</a>
@endif
</td>
<td class="px-5 py-4">
@if ($src->total > 0)
<a href="{{ $src->utm_source ? route('admin.registrants.index', ['utm_source' => $src->utm_source]) : route('admin.registrants.index', ['direct' => 1]) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">{{ $src->total }}</a>
@else
<span class="text-sm text-gray-400">0</span>
@endif
</td>
@if (Auth::user()->isClient())
<td class="px-5 py-4"><span class="text-sm {{ ($src->approved_count ?? 0) > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">{{ $src->approved_count ?? 0 }}</span></td>
<td class="px-5 py-4"><span class="text-sm {{ ($src->pending_count ?? 0) > 0 ? 'text-amber-600 font-bold' : 'text-gray-400' }}">{{ $src->pending_count ?? 0 }}</span></td>
<td class="px-5 py-4"><span class="text-sm {{ ($src->rejected_count ?? 0) > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">{{ $src->rejected_count ?? 0 }}</span></td>
@else
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $src->checked_in }}</span></td>
<td class="px-5 py-4">
<div class="flex items-center gap-2">
<div class="h-2 w-20 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-400 rounded-full" style="width:{{ $src->total > 0 ? $src->checked_in/$src->total*100 : 0 }}%"></div></div>
<span class="text-xs text-gray-500">{{ $src->total > 0 ? round($src->checked_in/$src->total*100) : 0 }}%</span>
</div>
</td>
@endif
<td class="px-5 py-4 text-center">
@if ($src->total > 0)
<a href="{{ $src->utm_source ? route('admin.registrants.index', ['utm_source' => $src->utm_source]) : route('admin.registrants.index', ['direct' => 1]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="View Registrants">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
View
</a>
@else
<span class="text-xs text-gray-400">—</span>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endif
</div>
</main>
</div>

{{-- Create/Edit UTM Link Modal --}}
<div id="linkModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="linkModalTitle">Create UTM Link</h3></div>
<form id="linkForm" method="POST" action="{{ route('admin.management.utm-links.store') }}">
@csrf
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

@include('admin.partials.mobile-sidebar')
<script>
function openLinkModal() {
document.getElementById('linkModalTitle').textContent = 'Create UTM Link';
document.getElementById('linkForm').action = '{{ route("admin.management.utm-links.store") }}';
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
