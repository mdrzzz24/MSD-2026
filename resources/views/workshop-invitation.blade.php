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
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Workshop Invitation — MSD 2026</title>
    <meta name="description" content="You're invited to a workshop at Metrodata Solution Day 2026." />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="Workshop Invitation — MSD 2026" />
    <meta property="og:description" content="You're invited to a workshop at Metrodata Solution Day 2026." />
    <meta property="og:image" content="{{ asset('img/header-sos.jpeg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('img/Website-BG.jpg?v2') }}" fetchpriority="high">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=7">
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

{{-- Hero / Key Visual --}}
<header class="invite-hero">
    <img src="{{ asset('img/QRHeader.png') }}" alt="MSD 2026" class="kv">
</header>

{{-- Main Content --}}
<div class="invite-body">
    <div class="invite-card">
        @if (session('success'))
            <div class="alert alert-success">{!! session('success') !!}</div>
        @elseif (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @elseif (session('info'))
            <div class="alert" style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.2);color:#93c5fd;">{{ session('info') }}</div>
        @endif

        <h2 style="font-size:20px;font-weight:700;color:#e2e8f0;margin:0 0 4px;text-align:center;">You're invited to workshop<br><span style="color:#f472b6;">{{ $workshop->name ?: $workshop->title }}</span></h2>
        @if ($workshop->name && $workshop->title)
            <p style="text-align:center;font-size:14px;color:#94a3b8;margin:0 0 16px;">{{ $workshop->title }}</p>
        @endif
        @php
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
        @endphp

            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:#94a3b8;line-height:1.8;padding:16px 20px;background:rgba(255,255,255,0.04);border-radius:14px;border:1px solid rgba(255,255,255,0.06);margin:16px 0 24px;">
                <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> {{ $date ? $date->format('l, d F Y') : '20 August 2026' }}</div>
            <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> {{ $timeRange }}</div>
            <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg> {{ $room }} Room</div>
        </div>

        {{-- Speakers --}}
        @php
            // Use track-level speakers if available, fall back to agenda-item speakers
            $displaySpeakers = $speakers ?? $agendaItem?->speakers ?? collect();
        @endphp
        @if ($displaySpeakers->isNotEmpty())
            <div style="margin-bottom:24px;">
                <h4 style="font-size:12px;font-weight:700;color:#94a3b8;margin:0 0 14px;text-transform:uppercase;letter-spacing:1px;">
                    <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-width="2" d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path stroke-width="2" d="M19 10v2a7 7 0 0 1-14 0v-2"/><line stroke-width="2" x1="12" y1="19" x2="12" y2="23"/><line stroke-width="2" x1="8" y1="23" x2="16" y2="23"/></svg>
                    Speaker{{ $speakers->count() > 1 ? 's' : '' }}
                </h4>
                @foreach ($displaySpeakers as $sp)
                    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid rgba(255,255,255,0.06);">
                        @if ($sp->photo)
                            @php $photoUrl = str_starts_with($sp->photo, 'http') || str_starts_with($sp->photo, '/') ? $sp->photo : asset('storage/' . $sp->photo); @endphp
                            <img src="{{ $photoUrl }}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.1);flex-shrink:0;margin-top:2px;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div style="display:none;width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;margin-top:2px;">{{ strtoupper(substr($sp->name, 0, 1)) }}</div>
                        @else
                            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;margin-top:2px;">{{ strtoupper(substr($sp->name, 0, 1)) }}</div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <p style="font-weight:700;font-size:14px;color:#e2e8f0;margin:0 0 2px;">{{ $sp->name }}</p>
                            <p style="font-size:12px;color:#64748b;margin:0;">{{ $sp->title ?? '' }}{!! $sp->company ? ' <span style="color:#475569;">·</span> ' . e($sp->company) : '' !!}</p>
                            @if ($sp->pivot && $sp->pivot->presentation_title)
                                <p style="font-weight:600;font-size:13px;color:#f472b6;margin:6px 0 0;"><svg style="width:13px;height:13px;vertical-align:-2px;margin-right:5px;" fill="none" stroke="#f472b6" viewBox="0 0 24 24"><path stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline stroke-width="2" points="14 2 14 8 20 8"/><line stroke-width="2" x1="9" y1="13" x2="15" y2="13"/><line stroke-width="2" x1="9" y1="17" x2="13" y2="17"/></svg> {{ $sp->pivot->presentation_title }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($workshop->description)
            <div class="ws-desc" style="font-size:13px;color:#cbd5e1;line-height:1.7;margin-bottom:24px;">
                {!! $workshop->description !!}
            </div>
            <style>.ws-desc, .ws-desc * { color: #cbd5e1 !important; } .ws-desc ul, .ws-desc ol { padding-left: 20px; margin: 8px 0; } .ws-desc li { margin-bottom: 4px; } .ws-desc p { margin: 6px 0; } .ws-desc h4 { font-size: 14px; font-weight: 700; color: #e2e8f0 !important; margin: 12px 0 4px; } .ws-desc strong { color: #e2e8f0 !important; }</style>
        @endif

        @if ($registrationStatus)
            <div style="text-align:center;padding:16px 0;margin-top:8px;border-top:1px solid rgba(255,255,255,0.06);">
                @if ($registrationStatus === 'approved')
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.2);">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#34d399" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2.5" d="M8 12l3 3 5-5"/></svg> You are registered
                    </div>
                @elseif ($registrationStatus === 'rejected')
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.2);margin-bottom:14px;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M15 9l-6 6M9 9l6 6"/></svg> Registration Rejected
                    </div>
                    @if ($invitation->isValid())
                        <form class="invite-form" method="POST" action="{{ $invitation->invitation_url }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="utm_source" id="utm_source" value="">
                            <input type="hidden" name="utm_medium" id="utm_medium" value="">
                            <input type="hidden" name="utm_campaign" id="utm_campaign" value="">
                            <input type="hidden" name="utm_content" id="utm_content" value="">
                            <button type="submit" class="btn-submit">Re-register</button>
                        </form>
                    @endif
                @else
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(251,191,36,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.2);">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#fbbf24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M12 6v6l4 2"/></svg> Pending Approval
                    </div>
                @endif
            </div>
        @elseif ($invitation->isValid())
            <form class="invite-form" method="POST" action="{{ $invitation->invitation_url }}">
                @csrf
                <label for="email">Enter your email to register</label>
                <input type="email" name="email" id="email" value="{{ old('email', $email) }}" placeholder="yourname@company.com" required autocomplete="email">
                <input type="hidden" name="utm_source" id="utm_source" value="">
                <input type="hidden" name="utm_medium" id="utm_medium" value="">
                <input type="hidden" name="utm_campaign" id="utm_campaign" value="">
                <input type="hidden" name="utm_content" id="utm_content" value="">
                @error('email')<p style="color:#ef4444;font-size:13px;margin-top:6px;">{{ $message }}</p>@enderror
                <button type="submit" class="btn-submit">Register for Workshop</button>
            </form>

            @if ($needRegistration)
                <p class="section-eyebrow" style="margin-top:34px;text-align:center;">Register</p>
                <div class="reg-notice">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    <span>Your email is not registered for the event yet. Complete the event registration form below — after that, you will be automatically registered for <strong>{{ $workshop->name ?: $workshop->title }}</strong>.</span>
                </div>
                <div class="form-wrap" style="grid-template-columns:1fr;margin-top:22px;">
                    <form id="inviteRegForm" class="form-grid" method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="field"><label>First Name</label><input required name="firstName" placeholder="First Name" /></div>
                        <div class="field"><label>Last Name</label><input required name="lastName" placeholder="Last Name" /></div>
                        <div class="field">
                            <label>Job Function</label>
                            <select name="job_title" required>
                                <option value="">Select Job Function</option>
                                @foreach (['Intern','Staff','Supervisor','Manager','Senior Manager','General Manager','Head of Department','Chief','Director','President','Vice President'] as $jf)<option>{{ $jf }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Job Title</label>
                            <select id="regJobRoleSelector" name="job_role_selector" required>
                                <option value="">Select Job Title</option>
                                @foreach (['Student','Sales','Pre-Sales / Solution Architect','Engineering','Marketing','Management','Finance / Accounting','Information Technology','Operations','Human Resources','Legal / Compliance','Procurement','Research & Development','Customer Service / Support','Consulting','Business Development','Administration'] as $jt)<option>{{ $jt }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field" id="regJobRoleSpecificField" style="display:none;">
                            <label>Job Role</label>
                            <select id="regJobRoleSpecific">
                                <option value="">Select Job Role</option>
                                @foreach (['IT Infrastructure','Infrastructure Engineer','System Administrator','Network Administrator','Network Engineer','IT Operations','IT Support','Helpdesk','Desktop Support','NOC Engineer','Data Center Engineer','IT Security','Cyber Security Analyst','Security Engineer','Security Operations Center','DevSecOps Engineer','IT Governance, Risk & Compliance (GRC)','Software Engineer','Software Developer','Full Stack Developer','Front-End Developer','Back-End Developer','Mobile Developer','Web Developer','Application Developer','Technical Lead','Engineering Manager','Enterprise Architect','Solution Architect','Technical Architect','Cloud Architect','Application Architect','Data Analyst','Business Intelligence (BI) Analyst','Data Engineer','Data Scientist','AI Engineer','Machine Learning Engineer','Analytics','ERP','ERP Basis','ERP Functional','ERP Consultant','Business Application','Cloud Engineer','Cloud Administrator','DevOps Engineer','Site Reliability Engineer (SRE)','Platform Engineer','Engineer','Database Administrator (DBA)','Database Engineer','Database Architect','IT Project','IT Governance','IT Compliance','IT Audit','IT Risk','Digital Transformation','Digital Innovation','Digital Technology','IT Transformation','IT Business Analyst','System Analyst','IT Business Solution','IT Product Owner','IT Product','QA Engineer','Software','IT Engineer','IT Automation Engineer','IT Quality Assurance'] as $jr)<option>{{ $jr }}</option>@endforeach
                            </select>
                        </div>
                        <input type="hidden" name="job_role" id="regJobRoleFinal">
                        <div class="field"><label>Company Name</label><input required name="company" placeholder="Company Name" /></div>
                        <div class="field"><label>Business Email</label><input required type="email" name="email" placeholder="yourname@company.com" value="{{ $email }}" /></div>
                        <div class="field">
                            <label>Mobile Phone</label>
                            <div class="phone-wrapper" style="display:flex;align-items:stretch;gap:0;">
                                <span class="phone-prefix" id="regPhonePrefix" style="display:flex;align-items:center;padding:12px 10px;background:rgba(255,255,255,.08);border:1px solid var(--line,#d1d5db);border-right:none;border-radius:10px 0 0 10px;font-size:14px;color:rgba(255,255,255,.45);white-space:nowrap;flex-shrink:0;transition:border-color .25s,box-shadow .25s,background .25s;">+62</span>
                                <input required name="phone" placeholder="815-xxx-xxxx" class="phone-input" id="regPhoneInput" style="flex:1;border-radius:0 10px 10px 0;border-left:none;" oninput="updateRegPhonePrefix(this)">
                            </div>
                        </div>
                        <div class="field">
                            <label>Industry</label>
                            <select name="industry" required>
                                <option value="">Select Industry</option>
                                @foreach (['AGRICULTURE, FORESTRY','CHEMICALS','CONSTRUCTION, PROPERTY & REAL ESTATE','DISTRIBUTION','EDUCATION','FINANCIAL SERVICES','FISHING & MARINE','FOREIGN SERVICES','GOVERNMENT SERVICES','HEALTHCARE','HIGH TECHNOLOGY','HOSPITALITY / TOURISM','MANUFACTURING','MEDIA','MINING & METALS','OIL & GAS','PROFESSIONAL & BUSINESS SERVICES','RETAIL, WHOLESALE','TELECOMMUNICATIONS','TRANSPORTATION','UTILITIES / PUBLIC SERVICES'] as $ind)<option>{{ $ind }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>How did you hear about this event?</label>
                            <select name="referral_source" required>
                                <option value="">Select one</option>
                                @foreach (['LinkedIn','Instagram','Kompas Newspaper','Metrodata Website','Email','Metrodata Group Sales Representative / Colleague'] as $rs)<option>{{ $rs }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Number of Employee</label>
                            <select name="employees" required>
                                <option value="">Select Number of Employee</option>
                                @foreach (['1 – 50','51 – 200','201 – 500','501 – 1000','1000+'] as $emp)<option>{{ $emp }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field full gdpr-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="gdpr" required checked>
                                <span>By submitting this form, I understand Metrodata will process my personal information in accordance with their <strong><a href="https://www.metrodata.co.id/privacy-policy" target="_blank">Privacy Notice</a></strong>. Additionally, I consent to my information being shared with <strong><a href="https://jovenindo.com/privacy-policy" target="_blank">Event Partners</a></strong> in accordance. I understand I may withdraw my consent or update my information at any time.</span>
                            </label>
                        </div>
                        <input type="hidden" name="utm_source" id="reg_utm_source" value="">
                        <input type="hidden" name="utm_medium" id="reg_utm_medium" value="">
                        <input type="hidden" name="utm_campaign" id="reg_utm_campaign" value="">
                        <input type="hidden" name="utm_content" id="reg_utm_content" value="">
                        <div class="field full" style="margin-top:8px">
                            <button type="submit" class="btn btn-submit">Submit Registration <span class="btn-arrow">→</span></button>
                        </div>
                    </form>
                </div>
            @endif
        @else
            <div style="text-align:center;padding:16px 0;margin-top:8px;border-top:1px solid rgba(255,255,255,0.06);">
                <p style="margin:0;font-size:14px;color:#94a3b8;">This invitation link has been used.</p>
                <p style="margin:4px 0 0;font-size:13px;color:#64748b;">If you have any questions, please contact the event organizer.</p>
            </div>
        @endif
    </div>
</div>

<footer>
    &copy; 2026 <strong>Metrodata Solution Day</strong> — Jakarta, 20 August 2026 &middot; Shangri-La Hotel
</footer>

{{-- Registration Success Modal --}}
<div id="inviteSuccessModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(5,13,42,0.7); backdrop-filter:blur(8px); padding:16px;">
  <div style="background:rgba(255,255,255,0.06); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.1); border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08); width:100%; max-width:420px; overflow:hidden; animation:fadeInUp 0.35s ease-out;">
    <div style="padding:36px 32px 28px; text-align:center;">
      <div style="width:72px; height:72px; background:rgba(16,185,129,0.15); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 22px; border:1px solid rgba(16,185,129,0.2);">
        <svg style="width:34px; height:34px; color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <h2 style="font-size:19px; font-weight:700; color:#e2e8f0; margin-bottom:6px; letter-spacing:-0.01em;">Registration Successful!</h2>
      <p id="inviteNotifMessage" style="font-size:14px; color:#94a3b8; line-height:1.6; margin-bottom:16px;"></p>
      <div style="background:rgba(255,183,74,0.1); border:1px solid rgba(255,183,74,0.25); border-radius:12px; padding:14px 16px; text-align:left; margin-bottom:22px;">
        <p style="margin:0 0 4px; font-size:13px; font-weight:700; color:#fbbf24;"> Check your email</p>
        <p style="margin:0; font-size:12.5px; color:#94a3b8; line-height:1.5;">If approved, you will receive an email containing your password to log in.</p>
      </div>
      <a href="{{ url('/') }}" style="display:block; text-align:center; padding:12px 0; background:linear-gradient(135deg,#ff3d6e,#e91e63); color:#fff; font-weight:700; font-size:14px; letter-spacing:0.03em; border:none; border-radius:999px; text-decoration:none; box-shadow:0 8px 24px rgba(233,30,99,0.35); transition:transform 0.25s,box-shadow 0.25s;">Back to Home</a>
    </div>
  </div>
</div>

<script>
// ── UTM Capture from URL ──
(function() {
    const params = new URLSearchParams(window.location.search);
    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'].forEach(function(name) {
        const el = document.getElementById(name);
        if (el && params.has(name)) el.value = params.get(name);
    });
    // Inline event registration form UTM fields
    [['reg_utm_source','utm_source'],['reg_utm_medium','utm_medium'],['reg_utm_campaign','utm_campaign'],['reg_utm_content','utm_content']].forEach(function(pair) {
        const el = document.getElementById(pair[0]);
        if (el && params.has(pair[1])) el.value = params.get(pair[1]);
    });
})();

// ── Mobile phone prefix logic (inline event registration form) ──
function updateRegPhonePrefix(el) {
    var v = el.value.replace(/[^0-9]/g, '').replace(/^0/, '');
    el.value = v;
    var p = el.parentNode.querySelector('.phone-prefix');
    if (p) { if (v) { p.style.background = '#fff'; p.style.color = '#374151'; } else { p.style.background = 'rgba(255,255,255,.08)'; p.style.color = 'rgba(255,255,255,.45)'; } }
}

// ── Job Function / Job Title / Job Role logic (inline event registration form) ──
(function() {
    var sel = document.getElementById('regJobRoleSelector');
    var field = document.getElementById('regJobRoleSpecificField');
    var spec = document.getElementById('regJobRoleSpecific');
    var final = document.getElementById('regJobRoleFinal');
    if (!sel || !final) return;
    function update() {
        var title = sel.value || '';
        var isIT = title === 'Information Technology';
        if (field) field.style.display = isIT ? '' : 'none';
        if (spec) { spec.required = isIT; if (!isIT) spec.value = ''; }
        var specific = spec ? spec.value : '';
        final.value = specific ? (title + ' - ' + specific) : title;
    }
    sel.addEventListener('change', update);
    if (spec) spec.addEventListener('change', update);
    update();
})();

// ── Inline event registration: AJAX submit + success modal ──
document.getElementById('inviteRegForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    var origText = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = 'Submitting...';
    try {
        var resp = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: new FormData(this),
        });
        var data = await resp.json();
        if (resp.ok && data.success) {
            document.getElementById('inviteNotifMessage').innerHTML = data.message || 'We have received your data. Please wait for confirmation from the admin via email.';
            document.getElementById('inviteSuccessModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            this.reset();
        } else {
            var errs = data.errors || {};
            var keys = Object.keys(errs);
            alert(keys.length ? (errs[keys[0]][0] || 'Please complete the required information.') : 'Please complete the required information.');
        }
    } catch (err) {
        // Non-JSON response (server redirect/hang) — fall back to native submit
        this.submit();
    } finally {
        btn.disabled = false;
        btn.innerHTML = origText;
    }
});

function closeInviteSuccessModal() {
    document.getElementById('inviteSuccessModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'inviteSuccessModal') closeInviteSuccessModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeInviteSuccessModal();
});
</script>
</body>
</html>
