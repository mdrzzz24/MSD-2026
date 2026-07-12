
<?php if(session('success') || session('error')): ?>
    <?php
        $type = session('success') ? 'success' : 'error';
        $message = session($type);
        $isHtml = $type === 'success';
    ?>
    <div id="notif-<?php echo e($type); ?>"
         style="position:fixed; top:20px; right:20px; z-index:9999; max-width:420px; width:100%;"
         class="flex items-start gap-3 px-5 py-4 rounded-2xl shadow-lg transition-all duration-500 ease-in-out
                <?php echo e($type === 'success'
                    ? 'bg-emerald-50 border border-emerald-200 text-emerald-800'
                    : 'bg-red-50 border border-red-200 text-red-800'); ?>">
        <?php if($type === 'success'): ?>
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        <?php else: ?>
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        <?php endif; ?>
        <span class="text-sm flex-1"><?php echo $isHtml ? $message : e($message); ?></span>
        <button onclick="this.parentElement.style.display='none'" class="flex-shrink-0 ml-2 opacity-60 hover:opacity-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <script>
        (function(){
            var el = document.getElementById('notif-<?php echo e($type); ?>');
            if(!el) return;
            // Slide in from right
            el.style.transform = 'translateX(120%)';
            el.style.opacity = '0';
            requestAnimationFrame(function(){
                el.style.transform = 'translateX(0)';
                el.style.opacity = '1';
            });
            // Auto dismiss after 3 detik
            setTimeout(function(){
                el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateX(120%)';
                setTimeout(function(){ el.style.display = 'none'; }, 400);
            }, 3000);
        })();
    </script>
<?php endif; ?>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/partials/notification.blade.php ENDPATH**/ ?>