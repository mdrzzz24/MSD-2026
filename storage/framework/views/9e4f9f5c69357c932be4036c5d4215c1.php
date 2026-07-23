<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-T69856QT');</script>
    <!-- End Google Tag Manager -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Workshop Invitation — MSD 2026</title>
    <meta name="description" content="You're invited to a workshop at Metrodata Solution Day 2026." />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="Workshop Invitation — MSD 2026" />
    <meta property="og:description" content="You're invited to a workshop at Metrodata Solution Day 2026." />
    <meta property="og:image" content="<?php echo e(asset('img/header-sos.jpeg')); ?>" />
    <meta property="og:url" content="<?php echo e(url()->current()); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="<?php echo e(asset('img/Website-BG.jpeg')); ?>" fetchpriority="high">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>?v=7">
    <style>
        body { margin:0;padding:0;background:#050d2a;font-family:'Inter',system-ui,sans-serif;color:#e2e8f0;min-height:100vh;display:flex;flex-direction:column; }
        .invite-hero { padding:40px 20px 0;text-align:center; }
        .invite-hero img.kv { display:block;width:100%;max-width:640px;height:auto;margin:0 auto;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.3); }
        .invite-hero h1 { position:relative;z-index:1;font-size:clamp(20px,3.5vw,32px);font-weight:800;margin:0 0 6px;letter-spacing:-0.02em; }
        .invite-hero p { position:relative;z-index:1;font-size:14px;color:#94a3b8;margin:0; }
        .invite-hero .event-meta { position:relative;z-index:1;display:flex;justify-content:center;gap:20px;flex-wrap:wrap;margin-top:16px;font-size:13px;color:#94a3b8; }
        .invite-hero .event-meta span { display:inline-flex;align-items:center;gap:6px; }
        .invite-hero .event-meta svg { width:16px;height:16px;flex-shrink:0; }

        .invite-body { flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px 60px; }
        .invite-card { background:rgba(255,255,255,0.05);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.08);border-radius:24px;padding:40px 36px;max-width:640px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3); }
        .invite-card h2 { font-size:20px;font-weight:700;margin:0 0 4px; }


        .invite-form { margin-top:8px; }
        .invite-form label { display:block;font-size:14px;font-weight:600;margin-bottom:6px;color:#cbd5e1; }
        .invite-form input[type="email"] { width:100%;padding:14px 16px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:14px;font-size:15px;color:#e2e8f0;outline:none;transition:all 0.25s;box-sizing:border-box; }
        .invite-form input[type="email"]:focus { border-color:#f472b6;background:rgba(255,255,255,0.08);box-shadow:0 0 0 3px rgba(244,114,182,0.15); }
        .invite-form input[type="email"]::placeholder { color:#64748b; }
        .invite-form .btn-submit { display:block;width:100%;margin-top:14px;padding:14px 0;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;font-weight:700;font-size:15px;letter-spacing:0.02em;border:none;border-radius:999px;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:all 0.25s; }
        .invite-form .btn-submit:hover { transform:translateY(-2px);box-shadow:0 12px 30px rgba(233,30,99,0.5); }

        .alert { padding:14px 18px;border-radius:12px;font-size:14px;margin-bottom:20px;line-height:1.5; }
        .alert-error { background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.2);color:#fca5a5; }
        .alert-success { background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.2);color:#6ee7b7; }

        footer { text-align:center;padding:24px 20px;font-size:12px;color:#475569;border-top:1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T69856QT" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


<header class="invite-hero">
    <img src="<?php echo e(asset('img/QRHeader.png')); ?>" alt="MSD 2026" class="kv">
</header>


<div class="invite-body">
    <div class="invite-card">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo session('success'); ?></div>
        <?php elseif(session('error')): ?>
            <div class="alert alert-error"><?php echo e(session('error')); ?></div>
        <?php elseif(session('info')): ?>
            <div class="alert" style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.2);color:#93c5fd;"><?php echo e(session('info')); ?></div>
        <?php endif; ?>

        <h2 style="font-size:20px;font-weight:700;color:#e2e8f0;margin:0 0 4px;text-align:center;">You're invited to workshop<br><span style="color:#f472b6;"><?php echo e($workshop->name ?: $workshop->title); ?></span></h2>
        <?php if($workshop->name && $workshop->title): ?>
            <p style="text-align:center;font-size:14px;color:#94a3b8;margin:0 0 16px;"><?php echo e($workshop->title); ?></p>
        <?php endif; ?>
        <?php
            $agendaItem = $workshop->agendaItems->first();
            $trackAi = $track?->agendaItems->first();
            $wsAi = $agendaItem;

            // Priority for track-specific invitations: track's own time > track's agenda item > workshop's agenda item > workshop
            if ($track) {
                $start = $track->start_time ?? $trackAi?->start_time ?? $wsAi?->start_time ?? $workshop->start_time;
                $end   = $track->end_time ?? $trackAi?->end_time ?? $wsAi?->end_time ?? $workshop->end_time;
                $room  = $trackAi?->room ?? $wsAi?->room ?? $workshop->room ?? '—';
                $date  = $trackAi?->date ?? $wsAi?->date ?? $workshop->date;
            } else {
                $room  = $workshop->room ?? $wsAi?->room ?? '—';
                $date  = $workshop->date ?? $wsAi?->date;
                $start = $workshop->start_time ?? $wsAi?->start_time;
                $end   = $workshop->end_time ?? $wsAi?->end_time;
            }
            $timeRange = '—';
            if ($start && $end) {
                $timeRange = date('H:i', strtotime($start)) . ' – ' . date('H:i', strtotime($end));
            }
        ?>

        <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:#94a3b8;line-height:1.8;padding:16px 20px;background:rgba(255,255,255,0.04);border-radius:14px;border:1px solid rgba(255,255,255,0.06);margin:16px 0 24px;">
            <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> <?php echo e($date ? $date->format('l, d F Y') : '20 August 2026'); ?></div>
            <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <?php echo e($timeRange); ?></div>
            <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg> <?php echo e($room); ?> Room</div>
        </div>

        
        <?php
            // Use track-level speakers if available, fall back to agenda-item speakers
            $displaySpeakers = $speakers ?? $agendaItem?->speakers ?? collect();
        ?>
        <?php if($displaySpeakers->isNotEmpty()): ?>
            <div style="margin-bottom:24px;">
                <h4 style="font-size:12px;font-weight:700;color:#94a3b8;margin:0 0 14px;text-transform:uppercase;letter-spacing:1px;">
                    <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-width="2" d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path stroke-width="2" d="M19 10v2a7 7 0 0 1-14 0v-2"/><line stroke-width="2" x1="12" y1="19" x2="12" y2="23"/><line stroke-width="2" x1="8" y1="23" x2="16" y2="23"/></svg>
                    Speaker<?php echo e($speakers->count() > 1 ? 's' : ''); ?>

                </h4>
                <?php $__currentLoopData = $displaySpeakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid rgba(255,255,255,0.06);">
                        <?php if($sp->photo): ?>
                            <?php $photoUrl = str_starts_with($sp->photo, 'http') || str_starts_with($sp->photo, '/') ? $sp->photo : asset('storage/' . $sp->photo); ?>
                            <img src="<?php echo e($photoUrl); ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.1);flex-shrink:0;margin-top:2px;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div style="display:none;width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;margin-top:2px;"><?php echo e(strtoupper(substr($sp->name, 0, 1))); ?></div>
                        <?php else: ?>
                            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;margin-top:2px;"><?php echo e(strtoupper(substr($sp->name, 0, 1))); ?></div>
                        <?php endif; ?>
                        <div style="flex:1;min-width:0;">
                            <p style="font-weight:700;font-size:14px;color:#e2e8f0;margin:0 0 2px;"><?php echo e($sp->name); ?></p>
                            <p style="font-size:12px;color:#64748b;margin:0;"><?php echo e($sp->title ?? ''); ?><?php echo $sp->company ? ' <span style="color:#475569;">·</span> ' . e($sp->company) : ''; ?></p>
                            <?php if($sp->pivot && $sp->pivot->presentation_title): ?>
                                <p style="font-weight:600;font-size:13px;color:#f472b6;margin:6px 0 0;"><svg style="width:13px;height:13px;vertical-align:-2px;margin-right:5px;" fill="none" stroke="#f472b6" viewBox="0 0 24 24"><path stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline stroke-width="2" points="14 2 14 8 20 8"/><line stroke-width="2" x1="9" y1="13" x2="15" y2="13"/><line stroke-width="2" x1="9" y1="17" x2="13" y2="17"/></svg> <?php echo e($sp->pivot->presentation_title); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if($workshop->description): ?>
            <div class="ws-desc" style="font-size:13px;color:#cbd5e1;line-height:1.7;margin-bottom:24px;">
                <?php echo $workshop->description; ?>

            </div>
            <style>.ws-desc, .ws-desc * { color: #cbd5e1 !important; } .ws-desc ul, .ws-desc ol { padding-left: 20px; margin: 8px 0; } .ws-desc li { margin-bottom: 4px; } .ws-desc p { margin: 6px 0; } .ws-desc h4 { font-size: 14px; font-weight: 700; color: #e2e8f0 !important; margin: 12px 0 4px; } .ws-desc strong { color: #e2e8f0 !important; }</style>
        <?php endif; ?>

        <?php if($registrationStatus): ?>
            <div style="text-align:center;padding:16px 0;margin-top:8px;border-top:1px solid rgba(255,255,255,0.06);">
                <?php if($registrationStatus === 'approved'): ?>
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.2);">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#34d399" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2.5" d="M8 12l3 3 5-5"/></svg> You are registered
                    </div>
                <?php elseif($registrationStatus === 'rejected'): ?>
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.2);margin-bottom:14px;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M15 9l-6 6M9 9l6 6"/></svg> Registration Rejected
                    </div>
                    <?php if($invitation->isValid()): ?>
                        <form class="invite-form" method="POST" action="<?php echo e(route('workshop.invitation', $invitation->token)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="email" value="<?php echo e($email); ?>">
                            <button type="submit" class="btn-submit">Re-register</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(251,191,36,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.2);">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#fbbf24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M12 6v6l4 2"/></svg> Pending Approval
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif($invitation->isValid()): ?>
            <form class="invite-form" method="POST" action="<?php echo e(route('workshop.invitation', $invitation->token)); ?>">
                <?php echo csrf_field(); ?>
                <label for="email">Enter your email to register</label>
                <input type="email" name="email" id="email" value="<?php echo e(old('email', $email)); ?>" placeholder="yourname@company.com" required autocomplete="email">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p style="color:#ef4444;font-size:13px;margin-top:6px;"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <button type="submit" class="btn-submit">Register for Workshop</button>
            </form>
        <?php else: ?>
            <div style="text-align:center;padding:16px 0;margin-top:8px;border-top:1px solid rgba(255,255,255,0.06);">
                <p style="margin:0;font-size:14px;color:#94a3b8;">This invitation link has been used.</p>
                <p style="margin:4px 0 0;font-size:13px;color:#64748b;">If you have any questions, please contact the event organizer.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    &copy; 2026 <strong>Metrodata Solution Day</strong> — Jakarta, 20 August 2026 &middot; Shangri-La Hotel
</footer>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/workshop-invitation.blade.php ENDPATH**/ ?>