<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Tracks — <?php echo e(config('app.name')); ?></title>
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
        <div><h1 class="text-lg font-bold text-gray-900">Manage Tracks</h1><p class="text-xs text-gray-500">Manage event tracks</p></div>
        <div class="flex items-center gap-2">
            <?php if(Auth::user()->canWrite()): ?>
            <button onclick="document.getElementById('addForm').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Track
            </button>
            <?php endif; ?>
        </div>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div id="addForm" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <form action="<?php echo e(route('admin.tracks.store')); ?>" method="POST" class="space-y-3">
            <?php echo csrf_field(); ?>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Title *</label><input type="text" name="title" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Save Track</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50/80">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Track</th>
                    <?php if(Auth::user()->canWrite()): ?>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Linked Agenda</th>
                    <?php endif; ?>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Registrants</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900"><?php echo e($tr->title); ?></p>
                                <?php if($tr->description): ?><p class="text-xs text-gray-400 mt-0.5 truncate max-w-[250px]"><?php echo e($tr->description); ?></p><?php endif; ?>
                            </td>
                            <?php if(Auth::user()->canWrite()): ?>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                <?php $linked = $tr->agendaItems; ?>
                                <?php if($linked->isNotEmpty()): ?>
                                    <?php $__currentLoopData = $linked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mb-1"><?php echo e($ai->title); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <?php $total = $tr->registrantsCount() + $tr->pendingCount() + $tr->rejectedCount(); ?>
                                <?php if($total > 0): ?>
                                    <div class="flex items-center gap-2 text-xs">
                                        <a href="<?php echo e(route('admin.tracks.registrants', $tr)); ?>" class="font-bold text-indigo-600 hover:text-indigo-800"><?php echo e($total); ?> total</a>
                                        <span class="text-emerald-600">✓<?php echo e($tr->registrantsCount()); ?></span>
                                        <span class="text-amber-600">⏳<?php echo e($tr->pendingCount()); ?></span>
                                        <span class="text-red-500">✕<?php echo e($tr->rejectedCount()); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php if($tr->is_active): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-1.5">
                                    <a href="<?php echo e(route('admin.tracks.registrants', $tr)); ?>" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">View</a>
                                    <?php if(Auth::user()->canWrite()): ?>
                                    <button onclick="editTrack(<?php echo e($tr->id); ?>,'<?php echo e(e($tr->title)); ?>','<?php echo e(e($tr->description)); ?>')" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition">Edit</button>
                                    <form action="<?php echo e(route('admin.tracks.toggle', $tr)); ?>" method="POST"><?php echo csrf_field(); ?>
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg <?php echo e($tr->is_active ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200'); ?> transition">
                                            <?php echo e($tr->is_active ? 'Disable' : 'Enable'); ?>

                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('admin.tracks.destroy', $tr)); ?>" method="POST" onsubmit="return confirm('Delete track <?php echo e($tr->title); ?>?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Delete</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-5 py-16 text-center"><p class="text-gray-400 font-medium">No tracks yet</p><p class="text-xs text-gray-400">Create your first track</p></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
</div>

<div id="editModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;width:100%;max-width:440px;padding:24px;">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Track</h3>
    <form id="editForm" method="POST" class="space-y-3"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Title *</label><input type="text" name="title" id="editTitle" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Description</label><textarea name="description" id="editDesc" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
        <div class="flex gap-2">
            <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Update</button>
        </div>
    </form>
  </div>
</div>
<script>
function editTrack(id,title,desc){
    document.getElementById('editForm').action='<?php echo e(route('admin.tracks.update', ['track' => '__ID__'])); ?>'.replace('__ID__', id);
    document.getElementById('editTitle').value=title;
    document.getElementById('editDesc').value=desc||'';
    document.getElementById('editModal').style.display='flex';
}
function closeEditModal(){document.getElementById('editModal').style.display='none';}
document.getElementById('editModal').addEventListener('click',function(e){if(e.target===this)closeEditModal();});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/tracks/index.blade.php ENDPATH**/ ?>