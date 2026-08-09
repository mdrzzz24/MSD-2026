<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Templates — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    function openTestModal(templateId, templateName) {
        window.dispatchEvent(new CustomEvent('open-test-modal', {
            detail: { templateId, templateName }
        }));
    }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .table-row-transition { transition: all 0.15s ease; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">

    
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main class="flex-1 lg:ml-64">
        
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Email Templates</h1>
                        <p class="text-xs text-gray-500">Manage all email templates — super admin only</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php $autoEmail = \Illuminate\Support\Facades\Cache::get('auto_registration_email', true); ?>
                    <form action="<?php echo e(route('admin.templates.toggle-auto-email')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl transition-all <?php echo e($autoEmail ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200'); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($autoEmail ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'); ?>"></span>
                            Auto-Email: <?php echo e($autoEmail ? 'ON' : 'OFF'); ?>

                        </button>
                    </form>

                    <a href="<?php echo e(route('admin.templates.upload')); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload HTML
                    </a>
                    <a href="<?php echo e(route('admin.templates.create')); ?>"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Template
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">

            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php
                $totalTemplates = $templates->count();
                $activeCount = $templates->where('is_active', true)->count();
                $typeCount = collect($types)->count();
            ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500">Total</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo e($totalTemplates); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500">Active</p>
                            <p class="text-xl font-bold text-emerald-600"><?php echo e($activeCount); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500">Types</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo e($typeCount); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500">Fallback</p>
                            <p class="text-xl font-bold text-amber-600"><?php echo e($typeCount - count($activeTypes)); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex flex-wrap items-center gap-2 mb-4 px-1">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-1">Types:</span>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $hasActive = in_array($key, $activeTypes); ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?php echo e($hasActive ? 'bg-' . $info['color'] . '-50 text-' . $info['color'] . '-700 border border-' . $info['color'] . '-200' : 'bg-gray-50 text-gray-400 border border-gray-200'); ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?php echo e($hasActive ? 'bg-' . $info['color'] . '-500' : 'bg-gray-300'); ?>"></span>
                        <?php echo e($info['label']); ?>

                        <?php if($hasActive): ?>
                            <svg class="w-3 h-3 text-<?php echo e($info['color']); ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <?php endif; ?>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Template</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $color = \App\Models\EmailTemplate::typeColor($t->type);
                                    $label = \App\Models\EmailTemplate::typeLabel($t->type);
                                ?>
                                <tr class="hover:bg-gray-50/50 transition table-row-transition group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-600 group-hover:scale-105 transition">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <span class="text-sm font-semibold text-gray-900"><?php echo e($t->name); ?></span>
                                                <?php if($t->description): ?>
                                                    <p class="text-xs text-gray-400 mt-0.5"><?php echo e($t->description); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-700 border border-<?php echo e($color); ?>-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-<?php echo e($color); ?>-500"></span>
                                            <?php echo e($label); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="text-sm text-gray-600 font-mono text-xs"><?php echo e($t->subject); ?></span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <?php if($t->is_active): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            
                                            <a href="<?php echo e(route('admin.templates.preview', $t)); ?>" target="_blank"
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-sky-200 text-sky-700 bg-sky-50 hover:bg-sky-100 hover:border-sky-300 hover:shadow-sm transition-all" title="Preview template">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Preview
                                            </a>
                                            
                                            <a href="<?php echo e(route('admin.templates.edit', $t)); ?>"
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100 hover:border-amber-300 hover:shadow-sm transition-all" title="Edit template">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                            
                                            <button onclick="openTestModal(<?php echo e($t->id); ?>, '<?php echo e($t->name); ?>')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100 hover:border-teal-300 hover:shadow-sm transition-all" title="Send test email">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                Test
                                            </button>
                                            
                                            <span class="w-px h-5 bg-gray-200 mx-1"></span>
                                            
                                            <form action="<?php echo e(route('admin.templates.toggle', $t)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border transition-all <?php echo e($t->is_active ? 'border-gray-200 text-gray-500 bg-white hover:border-amber-200 hover:text-amber-700 hover:bg-amber-50 hover:shadow-sm' : 'border-gray-200 text-gray-500 bg-white hover:border-emerald-200 hover:text-emerald-700 hover:bg-emerald-50 hover:shadow-sm'); ?>"
                                                   title="<?php echo e($t->is_active ? 'Deactivate template' : 'Activate template'); ?>">
                                                    <?php if($t->is_active): ?>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Deactivate
                                                    <?php else: ?>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Activate
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                            
                                            <form action="<?php echo e(route('admin.templates.destroy', $t)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete &quot;<?php echo e($t->name); ?>&quot;? This action cannot be undone.')">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-red-200 text-red-500 bg-white hover:bg-red-50 hover:border-red-300 hover:text-red-700 hover:shadow-sm transition-all"
                                                   title="Delete template">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-gray-900 font-semibold">No templates yet</p>
                                                <p class="text-sm text-gray-400 mt-0.5">Upload or create an email template to get started</p>
                                            </div>
                                            <a href="<?php echo e(route('admin.templates.create')); ?>"
                                               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Create Template
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            
            <div class="mt-6">
                <details class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group">
                    <summary class="px-5 py-4 flex items-center justify-between cursor-pointer hover:bg-gray-50/50 transition list-none">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 group-open:text-amber-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h2 class="text-sm font-bold text-gray-800">Fallback Email Status</h2>
                            <span class="text-xs text-gray-400 font-normal">— Types without an active template</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="px-5 pb-5 border-t border-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $hasActive = in_array($key, $activeTypes);
                                    $color = $info['color'];
                                ?>
                                <div class="rounded-xl border p-4 transition hover:shadow-sm <?php echo e($hasActive ? 'bg-emerald-50/50 border-emerald-200' : 'bg-amber-50/50 border-amber-200'); ?>">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-600"><?php echo e($info['label']); ?></span>
                                        <span class="w-2 h-2 rounded-full <?php echo e($hasActive ? 'bg-emerald-500' : 'bg-amber-500'); ?>"></span>
                                    </div>
                                    <?php if($hasActive): ?>
                                        <p class="text-xs text-emerald-600 font-medium">✅ Template active</p>
                                    <?php else: ?>
                                        <p class="text-xs text-amber-600 font-medium">⚠️ Using fallback view</p>
                                        <a href="<?php echo e(route('admin.templates.create-from-fallback')); ?>?type=<?php echo e($key); ?>"
                                           class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Create from fallback →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </details>
            </div>

        </div>
    </main>
</div>




<div x-data="testModal()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 w-full max-w-lg p-6">
            
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Send Test Email</h3>
                        <p class="text-xs text-gray-400" x-text="'Template: ' + templateName"></p>
                    </div>
                </div>
                <button @click="open = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            
            <form action="<?php echo e(route('admin.admin-emails.send-test')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="template_id" x-bind:value="templateId">

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Select test recipients:</label>
                    <?php $adminEmails = \App\Models\AdminEmail::active()->get(); ?>
                    <?php $__empty_1 = true; $__currentLoopData = $adminEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ae): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 cursor-pointer transition border border-transparent hover:border-gray-100 mb-1">
                            <input type="checkbox" name="admin_email_ids[]" value="<?php echo e($ae->id); ?>"
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800"><?php echo e($ae->name); ?></span>
                                <span class="text-xs text-gray-400 ml-1.5">(<?php echo e($ae->email); ?>)</span>
                            </div>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-8">
                            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 mb-1">No test recipients yet.</p>
                            <a href="<?php echo e(route('admin.admin-emails.create')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Add recipient →</a>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-xl transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Send Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function testModal() {
    return {
        open: false,
        templateId: null,
        templateName: '',
        init() {
            window.addEventListener('open-test-modal', (e) => {
                this.templateId = e.detail.templateId;
                this.templateName = e.detail.templateName;
                this.open = true;
            });
        }
    }
}
</script>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/templates/index.blade.php ENDPATH**/ ?>