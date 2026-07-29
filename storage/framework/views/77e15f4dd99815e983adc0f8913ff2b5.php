<?php if(auth()->guard()->check()): ?>
<?php if(session('impersonating')): ?>
<div style="position:fixed; top:0; left:0; right:0; z-index:9999;" class="bg-amber-500 text-black px-4 py-2.5 text-sm flex items-center justify-between shadow-md">
    <span class="flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        <strong>🔀 Impersonating:</strong> <?php echo e(Auth::user()->name); ?> (<?php echo e(Auth::user()->email); ?>)
        <?php if(session('impersonator_name')): ?>
        <span class="text-amber-800">— by <?php echo e(session('impersonator_name')); ?></span>
        <?php endif; ?>
    </span>
    <form action="<?php echo e(route('admin.management.impersonate.leave')); ?>" method="POST" class="inline flex-shrink-0">
        <?php echo csrf_field(); ?>
        <button class="bg-black text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-800 transition shadow-sm">
            ⬅ Return to Admin
        </button>
    </form>
</div>
<style>
body { padding-top: 44px !important; }
aside.fixed { top: 44px !important; height: calc(100vh - 44px) !important; }
header.sticky { top: 44px !important; }
</style>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/partials/impersonation-banner.blade.php ENDPATH**/ ?>