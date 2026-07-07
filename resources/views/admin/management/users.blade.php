<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Users — {{ config('app.name') }}</title>
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
<div><h1 class="text-lg font-bold text-gray-900">Admin Users</h1><p class="text-xs text-gray-500">Manage admin & super admin accounts</p></div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@if (session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm">{!! session('success') !!}</div>
@endif
@if (session('error'))
<div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl text-sm">{{ session('error') }}</div>
@endif

{{-- Users Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">Users</h2>
<button onclick="openUserModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ Add User</button>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($users as $u)
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><span class="text-sm font-semibold text-gray-900">{{ $u->name }}</span></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $u->email }}</span></td>
<td class="px-5 py-4">
@if ($u->role === 'super_admin')
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
<span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Super Admin
</span>
@else
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200">
<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Admin
</span>
@endif
</td>
<td class="px-5 py-4"><span class="text-sm text-gray-500">{{ $u->created_at->format('d M Y') }}</span></td>
<td class="px-5 py-4 text-center">
<button onclick="editUser('{{ $u->id }}', '{{ $u->name }}', '{{ $u->email }}', '{{ $u->role }}')" class="text-xs text-amber-600 hover:text-amber-800 font-medium mr-2">Edit</button>
@if ($u->id !== auth()->id())
<form action="{{ route('admin.management.users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $u->name }}?')">
@csrf @method('DELETE')
<button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
</form>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
</main>
</div>

{{-- Add/Edit User Modal --}}
<div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="userModalTitle">Add User</h3></div>
<form id="userForm" method="POST" action="{{ route('admin.management.users.store') }}">
@csrf
<input type="hidden" name="_method" id="userFormMethod" value="POST">
<input type="hidden" name="user_id" id="userId">
<div class="p-6 space-y-4">
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label><input type="text" id="userName" name="name" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label><input type="email" id="userEmail" name="email" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span id="pwdLabel" class="text-gray-400 font-normal">(leave blank to keep current)</span></label><input type="password" id="userPassword" name="password" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Role</label>
<select id="userRole" name="role" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
<option value="admin">Admin</option>
<option value="super_admin">Super Admin</option>
</select></div>
</div>
<div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
<button type="button" onclick="closeUserModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Save</button>
</div>
</form>
</div>
</div>

@include('admin.partials.mobile-sidebar')
<script>
function openUserModal() {
document.getElementById('userModalTitle').textContent = 'Add User';
document.getElementById('userForm').action = '{{ route("admin.management.users.store") }}';
document.getElementById('userFormMethod').value = 'POST';
document.getElementById('userId').value = '';
document.getElementById('userName').value = '';
document.getElementById('userEmail').value = '';
document.getElementById('userPassword').value = '';
document.getElementById('userPassword').required = true;
document.getElementById('pwdLabel').textContent = '(required for new user)';
document.getElementById('userRole').value = 'admin';
document.getElementById('userModal').classList.remove('hidden');
document.getElementById('userModal').classList.add('flex');
}
function editUser(id, name, email, role) {
document.getElementById('userModalTitle').textContent = 'Edit User';
document.getElementById('userForm').action = '/admin/management/users/' + id;
document.getElementById('userFormMethod').value = 'PUT';
document.getElementById('userId').value = id;
document.getElementById('userName').value = name;
document.getElementById('userEmail').value = email;
document.getElementById('userPassword').value = '';
document.getElementById('userPassword').required = false;
document.getElementById('pwdLabel').textContent = '(leave blank to keep current)';
document.getElementById('userRole').value = role;
document.getElementById('userModal').classList.remove('hidden');
document.getElementById('userModal').classList.add('flex');
}
function closeUserModal() {
document.getElementById('userModal').classList.add('hidden');
document.getElementById('userModal').classList.remove('flex');
}
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
