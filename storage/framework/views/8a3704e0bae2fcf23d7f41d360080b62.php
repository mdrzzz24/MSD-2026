<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Speakers — <?php echo e(config('app.name')); ?></title>
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
        <div><h1 class="text-lg font-bold text-gray-900">Speakers</h1><p class="text-xs text-gray-500">Manage event speakers</p></div>
        <button onclick="document.getElementById('addForm').classList.toggle('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">+ Add Speaker</button>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div id="addForm" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">New Speaker</h3>
        <form action="<?php echo e(route('admin.speakers.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
            <?php echo csrf_field(); ?>
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Photo</label>
                    <div class="relative">
                        <img id="addPhotoPreview" src="" class="w-20 h-20 rounded-xl object-cover border border-gray-200 hidden">
                        <div id="addPhotoPlaceholder" class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-2xl cursor-pointer hover:border-indigo-400 hover:text-indigo-400 transition" onclick="document.getElementById('addPhotoInput').click()">+</div>
                        <input type="file" name="photo" id="addPhotoInput" accept="image/*" class="hidden" onchange="previewAddPhoto(this)">
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div><label class="block text-xs font-semibold text-gray-700 mb-1">Name *</label><input type="text" name="name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Title</label><input type="text" name="title" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Company</label><input type="text" name="company" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                    </div>
                </div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Bio</label><textarea name="bio" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Save Speaker</button>
        </form>
    </div>

    <script>
    function previewAddPhoto(input) {
        const preview = document.getElementById('addPhotoPreview');
        const placeholder = document.getElementById('addPhotoPlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 <?php echo e($s->is_active ? '' : 'opacity-50'); ?>">
            <div class="flex items-start gap-4">
                <?php if($s->photo): ?>
                    <img src="<?php echo e(asset('storage/' . $s->photo)); ?>" alt="<?php echo e($s->name); ?>" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                <?php else: ?>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold flex-shrink-0"><?php echo e(strtoupper(substr($s->name,0,1))); ?></div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-gray-900"><?php echo e($s->name); ?></h3>
                    <?php if($s->title): ?><p class="text-xs text-gray-500"><?php echo e($s->title); ?></p><?php endif; ?>
                    <?php if($s->company): ?><p class="text-xs text-gray-400"><?php echo e($s->company); ?></p><?php endif; ?>
                    <div class="flex items-center gap-1 mt-2">
                        <?php if($s->is_active): ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="flex gap-1 mt-3 pt-3 border-t border-gray-100">
                <button onclick="editSpeaker(<?php echo e($s->id); ?>,'<?php echo e(e($s->name)); ?>','<?php echo e(e($s->title)); ?>','<?php echo e(e($s->company)); ?>','<?php echo e(e($s->photo)); ?>','<?php echo e(e($s->bio)); ?>')" class="flex-1 px-2 py-1.5 text-[11px] font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-center">Edit</button>
                <form action="<?php echo e(route('admin.speakers.toggle', $s)); ?>" method="POST" class="flex-1"><?php echo csrf_field(); ?><button class="w-full px-2 py-1.5 text-[11px] font-medium rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 transition"><?php echo e($s->is_active ? 'Disable' : 'Enable'); ?></button></form>
                <form action="<?php echo e(route('admin.speakers.destroy', $s)); ?>" method="POST" class="flex-1" onsubmit="return confirm('Delete <?php echo e($s->name); ?>?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="w-full px-2 py-1.5 text-[11px] font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">Delete</button></form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-12 text-gray-400 text-sm">No speakers yet. Add your first speaker!</div>
        <?php endif; ?>
    </div>
</div>
</main>
</div>


<div id="editModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);width:100%;max-width:480px;padding:24px;">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Speaker</h3>
    <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-3">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Photo</label>
                <div class="relative">
                    <img id="editPhotoPreview" src="" class="w-20 h-20 rounded-xl object-cover border border-gray-200 hidden">
                    <div id="editPhotoPlaceholder" class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-2xl cursor-pointer hover:border-indigo-400 hover:text-indigo-400 transition" onclick="document.getElementById('editPhotoInput').click()">+</div>
                    <input type="file" name="photo" id="editPhotoInput" accept="image/*" class="hidden" onchange="previewEditPhoto(this)">
                    <input type="hidden" name="remove_photo" id="editRemovePhoto" value="0">
                </div>
                <button type="button" id="editRemovePhotoBtn" class="hidden mt-1 text-[10px] text-red-500 hover:text-red-700 font-medium" onclick="removeEditPhoto()">Remove photo</button>
            </div>
            <div class="flex-1 space-y-3">
                <div><label class="block text-xs font-semibold text-gray-700 mb-1">Name *</label><input type="text" name="name" id="editName" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-semibold text-gray-700 mb-1">Title</label><input type="text" name="title" id="editTitle" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                    <div><label class="block text-xs font-semibold text-gray-700 mb-1">Company</label><input type="text" name="company" id="editCompany" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                </div>
            </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Bio</label><textarea name="bio" id="editBio" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
        <div class="flex gap-2">
            <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">Update</button>
        </div>
    </form>
  </div>
</div>

<script>
let _editCurrentPhoto = '';

function editSpeaker(id,name,title,company,photo,bio){
    document.getElementById('editForm').action='<?php echo e(route('admin.speakers.update', ['speaker' => '__ID__'])); ?>'.replace('__ID__', id);
    document.getElementById('editName').value=name;
    document.getElementById('editTitle').value=title;
    document.getElementById('editCompany').value=company;
    document.getElementById('editBio').value=bio;
    document.getElementById('editPhotoInput').value='';
    document.getElementById('editRemovePhoto').value='0';

    const preview = document.getElementById('editPhotoPreview');
    const placeholder = document.getElementById('editPhotoPlaceholder');
    const removeBtn = document.getElementById('editRemovePhotoBtn');

    if (photo) {
        _editCurrentPhoto = photo;
        preview.src = '<?php echo e(asset('storage')); ?>/' + photo;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        removeBtn.classList.remove('hidden');
    } else {
        _editCurrentPhoto = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }

    document.getElementById('editModal').style.display='flex';
}

function previewEditPhoto(input) {
    const preview = document.getElementById('editPhotoPreview');
    const placeholder = document.getElementById('editPhotoPlaceholder');
    const removeBtn = document.getElementById('editRemovePhotoBtn');
    document.getElementById('editRemovePhoto').value='0';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            removeBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeEditPhoto() {
    document.getElementById('editPhotoInput').value='';
    document.getElementById('editRemovePhoto').value='1';
    document.getElementById('editPhotoPreview').classList.add('hidden');
    document.getElementById('editPhotoPlaceholder').classList.remove('hidden');
    document.getElementById('editRemovePhotoBtn').classList.add('hidden');
}

function closeEditModal(){document.getElementById('editModal').style.display='none';}
document.getElementById('editModal').addEventListener('click',function(e){if(e.target===this)closeEditModal();});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/speakers/index.blade.php ENDPATH**/ ?>