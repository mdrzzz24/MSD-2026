@auth
@if (session('impersonating'))
<div style="position:fixed; top:0; left:0; right:0; z-index:9999;" class="bg-amber-500 text-black px-4 py-2.5 text-sm flex items-center justify-between shadow-md">
    <span class="flex items-center gap-2 ml-64">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        <strong> Impersonating:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})
        @if (session('impersonator_name'))
        <span class="text-amber-800">— by {{ session('impersonator_name') }}</span>
        @endif
    </span>
    <form action="{{ route('admin.management.impersonate.leave') }}" method="POST" class="inline flex-shrink-0 mr-4">
        @csrf
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
@endif
@endauth
<aside class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-gray-200 fixed inset-y-0 z-40">
    <div class="flex items-center justify-center h-20 px-6 border-b border-gray-200" style="background:linear-gradient(135deg, #050d2a, #0a1a4a)">
        <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" style="height:48px;width:auto;filter:brightness(0) invert(1)">
    </div>
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">

        {{-- ═══ Dashboard ═══ --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
            Dashboard
        </a>

        {{-- ═══ Participants ═══ --}}
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Participants</p>
        </div>
        @if (Auth::user()->hasPermission('registrants'))
        <a href="{{ route('admin.registrants.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.registrants.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Registrants
        </a>
        @if (!Auth::user()->isClient())
        <a href="{{ route('admin.walkin.form') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium ml-6 {{ request()->routeIs('admin.walkin.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Walk-in
        </a>
        <a href="{{ route('admin.regist-confirmation') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium ml-6 {{ request()->routeIs('admin.regist-confirmation') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Regist Confirmation
        </a>
        @if (Auth::user()->isSuperAdmin())
        <a href="{{ route('admin.import-client-confirmations') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium ml-6 {{ request()->routeIs('admin.import-client-confirmations') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Import Client Confirmations
        </a>
        @endif
        <a href="{{ route('admin.onsite') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium ml-6 {{ request()->routeIs('admin.onsite*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Onsite Event
        </a>
        @endif
        @endif

        {{-- ═══ Event Content ═══ --}}
        @if (Auth::user()->hasPermission('workshops') || Auth::user()->hasPermission('workshop_registrants') || Auth::user()->hasPermission('tracks') || Auth::user()->hasPermission('agenda') || Auth::user()->hasPermission('speakers') || Auth::user()->hasPermission('time_slots') || Auth::user()->hasPermission('rooms'))
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Event Content</p>
        </div>
        @endif
        @if (Auth::user()->hasPermission('workshops'))
        <a href="{{ route('admin.workshops.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.workshops.*') && !request()->routeIs('admin.workshops.utm-links*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Workshops
        </a>
        @endif
        @if (Auth::user()->hasPermission('workshops') || Auth::user()->hasPermission('workshop_registrants'))
        <a href="{{ route('admin.workshops.utm-links') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.workshops.utm-links*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            Workshop UTM Links
        </a>
        @endif
        @if (Auth::user()->hasPermission('workshop_registrants'))
        <a href="{{ route('admin.workshop-registrants.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.workshop-registrants.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Workshop Registrants
        </a>
        @endif
        @if (Auth::user()->hasPermission('tracks'))
        <a href="{{ route('admin.tracks.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.tracks.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Tracks
        </a>
        @endif
        @if (Auth::user()->hasPermission('agenda'))
        <a href="{{ route('admin.agenda.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.agenda.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Agenda
        </a>
        <a href="{{ route('admin.agenda.scan-index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium ml-6 {{ request()->routeIs('admin.agenda.scan*') || request()->routeIs('admin.agenda.visitors*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            Scan QR
        </a>
        @endif
        @if (Auth::user()->hasPermission('speakers'))
        <a href="{{ route('admin.speakers.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.speakers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Speakers
        </a>
        @endif
        @if (Auth::user()->hasPermission('time_slots'))
        <a href="{{ route('admin.time-slots.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.time-slots.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Time Slots
        </a>
        @endif
        @if (Auth::user()->hasPermission('rooms'))
        <a href="{{ route('admin.rooms.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.rooms.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Rooms & Floors
        </a>
        @endif

        {{-- ═══ Engagement ═══ --}}
        @if (Auth::user()->hasPermission('booths') || Auth::user()->hasPermission('agenda'))
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Engagement</p>
        </div>
        @endif
        @if (Auth::user()->hasPermission('booths'))
        <a href="{{ route('admin.booths.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.booths.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Booths
        </a>
        @endif
        @if (Auth::user()->hasPermission('agenda'))
        <a href="{{ route('admin.feedback.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.feedback.*') || request()->routeIs('admin.agenda.feedback.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Session Feedback
        </a>
        <a href="{{ route('admin.feedback.templates') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium ml-6 {{ request()->routeIs('admin.feedback.templates*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Feedback Templates
        </a>
        @endif

        {{-- ═══ Communications ═══ --}}
        @if (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('email_templates') || Auth::user()->hasPermission('utm_sources') || Auth::user()->hasPermission('qr_codes'))
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Communications</p>
        </div>
        @endif
        @if (Auth::user()->isSuperAdmin())
        <a href="{{ route('admin.templates.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.templates.*') || request()->routeIs('admin.admin-emails.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Email Templates
        </a>
        <a href="{{ route('admin.mail-settings.edit') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.mail-settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Mail Settings
        </a>
        @endif
        @if (Auth::user()->hasPermission('email_templates'))
        <a href="{{ route('admin.email-logs.reminder-form') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.email-logs.reminder-form') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Send Reminder
        </a>
        <a href="{{ route('admin.email-logs.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.email-logs.*') && !request()->routeIs('admin.email-logs.reminder-form') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Email Logs
        </a>
        @endif
        @if (Auth::user()->hasPermission('utm_sources'))
        <a href="{{ route('admin.management.utm') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.utm') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
            UTM Sources
        </a>
        @endif
        @if (Auth::user()->hasPermission('qr_codes'))
        <a href="{{ route('admin.management.qr') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.qr') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            QR Codes
        </a>
        @endif

        {{-- ═══ System ═══ --}}
        @if (Auth::user()->hasPermission('checkin_log') || Auth::user()->hasPermission('admin_users') || Auth::user()->hasPermission('login_logs'))
        <div class="pt-4">
            <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">System</p>
        </div>
        @endif
        @if (Auth::user()->hasPermission('checkin_log'))
        <a href="{{ route('admin.management.checkin') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.checkin') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Check-in Log
        </a>
        @endif
        @if (Auth::user()->hasPermission('login_logs'))
        <a href="{{ route('admin.management.login-logs') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.login-logs') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Login Logs
        </a>
        @endif
        @if (Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.management.impersonate.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.impersonate.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
             Impersonate
        </a>
        <a href="{{ route('admin.management.backup.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.backup.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7M4 7c0 1.657 3.582 3 8 3s8-1.343 8-3M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3m0 5c0 1.657-3.582 3-8 3s-8-1.343-8-3"/></svg>
             Database Backup
        </a>
        @endif
        @if (Auth::user()->hasPermission('admin_users'))
        <a href="{{ route('admin.management.users') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.users') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
            Admin Users
        </a>
        <a href="{{ route('admin.management.users.invite') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.users.invite') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition ml-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Invite Client
        </a>
        <a href="{{ route('admin.management.groups.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.management.groups.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} transition ml-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Permission Groups
        </a>
        @endif

        {{-- ═══ Account ═══ --}}
        <div class="pt-4">
            <p class="px-3 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-widest">Account</p>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
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
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">
                    @if (Auth::user()->isClient())
                        Client
                    @elseif (Auth::user()->isSuperAdmin())
                        Super Admin
                    @else
                        Administrator
                    @endif
                </p>
            </div>
        </div>
    </div>
</aside>
