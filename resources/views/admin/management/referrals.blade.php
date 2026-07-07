<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Referral Codes — {{ config('app.name') }}</title>
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
<div><h1 class="text-lg font-bold text-gray-900">Referral Codes</h1><p class="text-xs text-gray-500">Create, manage & track referral code performance</p></div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@if (session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm">{!! session('success') !!}</div>
@endif

{{-- Referral Code Builder --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
<h2 class="text-base font-bold text-gray-900">Referral Code Builder</h2>
<button onclick="openReferralModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Code</button>
</div>
<div class="p-6">
<p class="text-sm text-gray-500 mb-4">Create unique referral codes for Account Managers, partners, or campaigns. Each code can have a maximum usage limit.</p>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
<div class="bg-gray-50 rounded-lg p-3 text-center"><span class="text-gray-400">Code</span><p class="font-semibold text-gray-700 mt-0.5">Unique identifier</p></div>
<div class="bg-gray-50 rounded-lg p-3 text-center"><span class="text-gray-400">Owner</span><p class="font-semibold text-gray-700 mt-0.5">Account Manager name</p></div>
<div class="bg-gray-50 rounded-lg p-3 text-center"><span class="text-gray-400">Auto-tracked</span><p class="font-semibold text-gray-700 mt-0.5">Counts on registration</p></div>
</div>
</div>
</div>

{{-- Managed Referral Codes Table --}}
@if ($referralCodes->count())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">{{ Auth::user()->role === 'super_admin' ? 'All Referral Codes' : 'My Referral Codes' }}</h2>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Owner</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usage</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($referralCodes as $rc)
@php $used = $rc->registrants()->count(); @endphp
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<code class="text-sm font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $rc->code }}</code>
@if ($rc->description)
<div class="text-xs text-gray-400 mt-1">{{ $rc->description }}</div>
@endif
</td>
<td class="px-5 py-4">
<span class="text-sm text-gray-700">{{ $rc->owner_name ?: '&mdash;' }}</span>
</td>
<td class="px-5 py-4">
<span class="text-xs text-gray-500">{{ $rc->creator?->name ?? '—' }}</span>
</td>
<td class="px-5 py-4">
<div class="flex items-center gap-2">
<span class="text-sm font-bold {{ $rc->max_uses > 0 && $used >= $rc->max_uses ? 'text-red-600' : 'text-gray-900' }}">{{ $used }}</span>
@if ($rc->max_uses > 0)
<span class="text-xs text-gray-400">/ {{ $rc->max_uses }}</span>
<div class="h-2 w-16 bg-gray-100 rounded-full overflow-hidden">
<div class="h-full {{ $used >= $rc->max_uses ? 'bg-red-400' : 'bg-emerald-400' }} rounded-full" style="width:{{ min(100, round($used/$rc->max_uses*100)) }}%"></div>
</div>
@else
<span class="text-xs text-gray-400">unlimited</span>
@endif
</div>
</td>
<td class="px-5 py-4">
@if ($rc->is_active)
<span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded-full">Active</span>
@else
<span class="text-[10px] font-medium bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full">Inactive</span>
@endif
</td>
<td class="px-5 py-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<button onclick="editReferral({{ $rc->id }}, '{{ addslashes($rc->code) }}', '{{ addslashes($rc->owner_name ?? '') }}', '{{ addslashes($rc->description ?? '') }}', {{ $rc->max_uses }})" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</button>
<form action="{{ route('admin.management.referral-codes.destroy', $rc) }}" method="POST" class="inline" onsubmit="return confirm('Delete code {{ addslashes($rc->code) }}?')">
@csrf @method('DELETE')
<button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
</button>
</form>
</div>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-6 text-xs text-gray-500">
<span>Total codes: <strong class="text-gray-700">{{ $referralCodes->count() }}</strong></span>
<span>Total registrations: <strong class="text-gray-700">{{ $referralCodes->sum(fn($c) => $c->registrants()->count()) }}</strong></span>
</div>
</div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
<svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
<p class="text-gray-400 font-medium">No referral codes yet</p>
<p class="text-xs text-gray-400 mt-1">Create your first code to start tracking referrals.</p>
<button onclick="openReferralModal()" class="mt-4 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ Create Code</button>
</div>
@endif

{{-- Unmanaged / Auto-tracked Codes --}}
@if ($unmanagedCodes->count())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">Auto-tracked Codes (from registrations)</h2>
<p class="text-xs text-gray-500 mt-0.5">Codes entered by registrants that are not yet in the managed list above</p>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrants</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked In</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($unmanagedCodes as $c)
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><code class="text-sm font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">{{ $c->referral_code }}</code></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $c->total }}</span></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $c->checked_in }}</span></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $c->total > 0 ? round($c->checked_in/$c->total*100) : 0 }}%</span></td>
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

{{-- Create/Edit Referral Code Modal --}}
<div id="referralModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="referralModalTitle">Create Referral Code</h3></div>
<form id="referralForm" method="POST" action="{{ route('admin.management.referral-codes.store') }}">
@csrf
<input type="hidden" name="_method" id="referralFormMethod" value="POST">
<input type="hidden" name="code_id" id="codeId">
<div class="p-6 space-y-3">
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
<input type="text" id="codeCode" name="code" required placeholder="e.g. AM-JOHN-2026" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<p class="text-xs text-gray-400 mt-1">Use uppercase letters, numbers, and hyphens. Must be unique.</p></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Owner Name</label>
<input type="text" id="codeOwner" name="owner_name" placeholder="e.g. John Doe (Account Manager)" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
<textarea id="codeDescription" name="description" rows="2" placeholder="Purpose or notes about this code" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1">Max Uses <span class="text-gray-400 font-normal">(0 = unlimited)</span></label>
<input type="number" id="codeMaxUses" name="max_uses" min="0" value="0" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
</div>
<div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
<button type="button" onclick="closeReferralModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Save Code</button>
</div>
</form>
</div>
</div>

@include('admin.partials.mobile-sidebar')
<script>
function openReferralModal() {
document.getElementById('referralModalTitle').textContent = 'Create Referral Code';
document.getElementById('referralForm').action = '{{ route("admin.management.referral-codes.store") }}';
document.getElementById('referralFormMethod').value = 'POST';
document.getElementById('codeId').value = '';
document.getElementById('codeCode').value = '';
document.getElementById('codeOwner').value = '{{ Auth::user()->name }}';
document.getElementById('codeDescription').value = '';
document.getElementById('codeMaxUses').value = '0';
document.getElementById('referralModal').classList.remove('hidden');
document.getElementById('referralModal').classList.add('flex');
}
function editReferral(id, code, owner, desc, maxUses) {
document.getElementById('referralModalTitle').textContent = 'Edit Referral Code';
document.getElementById('referralForm').action = '/admin/management/referral-codes/' + id;
document.getElementById('referralFormMethod').value = 'PUT';
document.getElementById('codeId').value = id;
document.getElementById('codeCode').value = code;
document.getElementById('codeOwner').value = owner;
document.getElementById('codeDescription').value = desc;
document.getElementById('codeMaxUses').value = maxUses;
document.getElementById('referralModal').classList.remove('hidden');
document.getElementById('referralModal').classList.add('flex');
}
function closeReferralModal() {
document.getElementById('referralModal').classList.add('hidden');
document.getElementById('referralModal').classList.remove('flex');
}
document.getElementById('referralModal')?.addEventListener('click', function(e) {
if (e.target === this) closeReferralModal();
});
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>