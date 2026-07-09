<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Registrant — <?php echo e(config('app.name')); ?></title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Dashboard
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Detail Registrant</h1>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                
                <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                            <?php echo e(strtoupper(substr($registrant->name, 0, 1))); ?>

                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900"><?php echo e($registrant->name); ?></h2>
                            <p class="text-xs text-gray-500">ID: #<?php echo e($registrant->id); ?></p>
                        </div>
                        <div class="ml-auto">
                            <?php if($registrant->status === 'approved'): ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                </span>
                            <?php elseif($registrant->status === 'rejected'): ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->email); ?></dd>
                            </div>
                            <?php if($registrant->plain_password): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Password</dt>
                                <dd class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                    <code class="bg-gray-200 px-2 py-0.5 rounded select-all text-xs" id="detailPwd"><?php echo e($registrant->plain_password); ?></code>
                                    <button onclick="copyDetailPwd()" class="text-gray-400 hover:text-gray-600 transition" title="Copy password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </dd>
                            </div>
                            <?php endif; ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->phone ?? '—'); ?></dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Job Title</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->job_title ?? '—'); ?></dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Company</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->company ?? '—'); ?></dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Industry</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->industry ?? '—'); ?></dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Employees</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->employees ?? '—'); ?></dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">GDPR Consent</dt>
                                <dd class="text-sm font-medium">
                                    <?php if($registrant->gdpr): ?>
                                        <span class="inline-flex items-center gap-1 text-emerald-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Consented
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered At</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->created_at->format('d M Y, H:i')); ?></dd>
                            </div>
                            <?php if($registrant->first_name || $registrant->last_name): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">First Name</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->first_name ?? '—'); ?></dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Name</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->last_name ?? '—'); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if($registrant->referral_code): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Referral Code</dt>
                                <dd class="text-sm font-medium text-gray-900 font-mono"><?php echo e($registrant->referral_code); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if($registrant->utm_source): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">UTM Source</dt>
                                <dd class="text-sm font-medium text-gray-900"><?php echo e($registrant->utm_source); ?></dd>
                            </div>
                            <?php endif; ?>

                        </div>

                        <?php if($registrant->notes): ?>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registrant Notes</dt>
                                <dd class="text-sm text-gray-700"><?php echo e($registrant->notes); ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if($registrant->admin_notes): ?>
                            <div class="bg-<?php echo e($registrant->status === 'approved' ? 'emerald' : 'red'); ?>-50 rounded-xl p-4 border border-<?php echo e($registrant->status === 'approved' ? 'emerald' : 'red'); ?>-200">
                                <dt class="text-xs font-semibold text-<?php echo e($registrant->status === 'approved' ? 'emerald' : 'red'); ?>-500 uppercase tracking-wider mb-1">Admin Notes</dt>
                                <dd class="text-sm text-gray-800"><?php echo e($registrant->admin_notes); ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if($registrant->processed_at): ?>
                            <p class="text-xs text-gray-400 text-right">
                                Processed: <?php echo e($registrant->processed_at->format('d M Y, H:i')); ?>

                                <?php if($registrant->status === 'approved' && $registrant->approver): ?>
                                    <br>by <strong><?php echo e($registrant->approver->name); ?></strong>
                                <?php elseif($registrant->status === 'rejected' && $registrant->rejecter): ?>
                                    <br>by <strong><?php echo e($registrant->rejecter->name); ?></strong>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    
                    <?php if (! (Auth::user()->isClient())): ?>
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('admin.registrants.edit', $registrant)); ?>"
                               class="px-4 py-2 text-sm font-medium rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">
                                ✏️ Edit
                            </a>
                            <form action="<?php echo e(route('admin.registrants.destroy', $registrant)); ?>" method="POST" class="inline"
                                  onsubmit="return confirm('Delete <?php echo e(addslashes($registrant->name)); ?> permanently?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium rounded-xl bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <?php if($registrant->isPending()): ?>
                                <form action="<?php echo e(route('admin.registrants.approve', $registrant)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            onclick="return confirm('Approve <?php echo e(addslashes($registrant->name)); ?>?')"
                                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm shadow-emerald-200 transition">
                                        ✓ Approve
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if($registrant->status === 'approved' && $registrant->plain_password): ?>
                                <form action="<?php echo e(route('admin.registrants.resend-credentials', $registrant)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            onclick="return confirm('Resend credentials to <?php echo e(addslashes($registrant->name)); ?>?')"
                                            class="px-4 py-2.5 text-sm font-semibold rounded-xl bg-blue-500 text-white hover:bg-blue-600 shadow-sm shadow-blue-200 transition">
                                        📧 Resend Credentials
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="xl:col-span-1 space-y-5">
                    
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">Quick Stats</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Unique Code</p>
                                <p class="text-sm font-bold text-gray-900 font-mono"><?php echo e($registrant->unique_code ?? '—'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered</p>
                                <p class="text-sm font-bold text-gray-900"><?php echo e($registrant->created_at->format('d M Y, H:i')); ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Processed</p>
                                <p class="text-sm font-bold text-gray-900"><?php echo e($registrant->processed_at?->format('d M Y, H:i') ?? '—'); ?></p>
                                <?php if($registrant->status === 'approved' && $registrant->approver): ?>
                                    <p class="text-xs text-gray-500 mt-0.5">by <?php echo e($registrant->approver->name); ?></p>
                                <?php elseif($registrant->status === 'rejected' && $registrant->rejecter): ?>
                                    <p class="text-xs text-gray-500 mt-0.5">by <?php echo e($registrant->rejecter->name); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if($registrant->referral_code): ?>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Referral Code</p>
                                <p class="text-sm font-bold text-gray-900 font-mono"><?php echo e($registrant->referral_code); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <?php if($workshops->count() > 0): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Workshops (<?php echo e($workshops->count()); ?>)
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('admin.workshops.registrants', $w)); ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <?php echo e($w->title); ?>

                                        <?php $pw = $w->pivot; ?>
                                        <?php if($pw): ?>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full <?php echo e($pw->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($pw->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700')); ?>"><?php echo e($pw->status); ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($agendaItems->count() > 0): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Sessions (<?php echo e($agendaItems->count()); ?>)
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $agendaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-gray-50 text-gray-700 rounded-lg border border-gray-200">
                                        <?php echo e($item->title); ?>

                                        <?php $as = $item->pivot->status ?? 'pending'; ?>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full <?php echo e($as === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($as === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700')); ?>"><?php echo e($as); ?></span>
                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($registrant->qr_token): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                QR Code
                            </h3>
                        </div>
                        <div class="p-5 text-center">
                            <img src="<?php echo e($registrant->qr_code_url); ?>" alt="QR Code" class="w-32 h-32 mx-auto rounded-lg border border-gray-200 mb-3">
                            <div class="flex items-center gap-2">
                                <input type="text" value="<?php echo e($registrant->qr_share_url); ?>" readonly
                                       class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1.5 rounded-lg w-full border-0 cursor-text"
                                       id="qrShareUrl">
                                <button onclick="copyQrUrl()"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition whitespace-nowrap flex-shrink-0">
                                    Copy
                                </button>
                            </div>
                            <a href="<?php echo e($registrant->qr_share_url); ?>" target="_blank"
                               class="inline-block text-xs text-indigo-600 hover:text-indigo-800 font-medium mt-2">
                                Preview QR →
                            </a>
                            <?php if($registrant->checked_in_at): ?>
                                <p class="text-xs text-emerald-600 font-semibold mt-2">
                                    ✓ Checked in at <?php echo e($registrant->checked_in_at->format('H:i, d M Y')); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
    </main>
</div>

<script>
    function copyDetailPwd() {
        const el = document.getElementById('detailPwd');
        if (!el) return;
        navigator.clipboard.writeText(el.textContent).then(() => {
            const orig = el.textContent;
            el.textContent = 'Copied!';
            setTimeout(() => el.textContent = orig, 1200);
        });
    }

    function copyQrUrl() {
        const el = document.getElementById('qrShareUrl');
        if (!el) return;
        navigator.clipboard.writeText(el.value).then(() => {
            const btn = event.target;
            const orig = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-emerald-500');
            btn.classList.remove('bg-indigo-500', 'hover:bg-indigo-600');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
            }, 1500);
        });
    }
</script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/registrant-detail.blade.php ENDPATH**/ ?>