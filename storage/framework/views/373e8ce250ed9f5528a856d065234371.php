<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Room Accounts (Mobile App) — <?php echo e(config('app.name')); ?></title>
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
<h1 class="text-lg font-bold text-gray-900">Mobile App Accounts <span class="text-indigo-500">(Room & Booth)</span></h1>
<p class="text-xs text-gray-500">Login accounts for the mobile apps — one per room (sessions) or booth (visits)</p>
</div>
</div>
<button onclick="openCreateModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Account</button>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
<?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-xs text-indigo-900">
<span class="font-semibold">How it works:</span>
<strong>Room accounts</strong> (session tracking): no sessions assigned → can track <strong>all sessions</strong> (default); once you assign sessions, it can only track <strong>those sessions</strong> in the mobile app.
<strong>Booth accounts</strong> (booth visits): each account is bound to <strong>one booth</strong> and can only scan that booth.
All accounts are login credentials for the mobile app only (no admin panel access).
</div>


<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">Room Accounts</h2>
<a href="<?php echo e(route('admin.management.users')); ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Manage in Admin Users →</a>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Room</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned Sessions</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__empty_1 = true; $__currentLoopData = $roomAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<span class="text-sm font-semibold text-gray-900"><?php echo e($a->name); ?></span>
<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">Room</span>
</td>
<td class="px-5 py-4">
<?php if($a->room): ?>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200"><?php echo e($a->room->name); ?></span>
<?php else: ?>
<span class="text-xs text-gray-400">—</span>
<?php endif; ?>
</td>
<td class="px-5 py-4"><span class="text-sm text-gray-600"><?php echo e($a->email); ?></span></td>
<td class="px-5 py-4">
<?php if($a->managed_agenda_items_count === 0): ?>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> All sessions (default)
</span>
<?php else: ?>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200"><?php echo e($a->managed_agenda_items_count); ?> session(s)</span>
<?php endif; ?>
</td>
<td class="px-5 py-4 text-center whitespace-nowrap">
<a href="<?php echo e(route('admin.room-accounts.sessions', $a)); ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 mr-2">⚙ Assign Sessions</a>
<a href="<?php echo e(route('admin.management.users')); ?>" class="text-xs text-amber-600 hover:text-amber-800 font-medium mr-2">Edit</a>
<?php if($a->id !== auth()->id()): ?>
<form action="<?php echo e(route('admin.management.users.destroy', $a)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete <?php echo e($a->name); ?>? This removes the mobile app account.')">
<?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
<button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">No room accounts yet. Create one below or via <code class="text-indigo-500">php artisan app:create-room-accounts</code>.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>


<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">Booth Accounts</h2>
<a href="<?php echo e(route('admin.management.users')); ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Manage in Admin Users →</a>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Booth</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
<?php $__empty_1 = true; $__currentLoopData = $boothAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<span class="text-sm font-semibold text-gray-900"><?php echo e($b->name); ?></span>
<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-50 text-violet-700 border border-violet-200">Booth</span>
</td>
<td class="px-5 py-4">
<?php if($b->booth): ?>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200"><?php echo e($b->booth->name); ?></span>
<?php else: ?>
<span class="text-xs text-gray-400">—</span>
<?php endif; ?>
</td>
<td class="px-5 py-4"><span class="text-sm text-gray-600"><?php echo e($b->email); ?></span></td>
<td class="px-5 py-4 text-center whitespace-nowrap">
<a href="<?php echo e(route('admin.management.users')); ?>" class="text-xs text-amber-600 hover:text-amber-800 font-medium mr-2">Edit</a>
<?php if($b->id !== auth()->id()): ?>
<form action="<?php echo e(route('admin.management.users.destroy', $b)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete <?php echo e($b->name); ?>? This removes the mobile app account.')">
<?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
<button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400">No booth accounts yet. Create one with the "+ New Account" button above.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</main>
</div>


<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900">New Mobile App Account</h3></div>
<form method="POST" action="<?php echo e(route('admin.room-accounts.store')); ?>">
<?php echo csrf_field(); ?>
<div class="p-6 space-y-4">
<div>
<label class="block text-sm font-semibold text-gray-700 mb-1.5">Type</label>
<div class="grid grid-cols-2 gap-2">
<label class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border border-indigo-300 bg-indigo-50 text-indigo-700 cursor-pointer">
<input type="radio" name="role" value="room" checked onchange="setAccountType('room')"> Room (sessions)
</label>
<label class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 cursor-pointer">
<input type="radio" name="role" value="booth" onchange="setAccountType('booth')"> Booth (visits)
</label>
</div>
</div>
<div id="acctRoomBox"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Room</label>
<select id="acctRoom" name="room_id" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" onchange="onRoomSelect()">
<option value="">— Select room —</option>
<?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($r->id); ?>" data-name="<?php echo e($r->name); ?>" data-slug="<?php echo e(\Illuminate\Support\Str::slug($r->name)); ?>"><?php echo e($r->name); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<p class="text-xs text-gray-400 mt-1">For session tracking. Name &amp; email auto-fill from the room.</p>
</div>
<div id="acctBoothBox" style="display:none;"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Booth</label>
<select id="acctBooth" name="booth_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" onchange="onBoothSelect()">
<option value="">— Select booth —</option>
<?php $__currentLoopData = $booths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($b->id); ?>" data-name="<?php echo e($b->name); ?>" data-slug="<?php echo e(\Illuminate\Support\Str::slug($b->name)); ?>"><?php echo e($b->name); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<p class="text-xs text-gray-400 mt-1">For booth visits — this account can only scan its booth.</p>
</div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label><input type="text" id="acctName" name="name" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label><input type="email" id="acctEmail" name="email" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label><input type="password" id="acctPassword" name="password" required minlength="6" value="room12345" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
</div>
<div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
<button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Create Account</button>
</div>
</form>
</div>
</div>

<?php echo $__env->make('admin.partials.mobile-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
function openCreateModal() {
document.getElementById('createModal').classList.remove('hidden');
document.getElementById('createModal').classList.add('flex');
setAccountType('room');
}
function closeCreateModal() {
document.getElementById('createModal').classList.add('hidden');
document.getElementById('createModal').classList.remove('flex');
}
function setAccountType(type) {
const roomBox = document.getElementById('acctRoomBox');
const boothBox = document.getElementById('acctBoothBox');
document.getElementById('acctRoom').required = type === 'room';
document.getElementById('acctBooth').required = type === 'booth';
roomBox.style.display = type === 'room' ? 'block' : 'none';
boothBox.style.display = type === 'booth' ? 'block' : 'none';
document.querySelectorAll('input[name=role]').forEach(r => {
const on = r.value === type;
r.checked = on;
r.closest('label').className = on
? 'flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border border-indigo-300 bg-indigo-50 text-indigo-700 cursor-pointer'
: 'flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 cursor-pointer';
});
document.getElementById('acctName').value = '';
document.getElementById('acctEmail').value = '';
document.getElementById('acctRoom').value = '';
document.getElementById('acctBooth').value = '';
}
function onRoomSelect() {
const sel = document.getElementById('acctRoom');
const opt = sel.options[sel.selectedIndex];
const nameEl = document.getElementById('acctName');
const emailEl = document.getElementById('acctEmail');
if (!opt.value) return;
if (!nameEl.value.trim()) nameEl.value = opt.dataset.name || '';
if (!emailEl.value.trim()) emailEl.value = (opt.dataset.slug || '') + '@msd26.app';
}
function onBoothSelect() {
const sel = document.getElementById('acctBooth');
const opt = sel.options[sel.selectedIndex];
const nameEl = document.getElementById('acctName');
const emailEl = document.getElementById('acctEmail');
if (!opt.value) return;
if (!nameEl.value.trim()) nameEl.value = opt.dataset.name || '';
if (!emailEl.value.trim()) emailEl.value = (opt.dataset.slug || '') + '@booth.msd26.app';
}
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/room-accounts/index.blade.php ENDPATH**/ ?>