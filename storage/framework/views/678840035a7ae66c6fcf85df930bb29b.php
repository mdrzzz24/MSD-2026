<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Template — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="<?php echo e(route('admin.templates.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Template</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Edit: <?php echo e($template->name); ?></h1></div></header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php if(session('success')): ?>
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6 max-w-7xl mx-auto"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="text-sm"><?php echo session('success'); ?></span></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 max-w-7xl mx-auto"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <?php endif; ?>
    <div class="max-w-7xl mx-auto">
    <form action="<?php echo e(route('admin.templates.update', $template)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="flex gap-4 mb-4">
            <div class="flex-1"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Template Name</label><input type="text" name="name" value="<?php echo e(old('name', $template->name)); ?>" required class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></div>
            <div class="w-48"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe</label><select name="type" required class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>" <?php echo e(old('type', $template->type)===$key?'selected':''); ?>><?php echo e($info['label']); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="flex-1"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject Email</label><input type="text" name="subject" value="<?php echo e(old('subject', $template->subject)); ?>" required class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></div>
        </div>
        <div class="flex gap-4">
            
            <div class="w-1/2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">HTML Content</label>
                        <div class="text-xs text-gray-400">Placeholder: <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">{{ name }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">{{ email }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">{{ password }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">{{ qr_code }}</code></div>
                    </div>
                    <textarea name="html_content" id="htmlEditor" rows="24" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-y leading-relaxed"><?php echo e(old('html_content', $template->html_content)); ?></textarea>
                </div>
            </div>
            
            <div class="w-1/2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Live Preview</span>
                        <span class="text-xs text-gray-400">Sample data</span>
                    </div>
                    <div class="bg-gray-50 p-4" style="min-height:500px;">
                        <iframe id="previewFrame" style="width:100%;height:500px;border:none;border-radius:8px;background:#fff;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <a href="<?php echo e(route('admin.templates.preview', $template)); ?>" target="_blank" class="px-5 py-3 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">🔍 Open in Tab</a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">💾 Update Template</button>
        </div>
    </form>
    </div>
</div>

<script>
(function() {
    var editor = document.getElementById('htmlEditor');
    var frame = document.getElementById('previewFrame');
    var doc = frame.contentDocument || frame.contentWindow.document;

    function updatePreview() {
        var html = editor.value
            .replace(/\{\{\s*name\s*\}\}/g, 'John Doe')
            .replace(/\{\{\s*email\s*\}\}/g, 'john@example.com')
            .replace(/\{\{\s*password\s*\}\}/g, '••••••••')
            .replace(/\{\{\s*status\s*\}\}/g, 'approved')
            .replace(/\{\{\s*unique_code\s*\}\}/g, '100724080000')
            .replace(/\{\{\s*admin_notes\s*\}\}/g, 'Sample note')
            .replace(/\{\{\s*workshop_name\s*\}\}/g, 'Sample Workshop')
            .replace(/\{\{\s*track_name\s*\}\}/g, 'Sample Session')
            .replace(/\{\{\s*event_date\s*\}\}/g, '12 Agustus 2026')
            .replace(/\{\{\s*login_url\s*\}\}/g, window.location.origin + '/login')
            .replace(/\{\{\s*qr_code\s*\}\}/g, '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=sample" alt="QR" style="width:200px;height:200px;display:block;margin:16px auto;">')
            .replace(/\{\{\s*qr_checkin_url\s*\}\}/g, window.location.origin + '/login');

        doc.open();
        doc.write(html);
        doc.close();
    }

    editor.addEventListener('input', updatePreview);
    setTimeout(updatePreview, 100);
})();
</script>
</main></div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/templates/edit.blade.php ENDPATH**/ ?>