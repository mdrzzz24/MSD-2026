<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Database Backup — {{ config('app.name') }}</title>
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
<div><h1 class="text-lg font-bold text-gray-900">Database Backup</h1><p class="text-xs text-gray-500">Backup &amp; restore the entire database</p></div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@include('admin.partials.notification')

{{-- One-click Backup --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between flex-wrap gap-4">
<div>
<h2 class="text-base font-bold text-gray-900">Create New Backup</h2>
<p class="text-sm text-gray-500 mt-1">Creates a full dump of every table in the current database. This may take a few minutes depending on the data size.</p>
</div>
<form action="{{ route('admin.management.backup.store') }}" method="POST" onsubmit="return startBackup(this)">
@csrf
<button type="submit" id="backupBtn" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition flex items-center gap-2">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7M4 7c0 1.657 3.582 3 8 3s8-1.343 8-3M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3m0 5c0 1.657-3.582 3-8 3s-8-1.343-8-3"/></svg>
<span id="backupBtnText">Backup Now</span>
</button>
</form>
</div>

{{-- Backup List --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">Backup History</h2>
<span class="text-xs text-gray-400">{{ $files->count() }} file(s)</span>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">File Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Size</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@forelse ($files as $file)
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><span class="text-sm font-semibold text-gray-900">{{ $file['name'] }}</span></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ number_format($file['size'] / 1048576, 2) }} MB</span></td>
<td class="px-5 py-4"><span class="text-sm text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($file['modified'])->format('d M Y H:i') }}</span></td>
<td class="px-5 py-4 text-center">
<a href="{{ route('admin.management.backup.download', $file['name']) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium mr-3">Download</a>
<button type="button" onclick="useForRestore('{{ $file['name'] }}')" class="text-xs text-amber-600 hover:text-amber-800 font-medium mr-3">Restore</button>
<form action="{{ route('admin.management.backup.destroy', $file['name']) }}" method="POST" class="inline" onsubmit="return confirm('Delete backup file {{ $file['name'] }}?')">
@csrf @method('DELETE')
<button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400">No backups yet. Click "Backup Now" to create your first one.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>

{{-- Restore --}}
<div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-red-100 bg-red-50/50">
<h2 class="text-base font-bold text-red-700 flex items-center gap-2">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
Restore Database
</h2>
<p class="text-sm text-red-600 mt-1">This action will <strong>overwrite all current data</strong> in the database with the contents of the backup file. This cannot be undone — make sure you have a recent backup before continuing.</p>
</div>
<form action="{{ route('admin.management.backup.restore') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4" onsubmit="return confirmRestore(this)">
@csrf
<div>
<label class="block text-sm font-semibold text-gray-700 mb-1.5">Select a backup from history</label>
<select id="existingFileSelect" name="existing_file" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
<option value="">— Or upload a file below —</option>
@foreach ($files as $file)
<option value="{{ $file['name'] }}">{{ $file['name'] }} ({{ number_format($file['size'] / 1048576, 2) }} MB)</option>
@endforeach
</select>
</div>
<div>
<label class="block text-sm font-semibold text-gray-700 mb-1.5">Or upload a .sql file</label>
<input type="file" name="backup_file" accept=".sql" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
<p class="text-xs text-gray-400 mt-1">Upload size limit follows the server configuration (upload_max_filesize / post_max_size).</p>
</div>
<div>
<label class="block text-sm font-semibold text-gray-700 mb-1.5">Type <code class="px-1 py-0.5 bg-gray-100 rounded">RESTORE</code> to confirm</label>
<input type="text" name="confirm" required autocomplete="off" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
</div>
<div class="flex justify-end">
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-600 text-white hover:bg-red-700 transition">Restore Database</button>
</div>
</form>
</div>
</div>
</main>
</div>

@include('admin.partials.mobile-sidebar')
<script>
function useForRestore(name) {
    const select = document.getElementById('existingFileSelect');
    select.value = name;
    select.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
function confirmRestore(form) {
    const confirmInput = form.querySelector('input[name=confirm]').value;
    if (confirmInput !== 'RESTORE') {
        alert('Type "RESTORE" exactly to continue.');
        return false;
    }
    const hasExisting = form.querySelector('select[name=existing_file]').value;
    const hasUpload = form.querySelector('input[name=backup_file]').files.length > 0;
    if (!hasExisting && !hasUpload) {
        alert('Select a backup from history or upload a .sql file first.');
        return false;
    }
    return confirm('Are you sure you want to restore? All current data will be overwritten and cannot be recovered.');
}
function startBackup(form) {
    const btn = document.getElementById('backupBtn');
    const text = document.getElementById('backupBtnText');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-not-allowed');
    text.textContent = 'Creating backup...';
    return true;
}
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
    document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
