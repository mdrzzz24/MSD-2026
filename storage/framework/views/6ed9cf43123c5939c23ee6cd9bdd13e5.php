<aside class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-gray-200 fixed inset-y-0 z-40">
    <div class="flex items-center justify-center h-20 px-6 border-b border-gray-200" style="background:linear-gradient(135deg, #050d2a, #0a1a4a)">
        <img src="<?php echo e(asset('img/logo-msd.png')); ?>" alt="MSD" style="height:48px;width:auto;filter:brightness(0) invert(1)">
    </div>
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        
        <a href="<?php echo e(route('admin.dashboard')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
            Dashboard
        </a>

        
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Participants</p>
        </div>
        <?php if(Auth::user()->hasPermission('registrants')): ?>
        <a href="<?php echo e(route('admin.registrants.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.registrants.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Registrants
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('workshops')): ?>
        <a href="<?php echo e(route('admin.workshops.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.workshops.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Workshops
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('workshop_registrants')): ?>
        <a href="<?php echo e(route('admin.workshop-registrants.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.workshop-registrants.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Workshop Registrants
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('tracks')): ?>
        <a href="<?php echo e(route('admin.tracks.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.tracks.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Tracks
        </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->hasPermission('agenda') || Auth::user()->hasPermission('speakers') || Auth::user()->hasPermission('time_slots') || Auth::user()->hasPermission('rooms')): ?>
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Event Content</p>
        </div>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('agenda')): ?>
        <a href="<?php echo e(route('admin.agenda.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.agenda.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Agenda
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('speakers')): ?>
        <a href="<?php echo e(route('admin.speakers.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.speakers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Speakers
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('time_slots')): ?>
        <a href="<?php echo e(route('admin.time-slots.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.time-slots.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Time Slots
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('rooms')): ?>
        <a href="<?php echo e(route('admin.rooms.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.rooms.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Rooms & Floors
        </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->hasPermission('email_templates') || Auth::user()->hasPermission('utm_sources') || Auth::user()->hasPermission('qr_codes')): ?>
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Marketing</p>
        </div>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('email_templates')): ?>
        <a href="<?php echo e(route('admin.templates.index')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.templates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Email Templates
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->isSuperAdmin()): ?>
        <a href="<?php echo e(route('admin.mail-settings.edit')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.mail-settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Mail Settings
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('utm_sources')): ?>
        <a href="<?php echo e(route('admin.management.utm')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.management.utm') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
            UTM Sources
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('qr_codes')): ?>
        <a href="<?php echo e(route('admin.management.qr')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.management.qr') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            QR Codes
        </a>
        <?php endif; ?>

        
        <?php if(Auth::user()->hasPermission('checkin_log') || Auth::user()->hasPermission('admin_users')): ?>
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">System</p>
        </div>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('checkin_log')): ?>
        <a href="<?php echo e(route('admin.management.checkin')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.management.checkin') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Check-in Log
        </a>
        <?php endif; ?>
        <?php if(Auth::user()->hasPermission('admin_users')): ?>
        <a href="<?php echo e(route('admin.management.users')); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?php echo e(request()->routeIs('admin.management.users') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'); ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
            Admin Users
        </a>
        <?php endif; ?>
        <div class="pt-4">
            <p class="px-3 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-widest">Account</p>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>
    <div class="p-4 border-t border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e(Auth::user()->name); ?></p>
                <p class="text-xs text-gray-500 truncate">
                    <?php if(Auth::user()->isClient()): ?>
                        Client
                    <?php elseif(Auth::user()->isSuperAdmin()): ?>
                        Super Admin
                    <?php else: ?>
                        Administrator
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</aside>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/partials/sidebar.blade.php ENDPATH**/ ?>