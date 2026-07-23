<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Admin Email — <?php echo e(config('app.name')); ?></title>
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
                    <h1 class="text-lg font-bold text-gray-900">Edit Admin Email</h1>
                    <p class="text-xs text-gray-500">Update test email recipient</p>
                </div>
                <a href="<?php echo e(route('admin.admin-emails.index')); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 max-w-2xl">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <form action="<?php echo e(route('admin.admin-emails.update', $adminEmail)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $adminEmail->name)); ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email', $adminEmail->email)); ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="is_active" value="1"
                                   <?php echo e(old('is_active', $adminEmail->is_active) ? 'checked' : ''); ?>

                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-400 ml-7">Inactive admin emails won't appear in the test email selection.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="px-5 py-2.5 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition">
                            Update
                        </button>
                        <a href="<?php echo e(route('admin.admin-emails.index')); ?>"
                           class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/admin-emails/edit.blade.php ENDPATH**/ ?>