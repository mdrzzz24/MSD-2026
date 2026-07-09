<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Workshop — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="<?php echo e(route('admin.workshops.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshop</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Edit: <?php echo e($workshop->title); ?></h1></div></header>
<div class="p-4 sm:p-6 lg:p-8"><div class="max-w-2xl"><div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <?php if($errors->any()): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <?php endif; ?>
    <form action="<?php echo e(route('admin.workshops.update', $workshop)); ?>" method="POST" class="space-y-5">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Workshop Title</label><input type="text" name="title" value="<?php echo e(old('title', $workshop->title)); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition resize-y"><?php echo e(old('description', $workshop->description)); ?></textarea></div>
        <?php if($workshop->agendaItems()->exists()): ?>
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-sm text-indigo-700">
                <strong>🔗 Linked to Agenda:</strong>
                <?php $__currentLoopData = $workshop->agendaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-block mt-1 px-2 py-0.5 bg-white rounded text-xs font-medium"><?php echo e($ai->title); ?> (<?php echo e($ai->timeLabel()); ?>)</span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                <strong>💡 Not linked to any agenda yet.</strong> Go to <strong>Agenda</strong> → Create/Edit to link this workshop.
            </div>
        <?php endif; ?>
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">Update Workshop</button>
    </form>
</div></div></div>
</main>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/workshops/edit.blade.php ENDPATH**/ ?>