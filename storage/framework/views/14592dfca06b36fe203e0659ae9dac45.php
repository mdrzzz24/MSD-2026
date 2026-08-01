<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitations: <?php echo e($workshop->name ?: $workshop->title); ?> — <?php echo e(config('app.name')); ?></title>
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
        <a href="<?php echo e(route('admin.workshops.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshops
        </a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Invitations: <?php echo e($workshop->name ?: $workshop->title); ?></h1>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <h2 class="text-sm font-bold text-gray-800 mb-3">Generate Invitation Link</h2>
        <form action="<?php echo e(route('admin.workshops.invitations.generate', $workshop)); ?>" method="POST" class="flex flex-wrap items-end gap-3">
            <?php echo csrf_field(); ?>
            <?php $workshopTracks = $workshop->tracks()->where('is_active', true)->get(); ?>
            <?php if($workshopTracks->isNotEmpty()): ?>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Track <span class="text-gray-400">(optional)</span></label>
                    <select name="track_id" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                        <option value="">— All / No track —</option>
                        <?php $__currentLoopData = $workshopTracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="w-40">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Link Type</label>
                <select name="link_type" id="linkType" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="random">Random Code</option>
                    <option value="custom">Custom Slug</option>
                </select>
            </div>
            <div class="flex-1 min-w-[220px]" id="slugField" style="display: none;">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Custom Slug <span class="text-gray-400">(link: /invitation/workshop/{slug})</span></label>
                <input type="text" name="slug" id="slugInput" placeholder="nama-workshop"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                <p class="text-xs text-gray-400 mt-1">Preview: <code class="text-indigo-600">/invitation/workshop/<span id="slugPreview">nama-workshop</span></code></p>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Target Email <span class="text-gray-400">(optional)</span></label>
                <input type="email" name="email" placeholder="invitee@company.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Max Uses</label>
                <input type="number" name="max_uses" value="0" min="0" placeholder="0 = Unlimited" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                <p class="text-xs text-gray-400 mt-1">0 = tanpa batas</p>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm transition">Generate</button>
        </form>
        <script>
            (function() {
                var linkType = document.getElementById('linkType');
                var slugField = document.getElementById('slugField');
                var slugInput = document.getElementById('slugInput');
                var slugPreview = document.getElementById('slugPreview');

                function toggleSlugField() {
                    var isCustom = linkType.value === 'custom';
                    slugField.style.display = isCustom ? '' : 'none';
                }
                function updatePreview() {
                    var raw = slugInput.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                    slugPreview.textContent = raw || 'nama-workshop';
                }
                linkType.addEventListener('change', toggleSlugField);
                slugInput.addEventListener('input', updatePreview);
                toggleSlugField();
            })();
        </script>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Link</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Track</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Target Email</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Uses</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Created</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $invitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e($inv->invitation_url); ?>" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium truncate block max-w-[240px]">
                                        <?php echo e($inv->invitation_url); ?>

                                    </a>
                                    <button onclick="copyLink(this, '<?php echo e($inv->invitation_url); ?>')"
                                            class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 transition"
                                            title="Copy link">
                                        Copy
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if($inv->track): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700 border border-teal-200">
                                        <?php echo e($inv->track->name); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600"><?php echo e($inv->email ?? '—'); ?></td>
                            <td class="px-5 py-4 text-sm text-center text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    <span><?php echo e($inv->use_count); ?>/<?php echo e($inv->isUnlimited() ? '∞' : $inv->max_uses); ?></span>
                                    <button onclick="toggleEditLimit(<?php echo e($inv->id); ?>)" class="p-1 text-gray-400 hover:text-indigo-600 transition" title="Change limit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </span>
                                
                                <form id="edit-limit-form-<?php echo e($inv->id); ?>" action="<?php echo e(route('admin.workshops.invitations.update-max-uses', $inv)); ?>" method="POST" class="hidden mt-1">
                                    <?php echo csrf_field(); ?>
                                    <div class="flex items-center gap-1">
                                        <input type="number" name="max_uses" value="<?php echo e($inv->max_uses); ?>" min="0"
                                               class="w-16 px-2 py-1 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <button type="submit" class="px-2 py-1 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save</button>
                                        <button type="button" onclick="toggleEditLimit(<?php echo e($inv->id); ?>)" class="px-2 py-1 text-xs font-medium text-gray-500 hover:text-gray-700 transition">Cancel</button>
                                    </div>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if($inv->is_active && ($inv->isUnlimited() || $inv->use_count < $inv->max_uses)): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> <?php echo e($inv->is_active ? 'Used Up' : 'Inactive'); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-sm text-center text-gray-500"><?php echo e($inv->created_at->format('d M H:i')); ?></td>
                            <td class="px-5 py-4 text-center">
                                <form action="<?php echo e(route('admin.workshops.invitations.toggle', $inv)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-medium rounded-lg <?php echo e($inv->is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'); ?> transition">
                                        <?php echo e($inv->is_active ? 'Deactivate' : 'Activate'); ?>

                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <p class="text-gray-400 font-medium">No invitations yet</p>
                                <p class="text-xs text-gray-400 mt-1">Generate an invitation link above to get started.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Workshop UTM Links</h2>
                <p class="text-xs text-gray-500">UTM for this workshop's invitation link — separate from event registration UTM</p>
            </div>
            <button onclick="openWorkshopUtmModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New UTM Link</button>
        </div>
        <?php if($utmLinks->count()): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">UTM Parameters</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Full URL</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Regs</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $utmLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $regs = $link->workshopRegistrationsCount(); ?>
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4"><span class="text-sm font-semibold text-gray-900"><?php echo e($link->name); ?></span></td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span class="text-[10px] font-medium bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">source:<?php echo e($link->utm_source); ?></span>
                                <span class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">medium:<?php echo e($link->utm_medium); ?></span>
                                <span class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded">campaign:<?php echo e($link->utm_campaign); ?></span>
                                <?php if($link->utm_content): ?><span class="text-[10px] font-medium bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded">content:<?php echo e($link->utm_content); ?></span><?php endif; ?>
                            </div>
                        </td>
                        <td class="px-5 py-4 max-w-[200px]">
                            <div class="flex items-center gap-1">
                                <input type="text" id="ws-url-<?php echo e($link->id); ?>" value="<?php echo e($link->full_url); ?>" readonly onclick="this.select()" class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded flex-1 min-w-0 border-0 cursor-text">
                                <button onclick="copyUtmUrl(this, 'ws-url-<?php echo e($link->id); ?>')" class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 transition" title="Copy URL">Copy</button>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php if($regs > 0): ?>
                            <a href="<?php echo e(route('admin.registrants.index', ['utm_source' => $link->utm_source, 'utm_medium' => $link->utm_medium, 'utm_campaign' => $link->utm_campaign])); ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline"><?php echo e($regs); ?></a>
                            <?php else: ?>
                            <span class="text-sm text-gray-400">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="editWorkshopUtmLink(<?php echo e($link->id); ?>, '<?php echo e(addslashes($link->name)); ?>', '<?php echo e($link->utm_source); ?>', '<?php echo e($link->utm_medium); ?>', '<?php echo e($link->utm_campaign); ?>', '<?php echo e($link->utm_content ?? ''); ?>', '<?php echo e($link->workshop_invitation_id ?? ''); ?>', '<?php echo e($link->track_id ?? ''); ?>')" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="<?php echo e(route('admin.workshops.utm-links.destroy', $link)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete <?php echo e(addslashes($link->name)); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="px-6 py-8 text-center">
            <p class="text-gray-400 font-medium">No workshop UTM links yet</p>
            <p class="text-xs text-gray-400 mt-1">Create a UTM for this workshop's invitation link (newsletter, sales, meta, ...)</p>
        </div>
        <?php endif; ?>
    </div>

    
    <div id="wsUtmModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeWsUtmModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900" id="wsUtmModalTitle">Create Workshop UTM Link</h3></div>
                <form id="wsUtmForm" method="POST" action="<?php echo e(route('admin.workshops.utm-links.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="_method" id="wsUtmFormMethod" value="POST">
                    <input type="hidden" name="link_id" id="wsUtmLinkId">
                    <input type="hidden" name="workshop_id" value="<?php echo e($workshop->id); ?>">
                    <div class="p-6 space-y-3">
                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">Link Name <span class="text-red-500">*</span></label>
                            <input type="text" id="wsUtmName" name="name" required placeholder="e.g. Workshop AWS - Newsletter" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                        <div id="wsUtmTrackGroup" style="display:none"><label class="block text-sm font-semibold text-gray-700 mb-1">Track <span class="text-gray-400 font-normal">(optional)</span></label>
                            <select id="wsUtmTrack" onchange="wsPopulateInvitationOptions()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="">— All tracks —</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Filter the invitation list by track.</p></div>
                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">Invitation / Custom Slug <span class="text-gray-400 font-normal">(optional)</span></label>
                            <select id="wsUtmInvitation" name="workshop_invitation_id" onchange="wsUpdateUtmUrlPreview()" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="">— Auto (invitation default) —</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Pick a specific custom slug / invitation if the workshop has more than one.</p></div>
                        <input type="hidden" name="track_id" id="wsUtmTrackId">
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Source <span class="text-red-500">*</span></label>
                                <input type="text" id="wsUtmSource" name="utm_source" required placeholder="newsletter" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Medium <span class="text-red-500">*</span></label>
                                <input type="text" id="wsUtmMedium" name="utm_medium" required placeholder="email" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Campaign <span class="text-red-500">*</span></label>
                                <input type="text" id="wsUtmCampaign" name="utm_campaign" required placeholder="msd2026" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                        </div>
                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">Content (optional)</label>
                            <input type="text" id="wsUtmContent" name="utm_content" placeholder="e.g. newsletter-banner-a" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                        <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                            <p class="text-xs text-gray-500 mb-1">URL Preview</p>
                            <p class="text-[11px] text-indigo-600 break-all font-mono" id="wsUtmUrlPreview"><?php echo e(rtrim(\App\Models\UtmLink::BASE_URL, '/')); ?>/invitation/workshop/{slug}?utm_source=...</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <button type="button" onclick="closeWsUtmModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Save Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('admin.workshops.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Workshops</a>
    </div>
</div>
</main>
</div>

<script>
function copyLink(btn, url) {
    navigator.clipboard.writeText(url).then(function() {
        var orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-indigo-100', 'hover:text-indigo-700');
        btn.classList.add('bg-emerald-100', 'text-emerald-700');
        setTimeout(function() {
            btn.textContent = orig;
            btn.classList.remove('bg-emerald-100', 'text-emerald-700');
            btn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-indigo-100', 'hover:text-indigo-700');
        }, 2000);
    }).catch(function() {
        alert('Failed to copy link');
    });
}

function toggleEditLimit(id) {
    var form = document.getElementById('edit-limit-form-' + id);
    form.classList.toggle('hidden');
}

// ── Workshop UTM Links ──
var wsUtmUpdateUrl = '<?php echo e(route("admin.workshops.utm-links.update", ["utmLink" => "LINK_ID"])); ?>';
var wsInvitations = <?php echo json_encode($wsInvitationData, 15, 512) ?>;
var wsTracks = <?php echo json_encode($wsTrackData, 15, 512) ?>;
var wsBaseUrl = '<?php echo e(rtrim(\App\Models\UtmLink::BASE_URL, '/')); ?>';

function wsPopulateTrackOptions() {
    var trackSel = document.getElementById('wsUtmTrack');
    var opts = '<option value="">— All tracks —</option>';
    wsTracks.forEach(function(t){ opts += '<option value="'+t.id+'">'+t.name+'</option>'; });
    trackSel.innerHTML = opts;
    document.getElementById('wsUtmTrackGroup').style.display = wsTracks.length ? '' : 'none';
    wsPopulateInvitationOptions();
}

function wsPopulateInvitationOptions() {
    var trackId = document.getElementById('wsUtmTrack').value;
    var invs = wsInvitations.filter(function(i){ return !trackId || String(i.track_id) === String(trackId); })
        .sort(function(a,b){
            // Prefer custom-slug invitations (nicer URL) when auto-selecting a track
            var aSlug = a.slug ? 0 : 1, bSlug = b.slug ? 0 : 1;
            if (aSlug !== bSlug) return aSlug - bSlug;
            return a.id - b.id;
        });
    var sel = document.getElementById('wsUtmInvitation');
    var opts = '<option value="">— Auto (invitation default) —</option>';
    invs.forEach(function(i){
        var label = (i.slug ? i.slug : '(random) ' + i.token.slice(0,8));
        if (i.track_name) label += ' · ' + i.track_name;
        opts += '<option value="'+i.id+'">'+label+'</option>';
    });
    sel.innerHTML = opts;
    // Store the chosen track on the link — the invitation page resolves the session from the UTM link
    if (document.getElementById('wsUtmTrackId')) document.getElementById('wsUtmTrackId').value = trackId || '';
    wsUpdateUtmUrlPreview();
}

function wsUpdateUtmUrlPreview() {
    var invSel = document.getElementById('wsUtmInvitation');
    var inv = invSel.selectedOptions[0];
    var base;
    if (inv && inv.value) {
        var data = wsInvitations.find(function(i){ return String(i.id) === String(inv.value); });
        base = data ? (wsBaseUrl + '/invitation/workshop/' + (data.slug || data.token)) : (wsBaseUrl + '/invitation/workshop/{slug}');
    } else {
        // Prefer the workshop's custom-slug (workshop-level) invitation so the shared link stays short/custom
        var master = wsInvitations.find(function(i){ return !i.track_id && i.slug; }) || wsInvitations.find(function(i){ return i.slug; }) || wsInvitations[0];
        base = master ? (wsBaseUrl + '/invitation/workshop/' + (master.slug || master.token)) : (wsBaseUrl + '/invitation/workshop/{slug}');
    }
    var source = document.getElementById('wsUtmSource').value || '...';
    document.getElementById('wsUtmUrlPreview').textContent = base + '?utm_source=' + source;
}

function openWorkshopUtmModal() {
    document.getElementById('wsUtmModalTitle').textContent = 'Create Workshop UTM Link';
    document.getElementById('wsUtmForm').action = '<?php echo e(route("admin.workshops.utm-links.store")); ?>';
    document.getElementById('wsUtmFormMethod').value = 'POST';
    ['wsUtmLinkId','wsUtmName','wsUtmSource','wsUtmMedium','wsUtmCampaign','wsUtmContent','wsUtmTrackId'].forEach(id => document.getElementById(id).value = '');
    wsPopulateTrackOptions();
    document.getElementById('wsUtmInvitation').value = '';
    wsUpdateUtmUrlPreview();
    document.getElementById('wsUtmModal').classList.remove('hidden');
}

function editWorkshopUtmLink(id, name, source, medium, campaign, content, invitationId, trackId) {
    document.getElementById('wsUtmModalTitle').textContent = 'Edit Workshop UTM Link';
    document.getElementById('wsUtmForm').action = wsUtmUpdateUrl.replace('LINK_ID', id);
    document.getElementById('wsUtmFormMethod').value = 'PUT';
    document.getElementById('wsUtmLinkId').value = id;
    document.getElementById('wsUtmName').value = name;
    document.getElementById('wsUtmSource').value = source;
    document.getElementById('wsUtmMedium').value = medium;
    document.getElementById('wsUtmCampaign').value = campaign;
    document.getElementById('wsUtmContent').value = content;
    var invData = invitationId ? wsInvitations.find(function(i){ return String(i.id) === String(invitationId); }) : null;
    wsPopulateTrackOptions();
    document.getElementById('wsUtmTrack').value = invData ? invData.track_id : (trackId || '');
    wsPopulateInvitationOptions();
    document.getElementById('wsUtmInvitation').value = invitationId || '';
    document.getElementById('wsUtmTrackId').value = trackId || '';
    wsUpdateUtmUrlPreview();
    document.getElementById('wsUtmModal').classList.remove('hidden');
}

function closeWsUtmModal() {
    document.getElementById('wsUtmModal').classList.add('hidden');
}

function copyUtmUrl(btn, inputId) {
    const input = document.getElementById(inputId);
    navigator.clipboard.writeText(input.value).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.add('bg-emerald-100', 'text-emerald-700');
        setTimeout(() => { btn.textContent = orig; btn.classList.remove('bg-emerald-100', 'text-emerald-700'); }, 1500);
    });
}

document.getElementById('wsUtmSource')?.addEventListener('input', wsUpdateUtmUrlPreview);

// Close workshop UTM modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeWsUtmModal();
});
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/workshops/invitations.blade.php ENDPATH**/ ?>