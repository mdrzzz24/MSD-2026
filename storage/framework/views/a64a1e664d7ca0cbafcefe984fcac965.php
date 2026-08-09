<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Send Reminder — <?php echo e(config('app.name')); ?></title>
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
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Gentle Reminder</h1>
                    <p class="text-xs text-gray-500">Send reminders to approved registrants</p>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">

            <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Template Active</p>
                        <?php if($activeTemplate): ?>
                            <p class="text-xs text-gray-400 mt-0.5">"<?php echo e($activeTemplate->name); ?>" — <?php echo e($activeTemplate->subject); ?></p>
                        <?php else: ?>
                            <p class="text-xs text-amber-600 mt-0.5">No active template for Gentle Reminder. Create a template first.</p>
                        <?php endif; ?>
                    </div>
                    <?php if($activeTemplate): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inactive
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('admin.email-logs.send-reminder')); ?>" id="reminderForm">
                <?php echo csrf_field(); ?>

                
                <div class="flex items-center gap-4 mb-4">
                    <button type="button" onclick="toggleAll(true)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                        Select All
                    </button>
                    <button type="button" onclick="toggleAll(false)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                        Deselect All
                    </button>
                    <span class="text-xs text-gray-400" id="selectedCount">0 selected</span>
                </div>

                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">
                                        <input type="checkbox" onchange="toggleAll(this.checked)" class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                                    </th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unique Code</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php $__empty_2 = true; $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-5 py-3">
                                            <input type="checkbox" name="registrant_ids[]" value="<?php echo e($r->id); ?>"
                                                   class="cb-item w-4 h-4 rounded border-gray-300 text-indigo-600"
                                                   onchange="updateCount()">
                                        </td>
                                        <td class="px-5 py-3 text-sm font-medium text-gray-900"><?php echo e($r->display_name); ?></td>
                                        <td class="px-5 py-3 text-sm text-gray-600"><?php echo e($r->email); ?></td>
                                        <td class="px-5 py-3 text-sm text-gray-400 font-mono"><?php echo e($r->unique_code); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <tr>
                                        <td colspan="4" class="px-5 py-16 text-center">
                                            <p class="text-gray-400 font-medium">No approved registrants yet.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-xs text-gray-400">Gentle Reminder will be sent to selected registrants.</p>
                    <button type="submit" <?php echo e(!$activeTemplate ? 'disabled' : ''); ?>

                            class="px-5 py-2.5 <?php echo e($activeTemplate ? 'bg-violet-500 hover:bg-violet-600 shadow-sm shadow-violet-200' : 'bg-gray-300 cursor-not-allowed'); ?> text-white text-sm font-semibold rounded-xl transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Send Reminder
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function toggleAll(checked) {
    document.querySelectorAll('.cb-item').forEach(cb => cb.checked = checked);
    updateCount();
    // Also toggle header checkbox
    document.querySelector('thead input[type="checkbox"]').checked = checked;
}
function updateCount() {
    const count = document.querySelectorAll('.cb-item:checked').length;
    document.getElementById('selectedCount').textContent = count + ' selected';
}
document.addEventListener('DOMContentLoaded', updateCount);
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/email-logs/send-reminder.blade.php ENDPATH**/ ?>