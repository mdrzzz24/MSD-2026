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
<title>Metrodata Solution Day 2026 — Winning with AI</title>
<meta name="description" content="MSD 2026: Winning with AI — Build, Run, and Scale for Measurable Impact. Jakarta, 20 August 2026, Shangri-La Hotel." />

{{-- Open Graph / Social Media Meta Tags --}}
<meta property="og:type" content="website" />
<meta property="og:title" content="Metrodata Solution Day 2026 — Winning with AI" />
<meta property="og:description" content="MSD 2026: Winning with AI — Build, Run, and Scale for Measurable Impact. Jakarta, 20 August 2026, Shangri-La Hotel." />
<meta property="og:image" content="{{ asset('img/header-sos.jpeg') }}" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:site_name" content="Metrodata Solution Day 2026" />
<meta property="og:locale" content="id_ID" />

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Metrodata Solution Day 2026 — Winning with AI" />
<meta name="twitter:description" content="MSD 2026: Winning with AI — Build, Run, and Scale for Measurable Impact. Jakarta, 20 August 2026, Shangri-La Hotel." />
<meta name="twitter:image" content="{{ asset('img/header-sos.jpeg') }}" />

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="preload" as="image" href="{{ asset('img/Website-BG.jpg?v2') }}" fetchpriority="high">
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v=7">
</head>
<body>@include('partials.impersonation-banner')<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T69856QT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- NAV -->
<nav class="nav">
  <div class="container nav-inner">
    <a href="#" class="logo">
      <img src="{{ asset('img/logo-metrodata.png') }}" alt="Metrodata" style="height:48px;width:auto">
    </a>
    <button class="nav-toggle" aria-label="Menu" id="navToggle">☰</button>
    <div class="nav-links" id="navLinks">
      <a href="#overview" class="active">Overview</a>
      @if ($agendaSectionsVisible)
        <a href="#agenda-sections">Agenda</a>
      @endif
      <a href="#sponsors">Sponsors</a>
      @if (Auth::guard('registrant')->check())
        <a href="{{ route('registrant.dashboard') }}">Dashboard</a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" style="color:#ef4444;">Logout</a>
        <form id="logoutForm" action="{{ route('registrant.logout') }}" method="POST" style="display:none;">@csrf</form>
      @else
        <a href="#register">Register</a>
        <a href="{{ route('login') }}" class="btn" style="padding:6px 18px;font-size:13px;">Login</a>
      @endif
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="hero" id="top">
  <div class="hero-light hero-light--blue"></div>
  <!-- <div class="hero-light hero-light--pink"></div> -->
  <div class="container hero-content">
    <p class="eyebrow"><strong>Metrodata</strong> Proudly Present</p>
    <div class="hero-title-group">
      <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" class="logo-glow" style="height:clamp(60px,10vw,100px);width:auto">
    </div>
    <h1>Winning with AI:<br>Build, Run, and Scale <span class="br-mobile"></span>for Measurable Impact</h1>
    <p class="tagline">Accelerating AI for Real Business and Operational Value</p>
    <div class="hero-meta">
      <span>
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        Jakarta, 20 August 2026
      </span>
      <span>
        <svg viewBox="0 0 24 24"><path d="M12 22s8-7.58 8-13a8 8 0 10-16 0c0 5.42 8 13 8 13z"/><circle cx="12" cy="9" r="3"/></svg>
        Shangri-La Hotel
      </span>
    </div>
    @unless (Auth::guard('registrant')->check())
    <a href="#register" class="btn">Register Now</a>
    @else
    <a href="{{ route('registrant.dashboard') }}" class="btn">My Dashboard</a>
    @endunless
  </div>
</header>
<div id="postHeroContent">
<!-- OVERVIEW -->
<section id="overview" class="reveal">
  <div class="container">
    <!-- <p class="section-eyebrow">Topic</p> -->
    <div class="two-col">
      <div class="reveal">
        <h2 class="section-title">Winning with AI:<br>Build, Run, and Scale for Measurable Impact</h2>
        <p style="color:var(--pink);font-weight:600;font-style:italic">Accelerating AI for Real Business and Operational Value</p>
      </div>
      <div class="reveal">
        <p class="section-lead">
          AI has entered a new era. In 2026, the conversation is no longer about what AI can do,
          but about how organizations can scale AI to create lasting business value. The challenge
          has shifted from isolated pilots to enterprise-wide adoption, where success depends on
          trusted data, secure and governed platforms, and an integrated technology ecosystem.
        </p>
      </div>
    </div>

    <p class="section-eyebrow" style="margin-top:80px">What to Expect</p>
    <h3 class="section-title" style="font-size:28px">A melting pot of innovators and game changers</h3>

    <div class="stats-grid">
      <div class="stat"><div class="num">10</div><div class="lbl">Visionary Keynotes</div><div class="desc">from global technology leaders, industry experts, and national stakeholders.</div></div>
      <div class="stat"><div class="num">9</div><div class="lbl">Interactive Workshops</div><div class="desc">with hands-on learning from technology experts.</div></div>
      <div class="stat"><div class="num">32</div><div class="lbl">Tech Sessions</div><div class="desc">featuring real-world case studies and best practices.</div></div>
      <div class="stat"><div class="num">32</div><div class="lbl">Innovation Exhibits</div><div class="desc">showcasing the latest enterprise technologies.</div></div>
      <div class="stat"><div class="num">1,200+</div><div class="lbl">Professionals</div></div>
    </div>
  </div>
</section>

<!-- WHY ATTEND -->
<!-- WHY ATTEND -->
<section class="why reveal">
  <div class="container">
    <p class="section-eyebrow">Why Should You Attend</p>
    <h2 class="section-title">Insights, connections, and solutions for your transformation journey</h2>
    <p class="section-lead">
      Whether you are defining business strategy, accelerating AI adoption, or modernizing enterprise
      technology, MSD 2026 offers valuable insights, meaningful connections, and practical solutions.
    </p>
    <div class="why-list">
      <div class="why-item"><div class="dot">1</div><div><h5>C-Level Executives & Business Strategists</h5></div></div>
      <div class="why-item"><div class="dot">2</div><div><h5>CIOs, CTOs & Digital Transformation Leaders</h5></div></div>
      <div class="why-item"><div class="dot">3</div><div><h5>Business Executives & Decision Makers</h5></div></div>
      <div class="why-item"><div class="dot">4</div><div><h5>IT Professionals, Architects & Developers</h5></div></div>
      <div class="why-item"><div class="dot">5</div><div><h5>Government Officials, Regulators & Academia</h5></div></div>
      <div class="why-item"><div class="dot">6</div><div><h5>SMEs, Startups & Innovation Leaders</h5></div></div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section class="reveal">
  <div class="container">
    <p class="section-eyebrow">About</p>
    <h2 class="section-title">Metrodata Solution Day</h2>
    <p class="section-lead" style="font-size:18px">
      Metrodata Solution Day (MSD) is Metrodata Group's flagship thought leadership platform where business and technology
      leaders come together to shape Indonesia's digital future.
    </p>
    <p style="color:var(--muted);margin-top:16px;max-width:760px">
      Celebrating its <strong style="color:var(--pink)">21st edition</strong>, MSD explores AI, digital
      transformation, and emerging technologies through visionary keynotes, executive discussions,
      innovation showcases, and expert-led sessions — empowering organizations to transform innovation
      into measurable business outcomes.
    </p>
  </div>
</section>

{{-- AGENDA SCHEDULE (template grid: time+location | topic | speakers | Bookmark) --}}
<style>
  .agenda-sched-header{display:flex;align-items:baseline;gap:24px;padding-bottom:20px;border-bottom:1px solid var(--line);margin-bottom:30px;flex-wrap:wrap}
  .agenda-sched-label{font-size:.9rem;letter-spacing:2px;text-transform:uppercase;color:var(--pink);font-weight:700}
  .agenda-sched-tabs{display:flex;gap:28px;align-items:baseline;flex-wrap:wrap}
  .agenda-sched-tab{font-size:1.3rem;font-weight:700;color:var(--muted);cursor:pointer;background:none;border:none;font-family:inherit;transition:color .2s,font-size .2s;padding:0}
  .agenda-sched-tab.active{color:#fff;font-size:1.6rem}
  .agenda-sched-tab:hover{color:var(--pink)}
  .agenda-sched-content{display:none}
  .agenda-sched-content.active{display:block}
  .agenda-empty{color:var(--muted);text-align:center;padding:40px 0;font-style:italic}
  .agenda-row{display:grid;grid-template-columns:140px 1.2fr 1.8fr 120px;gap:24px;padding:24px 0;border-bottom:1px solid var(--line);align-items:start}
  .agenda-row:last-child{border-bottom:none}
  .agenda-col-time{display:flex;flex-direction:column;gap:4px;font-size:.85rem}
  .agenda-col-time .atime{color:var(--ink);font-weight:700}
  .agenda-col-time .atz{color:var(--muted);margin-bottom:8px}
  .agenda-col-time .aroom{display:flex;flex-direction:column;gap:2px;margin-top:2px}
  .agenda-col-time .aroom-label{font-size:.65rem;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)}
  .agenda-col-time .aroom-value{font-weight:700;font-size:1rem;text-transform:uppercase;color:#fff}
  .agenda-col-topic{font-size:.95rem;font-weight:700;text-transform:none;letter-spacing:.5px;color:#fff;padding-right:15px}
  .agenda-col-topic .agenda-topic-title{display:block}
  .agenda-col-topic .agenda-topic-headline{display:block;margin-bottom:4px;font-size:1.15rem;font-weight:700;text-transform:none;letter-spacing:0;color:#f472b6;line-height:1.4}
  .agenda-col-topic .agenda-topic-desc{display:block;margin-top:6px;font-size:.8rem;font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);line-height:1.6}
  .agenda-col-topic .agenda-topic-highlights{display:flex;flex-direction:column;gap:3px;margin-top:8px}
  .agenda-col-topic .agenda-hl-item{font-size:.78rem;font-weight:400;text-transform:none;letter-spacing:0;color:#a5b4fc;line-height:1.5}
  .agenda-col-speakers{display:flex;flex-direction:column;gap:14px;font-size:.85rem}
  .agenda-speaker-card{display:flex;align-items:flex-start;gap:10px}
  .agenda-sp-photo{width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.1);flex-shrink:0}
  .agenda-sp-initial{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;flex-shrink:0;background:linear-gradient(135deg,#ff3d6e,#e91e63)}
  .agenda-sp-info{display:flex;flex-direction:column;gap:2px;min-width:0}
  .agenda-speaker-name{font-weight:700;color:var(--ink)}
  .agenda-speaker-role{font-style:italic;color:var(--muted);font-weight:400}
  .agenda-col-action{display:flex;flex-direction:column;align-items:flex-end;gap:10px;text-align:right}
  .agenda-silent-badge{display:inline-flex;align-items:center;gap:6px;font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--pink);border:1px solid rgba(244,114,182,.45);background:rgba(244,114,182,.12);border-radius:999px;padding:5px 12px;white-space:nowrap}
  .agenda-silent-badge svg{width:13px;height:13px;flex-shrink:0}
  .agenda-break-banner{display:flex;justify-content:center;align-items:center;gap:12px;padding:16px 20px;margin:10px 0;border-radius:12px;background:linear-gradient(135deg,rgba(255,61,110,.16),rgba(233,30,99,.07));border:1px solid rgba(244,114,182,.35)}
  .agenda-break-banner .b-text{display:flex;flex-direction:column;gap:2px}
  .agenda-break-banner .b-label{font-size:.9rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#f472b6;line-height:1.3}
  .agenda-track-sep{height:3px;border:none;background:linear-gradient(90deg,transparent,rgba(244,114,182,.8) 20%,rgba(244,114,182,.8) 80%,transparent);margin:22px 0;border-radius:999px}
  .agenda-row-last{border-bottom:none}
  .agenda-col-action .btn-register{display:inline-flex;align-items:center;gap:5px;padding:7px 16px;border-radius:999px;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;font-size:.8rem;font-weight:700;border:none;cursor:pointer;transition:transform .2s,box-shadow .2s}
  .agenda-col-action .btn-register:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(233,30,99,.4)}
  .agenda-col-action .btn-register svg{width:13px;height:13px}
  .agenda-row{transition:background .2s,border-radius .2s;cursor:pointer}
  .agenda-row:hover{background:rgba(255,255,255,.04);border-radius:12px}
  @media(max-width:900px){
    .agenda-sched-header{flex-direction:column;gap:12px}
    .agenda-row{grid-template-columns:1fr;gap:16px}
    .agenda-col-action{align-items:flex-start;text-align:left;margin-top:8px}
    .agenda-sched-tab.active{font-size:1.3rem}
    .agenda-sched-tab{font-size:1.1rem}
  }
</style>
@if ($agendaSectionsVisible)
<section id="agenda-sections" class="why reveal">
  <div class="container">

    @php
        // Dedupe by (start_time, normalized title): keep the entry with the most speakers, then the longest duration.
        $msdPickBest = function ($items) {
            $best = [];
            foreach ($items as $it) {
                $normTitle = html_entity_decode(strip_tags($it->title), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $key = substr($it->start_time, 0, 5) . '|' . mb_strtolower(trim($normTitle));
                if (!isset($best[$key])) { $best[$key] = $it; continue; }
                $cur = $best[$key];
                $curReg = (bool) $cur->is_registrable;
                $newReg = (bool) $it->is_registrable;
                $curSc = $cur->speakers->count();
                $newSc = $it->speakers->count();
                $curLen = strtotime($cur->end_time) - strtotime($cur->start_time);
                $newLen = strtotime($it->end_time) - strtotime($it->start_time);
                if ($newReg && !$curReg) { $best[$key] = $it; continue; }
                if (!$newReg && $curReg) continue;
                if ($newSc > $curSc || ($newSc === $curSc && $newLen > $curLen)) $best[$key] = $it;
            }
            return array_values($best);
        };

        // Classify an agenda item into a session type: general | workshop | session | track (null = skip breaks).
        $msdClassify = function ($it) {
            if ($it->category === 'break') return null;
            // Hide the generic "General Sessions" banner row (the placeholder that just
            // repeats the time + room). The individual keynote sessions still show.
            if (mb_strtolower(trim((string) $it->title)) === 'general sessions') return null;
            // Hide legacy placeholder track rows ("Track A1".."Track E7") from the site.
            // They stay in the DB; we just don't surface them on the agenda.
            $trkLabel = $it->track ? trim((string)($it->track->name ?: $it->track->title)) : '';
            if ($trkLabel !== '' && preg_match('/^Track [A-E][0-9]+$/i', $trkLabel)) return null;
            if (trim((string) $it->title) !== '' && preg_match('/^Track [A-E][0-9]+$/i', trim((string) $it->title))) return null;
            $t = $it->agenda_type
                ?: ($it->category === 'workshop' ? 'workshop' : null)
                ?: ($it->track_id ? 'track' : null)
                ?: ($it->workshop_id ? 'workshop' : null)
                ?: 'session';
            if ($t === 'keynote') return 'general';
            if (in_array($it->category, ['platinum', 'gold'], true)) return 'track';
            if ($it->category === 'general') return 'general';
            return $t;
        };

        // Display helper: speaker name in "Camel Style" (each word capitalized).
        $msdTitle = fn ($name) => \Illuminate\Support\Str::title(trim((string) $name));

        $sessionDefs = [
            'general'  => ['label' => 'General Session',   'icon' => '🎤'],
            'workshop' => ['label' => 'Workshop Session',  'icon' => '🛠️'],
            'track'    => ['label' => 'Track Session',     'icon' => '🛤️'],
        ];
        $panels = [];
        foreach ($sessionDefs as $key => $def) {
            $items = $msdPickBest($agendaItems->filter(fn ($i) => $msdClassify($i) === $key));
            if ($key === 'track' || $key === 'workshop') {
                // Order sessions by time slot, then by room order:
                //   track    → Ballroom A, Ballroom B, Ballroom C, Kalimantan, Maluku
                //   workshop → Sumatra, Java, Sulawesi
                $roomOrder = $key === 'track'
                    ? ['Ballroom A', 'Ballroom B', 'Ballroom C', 'Kalimantan', 'Maluku']
                    : ['Sumatra', 'Java', 'Sulawesi'];
                $roomIdx = array_flip($roomOrder);
                usort($items, function ($a, $b) use ($roomIdx) {
                    $timeCmp = strcmp((string) $a->start_time, (string) $b->start_time);
                    if ($timeCmp !== 0) return $timeCmp;
                    $ra = $roomIdx[trim((string) $a->room)] ?? PHP_INT_MAX;
                    $rb = $roomIdx[trim((string) $b->room)] ?? PHP_INT_MAX;
                    if ($ra !== $rb) return $ra <=> $rb;
                    return strcmp(trim((string) $a->room), trim((string) $b->room));
                });
                // Mark the last session of each time slot so its thin bottom
                // border can be merged into the thick group separator line (track only).
                if ($key === 'track') {
                    foreach ($items as $idx => $it) {
                        $next = $items[$idx + 1] ?? null;
                        $it->track_is_last_in_slot = $next ? ((string) $it->start_time !== (string) $next->start_time) : true;
                    }
                }
            }
            $panels[] = [
                'key'   => $key,
                'label' => $def['label'],
                'icon'  => $def['icon'],
                'items' => $items,
            ];
        }
    @endphp
 <h2 class="section-title">A full day of learning, exchange, and discovery</h2>
    <div class="agenda-sched-header">
      <span class="agenda-sched-label">Schedule</span>
      <nav class="agenda-sched-tabs">
        @foreach ($panels as $panel)
          <button type="button" class="agenda-sched-tab{{ $loop->first ? ' active' : '' }}" data-sess="{{ $panel['key'] }}">
            {{ $panel['label'] }}
          </button>
        @endforeach
      </nav>
    </div>

    @foreach ($panels as $panel)
      <div class="agenda-sched-content{{ $loop->first ? ' active' : '' }}" data-panel="{{ $panel['key'] }}">
        @if (empty($panel['items']))
          <p class="agenda-empty">No sessions in this category yet.</p>
        @else
          @foreach ($panel['items'] as $it)
            @php
                $linkedName = $it->workshop
                    ? ($it->workshop->name ?: $it->workshop->title)
                    : ($it->track ? ($it->track->name ?: $it->track->title) : null);
                $entryTitle = $linkedName ?: $it->title;
                // Pink headline on top: workshops use their session title;
                // track sessions show the TRACK TITLE above the company name;
                // other sessions fall back to topic_headline.
                $pinkTitle = $it->workshop
                    ? trim((string) ($it->workshop->title ?: $it->title))
                    : trim((string) (
                        $it->track
                            ? (($it->track->title && $it->track->title !== '-')
                                ? $it->track->title
                                : ($it->topic_headline ?: $it->title))
                            : ($it->topic_headline ?? '')
                    ));
                $spk = $it->speakers->values();
            @endphp
            @php
                $startTime = date('H.i', strtotime($it->start_time));
                $endTime   = date('H.i', strtotime($it->end_time));
                // Display-only overrides — the underlying data (and the Full Agenda) stay
                // untouched. Workshop batches run the full slot; the 14:10 / 16:05 track
                // slots render an extended end time.
                $hm = date('H:i', strtotime($it->start_time));
                if ($panel['key'] === 'workshop') {
                    if ($hm === '13:00') { $endTime = '14.30'; }
                    elseif ($hm === '15:00') { $endTime = '16.30'; }
                }
                if ($panel['key'] === 'track') {
                    if ($hm === '14:10') { $endTime = '14.40'; }
                    elseif ($hm === '16:05') { $endTime = '16.35'; }
                    elseif ($hm === '16:30') { $startTime = '16.35'; }
                }
                $showTime  = !($startTime === '00.00' && $endTime === '00.00');
                // Insert a break banner before the first workshop/track of each afternoon batch.
                $itHm = date('H:i', strtotime($it->start_time));
                $breakKey = $panel['key'] . '|' . $itHm;
                $showBreak = (
                        ($panel['key'] === 'workshop' && in_array($itHm, ['13:00', '15:00'], true))
                        || ($panel['key'] === 'track' && $itHm === '15:00')
                    )
                    && ($lastBreakKey ?? null) !== $breakKey;
                if ($showBreak) { $lastBreakKey = $breakKey; }
                $breakLabel = $showBreak
                    ? ($itHm === '13:00' ? 'Lunch, Networking & Exhibition Booths' : 'Break Session & Exhibition Booths')
                    : '';
                // Track sessions: draw a thick separator line between time groups,
                // except where the break banner replaces it.
                $itSlot = $it->start_time . '|' . $it->end_time;
                $showTrackSep = ($panel['key'] === 'track')
                    && !$loop->first
                    && ($prevTrackSlot ?? null) !== $itSlot
                    && !$showBreak;
                $prevTrackSlot = $itSlot;
            @endphp
            @if ($showTrackSep)
              <hr class="agenda-track-sep">
            @endif
            @if ($showBreak)
              <div class="agenda-break-banner">
                <div class="b-text">
                  <span class="b-label">{{ $breakLabel }}</span>
                </div>
              </div>
            @endif
            <div class="agenda-row{{ ($panel['key'] === 'track' && ($it->track_is_last_in_slot ?? false)) ? ' agenda-row-last' : '' }}" onclick="openAgendaModal({{ $it->id }})" title="View session details">
              <div class="agenda-col-time">
                @if ($showTime)
                  <div class="atime">{{ $startTime }} - {{ $endTime }}</div>
                  <div class="atz">(WIB)</div>
                @endif
                @if ($it->room)
                  <div class="aroom">
                    <span class="aroom-label">Room :</span>
                    <span class="aroom-value">{{ $it->room }}</span>
                  </div>
                @endif
              </div>
              @php
                  $cleanHl   = trim(strip_tags(html_entity_decode($it->key_highlights ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8')));
              @endphp
              <div class="agenda-col-topic">
                @if ($pinkTitle !== '')
                  <span class="agenda-topic-title">{{ $entryTitle }}</span>
                @endif
                @if ($pinkTitle !== '' && $pinkTitle !== '-')
                  <span class="agenda-topic-headline">{{ $pinkTitle }}</span>
                @endif

                @if ($cleanHl !== '')
                  <span class="agenda-topic-highlights">
                    @foreach (preg_split('/\r\n|\r|\n/', $cleanHl) as $line)
                      @if (trim($line) !== '')
                        <span class="agenda-hl-item">• {{ trim($line) }}</span>
                      @endif
                    @endforeach
                  </span>
                @endif
              </div>
              <div class="agenda-col-speakers">
                @foreach ($spk as $sp)
                  @php
                    $spRole = trim(($sp->title ?? '') . ($sp->company ? ' · ' . $sp->company : ''));
                    $spPhoto = $sp->photo;
                    if ($spPhoto && !str_starts_with($spPhoto, 'http') && !str_starts_with($spPhoto, '/')) {
                        $spPhoto = asset('storage/' . $spPhoto);
                    }
                  @endphp
                  <div class="agenda-speaker-card">
                    @if ($spPhoto)
                      <img src="{{ $spPhoto }}" class="agenda-sp-photo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div class="agenda-sp-initial" style="display:none;">{{ mb_strtoupper(mb_substr($sp->name, 0, 1)) }}</div>
                    @else
                      <div class="agenda-sp-initial">{{ mb_strtoupper(mb_substr($sp->name, 0, 1)) }}</div>
                    @endif
                    <div class="agenda-sp-info">
                      <span class="agenda-speaker-name">{{ $msdTitle($sp->name) }}</span>
                      @if ($spRole)
                        <span class="agenda-speaker-role">{{ $spRole }}</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="agenda-col-action">
                @if ($panel['key'] === 'track' && in_array(trim((string) $it->room), ['Ballroom A', 'Ballroom B', 'Ballroom C'], true))
                  <span class="agenda-silent-badge" title="This session is a silent session (audio delivered via headset interpretation)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 0 1 18 0M3 12a2 2 0 0 1 2-2h1v6H5a2 2 0 0 1-2-2v-2zm18 0a2 2 0 0 0-2-2h-1v6h1a2 2 0 0 0 2-2v-2z"/></svg>
                    Silent Session
                  </span>
                @endif
                @if ($it->is_registrable)
                  <button type="button" class="btn-register" onclick="event.stopPropagation(); openAgendaModal({{ $it->id }})">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                    Register
                  </button>
                @endif
              </div>
            </div>
          @endforeach
        @endif
      </div>
    @endforeach

  </div>
</section>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
  var tabs = document.querySelectorAll('#agenda-sections .agenda-sched-tab');
  var panels = document.querySelectorAll('#agenda-sections .agenda-sched-content');
  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      var key = t.getAttribute('data-sess');
      tabs.forEach(function (x) { x.classList.toggle('active', x === t); });
      panels.forEach(function (p) { p.classList.toggle('active', p.getAttribute('data-panel') === key); });
    });
  });
});
</script>

<!-- AGENDA -->
<section id="agenda" class="why reveal">
  <div class="container">
    <p class="section-eyebrow">Agenda</p>
    <h2 class="section-title">Full Agenda</h2>

    @if(isset($timeSlots) && $timeSlots->isNotEmpty())
      @php
        $roomNames = $rooms->pluck('name')->toArray();
        // Group rooms by floor for dynamic header
        $floorGroups = $rooms->groupBy(fn($r) => $r->floorRelation?->name ?? 'Other');
      @endphp
      <div class="table-wrap" style="position:relative;">
        {{-- Overlay instruction --}}
        <div id="agendaOverlay" style="position:absolute;inset:-4px;z-index:50;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(5,13,42,0.88);backdrop-filter:blur(8px);border-radius:20px;cursor:pointer;transition:opacity 0.6s,transform 0.6s;">
          {{-- Desktop content --}}
          <div id="overlayDesktop" style="display:flex;flex-direction:column;align-items:center;">
            <img src="https://img.icons8.com/?size=100&id=pGqqobAPSa_u&format=png&color=000000" style="width:48px;height:48px;margin-bottom:14px;opacity:0.7;filter:brightness(0) invert(1);">
            <p style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:4px;letter-spacing:-0.01em;">Tap any session to explore</p>
            <p style="font-size:13px;color:#94a3b8;max-width:300px;text-align:center;line-height:1.6;">Click on a <strong style="color:#f472b6;">workshop</strong> or <strong style="color:#818cf8;">track</strong> in the table to view details.</p>
          </div>
          {{-- Mobile content --}}
          <div id="overlayMobile" style="display:none;flex-direction:column;align-items:center;padding:0 16px;">
            <img src="https://img.icons8.com/?size=100&id=xP7ywSliik10&format=png&color=000000" style="width:44px;height:44px;margin-bottom:12px;opacity:0.7;filter:brightness(0) invert(1);">
            <p style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:4px;letter-spacing:-0.01em;text-align:center;">Swipe to explore</p>
            <p style="font-size:13px;color:#94a3b8;max-width:280px;text-align:center;line-height:1.6;">Swipe <strong style="color:#f472b6;">horizontally</strong> to see all sessions, then tap any <strong style="color:#818cf8;">workshop</strong> or <strong style="color:#f472b6;">track</strong> for details.</p>
          </div>
        </div>
        <style>
          @media (max-width: 767px) {
            #overlayDesktop { display: none !important; }
            #overlayMobile  { display: flex !important; }
          }
        </style>
        <script>
        (function() {
          var overlay = document.getElementById('agendaOverlay');
          if (!overlay) return;

          var duration = 3000;

          var dismissed = false;
          function dismissOverlay() {
            if (dismissed) return;
            dismissed = true;
            overlay.style.opacity = '0';
            overlay.style.transform = 'scale(0.96)';
            setTimeout(function() {
              overlay.style.display = 'none';
            }, 600);
          }

          // Dismiss on click
          overlay.addEventListener('click', dismissOverlay);

          // Intersection Observer — start timer only when agenda is visible
          var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
              if (entry.isIntersecting && !dismissed) {
                setTimeout(dismissOverlay, duration);
                observer.disconnect();
              }
            });
          }, { threshold: 0.3 });
          observer.observe(overlay);
        })();
        </script>
        <table>
          <thead>
            <tr>
              <th rowspan="2">Time</th>
              @foreach ($floorGroups as $floorName => $floorRooms)
                <th colspan="{{ $floorRooms->count() }}" style="background:{{ $loop->first ? '#eef2ff' : '#fefce8' }}; color:{{ $loop->first ? '#4338ca' : '#a16207' }};">{{ $floorName }}</th>
              @endforeach
            </tr>
            <tr>
              @foreach ($rooms as $rm)
                <th>{{ $rm->name }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @php
                $skipMap = [];
            @endphp
            @foreach ($timeSlots as $ts)
              @php
                $slotKey = $ts->start_time . '-' . $ts->end_time;
                $items = collect($itemMap[$slotKey] ?? []);
                $fullRow = $items->firstWhere(fn($i) => $i->isFullRow());
                $hasPerRoom = $items->contains(fn($i) => !$i->isFullRow());
                if ($hasPerRoom) $fullRow = null;
              @endphp
              <tr>
                <td class="time">{{ $ts->label() }}</td>
                @if ($fullRow)
                  <td class="full" colspan="{{ $rooms->count() }}" data-timeslot="{{ $slotKey }}" data-agenda-id="{{ $fullRow->id }}">
                    @php $fullTitle = $fullRow->workshop ? ($fullRow->workshop->name ?: $fullRow->workshop->title) : ($fullRow->track ? ($fullRow->track->name ?: $fullRow->track->title) : $fullRow->title); @endphp
                    @if ($fullRow->category || $fullRow->agenda_type)
                      <span class="tag {{ \App\Models\AgendaItem::categoryClass($fullRow->category, $fullRow->agenda_type) }}">{{ $fullTitle }}</span>
                    @else
                      {{ $fullTitle }}
                    @endif
                  </td>
                @else
                  @php
                    $cells = [];
                    $colCovered = [];
                    foreach ($roomNames as $rm) {
                        if (isset($colCovered[$rm])) { unset($colCovered[$rm]); continue; }
                        $item = collect($items)->firstWhere('room', $rm);
                        if (isset($skipMap[$rm])) continue;

                        if ($item) {
                            $attrs = '';
                            if ($item->rowspan > 1) {
                                $attrs .= ' rowspan="' . $item->rowspan . '"';
                                $skipMap[$rm] = $item->rowspan;
                            }
                            if ($item->colspan > 1) {
                                $attrs .= ' colspan="' . $item->colspan . '"';
                                $idx = array_search($rm, $roomNames);
                                for ($i = 1; $i < $item->colspan; $i++) {
                                    if (isset($roomNames[$idx + $i])) {
                                        $colCovered[$roomNames[$idx + $i]] = true;
                                        if ($item->rowspan > 1) {
                                            $skipMap[$roomNames[$idx + $i]] = $item->rowspan;
                                        }
                                    }
                                }
                            }
                            $displayTitle = $item->workshop ? ($item->workshop->name ?: $item->workshop->title) : ($item->track ? ($item->track->name ?: $item->track->title) : $item->title);
                            $tag = ($item->category || $item->agenda_type)
                                ? '<span class="tag ' . \App\Models\AgendaItem::categoryClass($item->category, $item->agenda_type) . '">' . e($displayTitle) . '</span>'
                                : e($displayTitle);
                            $cells[] = '<td' . $attrs . ' data-timeslot="' . $slotKey . '" data-agenda-id="' . $item->id . '">' . $tag . '</td>';
                        } else {
                            $cells[] = '<td data-timeslot="' . $slotKey . '">—</td>';
                        }
                    }
                  @endphp
                  {!! implode("\n", $cells) !!}
                @endif
              </tr>
              @php
                foreach ($skipMap as $rm => $rem) {
                    $skipMap[$rm] = $rem - 1;
                    if ($skipMap[$rm] <= 0) unset($skipMap[$rm]);
                }
              @endphp
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th rowspan="2">Time</th>
              @php $floorGroups = $rooms->groupBy(fn($r) => $r->floorRelation?->name ?? 'Other'); @endphp
              @foreach ($floorGroups as $floorName => $floorRooms)
                <th colspan="{{ $floorRooms->count() }}" style="background:{{ $loop->first ? '#eef2ff' : '#fefce8' }}; color:{{ $loop->first ? '#4338ca' : '#a16207' }};">{{ $floorName }}</th>
              @endforeach
            </tr>
            <tr>
              @foreach ($rooms as $rm)
                <th>{{ $rm->name }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach ($timeSlots as $ts)
            <tr>
              <td class="time">{{ $ts->label() }}</td>
              <td colspan="{{ $rooms->count() }}" class="text-center text-gray-400 py-4" style="color:var(--muted)">—</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <div class="agenda-legend">
      <span class="legend-item"><span class="legend-swatch legend-general"></span>General</span>
      <span class="legend-item"><span class="legend-swatch legend-track"></span>Track Session</span>
      <span class="legend-item"><span class="legend-swatch legend-ws"></span>Workshop</span>
    </div>
  </div>
</section>

{{-- Agenda Detail Modal --}}
<div id="agendaModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(5,13,42,0.85);backdrop-filter:blur(12px);padding:20px;overflow-y:auto;">
  <div style="background:rgba(255,255,255,0.05);backdrop-filter:blur(16px);border-radius:24px;box-shadow:0 30px 80px rgba(0,0,0,0.5),inset 0 1px 0 rgba(255,255,255,0.08);width:100%;max-width:600px;max-height:90vh;overflow-y:auto;animation:msdFadeIn 0.35s ease-out;border:1px solid rgba(255,255,255,0.08);">
    {{-- Header bar with close --}}
    <div style="position:sticky;top:0;z-index:10;display:flex;align-items:center;justify-content:space-between;padding:16px 24px;background:linear-gradient(135deg,#050d2a,#0e2461);border-radius:24px 24px 0 0;border-bottom:1px solid rgba(255,255,255,0.06);">
      <div style="display:flex;align-items:center;gap:10px;">
        <img src="{{ asset('img/logo-msd.png') }}" style="height:22px;width:auto;filter:brightness(0) invert(1);">
        {{-- <span style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.6);letter-spacing:1.5px;">MSD 2026</span> --}}
      </div>
      <button onclick="closeAgendaModal()" style="width:30px;height:30px;border-radius:50%;border:none;background:rgba(255,255,255,0.08);font-size:15px;cursor:pointer;color:rgba(255,255,255,0.6);display:flex;align-items:center;justify-content:center;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.15)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.08)';this.style.color='rgba(255,255,255,0.6)'">✕</button>
    </div>

    <div style="padding:28px 28px 32px;">
      {{-- Date, Time & Room --}}
      <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;font-size:12px;">
        <div id="modalDateTime" style="display:flex;align-items:center;gap:6px;color:#f472b6;font-weight:500;"></div>
        <div id="modalRoom" style="display:flex;align-items:center;gap:6px;color:#94a3b8;"></div>
      </div>

      {{-- Type Badge --}}
      <div style="margin-bottom:16px;">
        <span id="modalTypeBadge" style="display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;padding:3px 10px;border-radius:20px;letter-spacing:0.8px;"></span>
        <span id="modalCapacity" style="display:inline-block;font-size:12px;color:#94a3b8;margin-left:10px;"></span>
      </div>

      {{-- Title --}}
      <h2 style="font-size:22px;font-weight:800;color:#e2e8f0;margin-bottom:18px;line-height:1.35;letter-spacing:-0.02em;" id="modalTitle"></h2>

      {{-- Speakers --}}
      <div id="modalSpeakers" style="margin-bottom:24px;padding:20px;background:rgba(255,255,255,0.04);border-radius:16px;border:1px solid rgba(255,255,255,0.06);"></div>

      {{-- Description --}}
      <div id="modalDesc" style="font-size:13px;color:#cbd5e1;line-height:1.7;margin-bottom:24px;"></div>
      <style>#modalDesc, #modalDesc * { color: #cbd5e1 !important; } #modalDesc ul, #modalDesc ol { padding-left: 20px; margin: 8px 0; } #modalDesc li { margin-bottom: 4px; } #modalDesc p { margin: 6px 0; } #modalDesc h4 { font-size: 14px; font-weight: 700; color: #e2e8f0 !important; margin: 12px 0 4px; } #modalDesc strong { color: #e2e8f0 !important; }</style>

      {{-- Key Highlights --}}
      <div id="modalHighlights" style="margin-bottom:24px;"></div>

      {{-- Registration --}}
      <div id="modalRegSection" style="border-top:1px solid rgba(255,255,255,0.08);padding-top:20px;margin-top:12px;"></div>
    </div>
  </div>
</div>

{{-- Speaker Bio Modal --}}
<div id="speakerBioModal" style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center;background:rgba(5,13,42,0.85);backdrop-filter:blur(12px);padding:20px;overflow-y:auto;" onclick="if(event.target===this)closeSpeakerBio()">
  <div style="background:rgba(255,255,255,0.05);backdrop-filter:blur(16px);border-radius:24px;box-shadow:0 30px 80px rgba(0,0,0,0.5),inset 0 1px 0 rgba(255,255,255,0.08);width:100%;max-width:520px;max-height:85vh;overflow-y:auto;animation:msdFadeIn 0.3s ease-out;border:1px solid rgba(255,255,255,0.08);">
    <div style="position:sticky;top:0;z-index:10;display:flex;align-items:center;justify-content:space-between;padding:16px 24px;background:linear-gradient(135deg,#050d2a,#0e2461);border-radius:24px 24px 0 0;border-bottom:1px solid rgba(255,255,255,0.06);">
      <span style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.6);letter-spacing:1.5px;text-transform:uppercase;">Speaker Bio</span>
      <button onclick="closeSpeakerBio()" style="width:30px;height:30px;border-radius:50%;border:none;background:rgba(255,255,255,0.08);font-size:15px;cursor:pointer;color:rgba(255,255,255,0.6);display:flex;align-items:center;justify-content:center;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.15)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.08)';this.style.color='rgba(255,255,255,0.6)'">✕</button>
    </div>
    <div id="speakerBioBody" style="padding:28px;"></div>
  </div>
</div>

<style>
@keyframes msdFadeIn{from{opacity:0;transform:scale(0.92) translateY(20px);}to{opacity:1;transform:scale(1) translateY(0);}}
</style>

<script>
// ── Agenda data for modal (available to all) ──
window._agendaData = {!! json_encode($agendaItems->keyBy('id')->map(function ($item) use ($timeSlots) {
    // Calculate display end_time based on rowspan
    $displayEndTime = $item->end_time;
    if ($item->rowspan > 1 && $timeSlots->isNotEmpty()) {
        $slotIndex = $timeSlots->search(function ($ts) use ($item) {
            return $ts->start_time === $item->start_time && $ts->end_time === $item->end_time;
        });
        if ($slotIndex !== false) {
            $lastSlotIndex = min($slotIndex + $item->rowspan - 1, $timeSlots->count() - 1);
            $lastSlot = $timeSlots->get($lastSlotIndex);
            if ($lastSlot) {
                $displayEndTime = $lastSlot->end_time;
            }
        }
    }
    // Display-only overrides for the detail modal (matching the schedule list) without
    // changing the underlying data. 14:10 / 16:05 render an extended end time; 16:30
    // track sessions render an earlier start time.
    $hm = date('H:i', strtotime($item->start_time));
    $displayStartTime = $item->start_time;
    if ($hm === '14:10') {
        $displayEndTime = '14:40:00';
    } elseif ($hm === '16:05') {
        $displayEndTime = '16:35:00';
    } elseif ($hm === '16:30') {
        $displayStartTime = '16:35:00';
    }
    return array_merge($item->toArray(), [
        // Speaker names in "Camel Style" for the modal display.
        'speakers'             => $item->speakers->map(fn ($s) => array_merge($s->toArray(), [
            'name' => \Illuminate\Support\Str::title(trim((string) $s->name)),
        ]))->values()->all(),
        'display_start_time'   => $displayStartTime,
        'display_end_time'     => $displayEndTime,
        'workshop_name'        => $item->workshop ? ($item->workshop->name ?: $item->workshop->title) : null,
        'workshop_title'       => $item->workshop ? $item->workshop->title : null,
        'workshop_description' => $item->workshop ? $item->workshop->description : null,
        'track_name'           => $item->track ? ($item->track->name ?: $item->track->title) : null,
        'track_title'          => $item->track ? $item->track->title : null,
        'track_description'    => $item->track ? $item->track->description : null,
    ]);
}), JSON_UNESCAPED_SLASHES) !!};

@if (Auth::guard('registrant')->check())
window._agendaRegistrations = {!! json_encode(
    Auth::guard('registrant')->user()->agendaItems()->get()->mapWithKeys(fn($i) => [$i->id => $i->pivot->status]),
    JSON_UNESCAPED_SLASHES
) !!};
window._workshopRegistrations = {!! json_encode(
    Auth::guard('registrant')->user()->workshops()->get()->mapWithKeys(fn($w) => [$w->id => $w->pivot->status]),
    JSON_UNESCAPED_SLASHES
) !!};
window._agendaRegisterUrl = '{{ route('registrant.agenda.register', ['agendaItem' => '__ID__']) }}';
window._agendaUnregisterUrl = '{{ route('registrant.agenda.unregister', ['agendaItem' => '__ID__']) }}';
@endif

// ── Modal Functions ──
function openAgendaModal(id) {
    const item = window._agendaData[id];
    if (!item) return;

    document.getElementById('modalDateTime').innerHTML =
        '<svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="#f472b6" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/><path stroke-width="2" d="M16 2v4M8 2v4M3 10h18"/></svg> ' +
        '<span>' + (item.date || '20 August 2026') + '</span>' +
        '<span style="color:#475569;">·</span>' +
        '<svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="#f472b6" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M12 6v6l4 2"/></svg> ' +
        '<span>' + ((item.display_start_time || item.start_time || '').substring(0,5).replace(':', '.')) + ' - ' + (((item.display_end_time || item.end_time) || '').substring(0,5).replace(':', '.')) + '</span>';
    document.getElementById('modalRoom').innerHTML =
        '<svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5" stroke-width="2"/></svg> ' +
        '<span>Shangri-La Hotel' + (item.room ? ', ' + item.room + ' Room' : '') + '</span>';
    // Topic headline above the title; workshop/track name as main title, agenda item title as subtitle.
    var topicHeadlineHtml = item.topic_headline ? '<span style="font-size:17px;font-weight:700;color:#f472b6;display:block;margin-bottom:4px;">' + item.topic_headline + '</span>' : '';
    if (item.workshop_name) {
        document.getElementById('modalTitle').innerHTML =
            topicHeadlineHtml +
            '<span style="font-size:22px;font-weight:800;color:#e2e8f0;">' + item.workshop_name + '</span>' +
            '<span style="font-size:14px;font-weight:500;color:#94a3b8;display:block;margin-top:4px;">' + item.title + '</span>';
    } else if (item.track_name) {
        document.getElementById('modalTitle').innerHTML =
            topicHeadlineHtml +
            '<span style="font-size:22px;font-weight:800;color:#e2e8f0;">' + item.track_name + '</span>' +
            '<span style="font-size:14px;font-weight:500;color:#94a3b8;display:block;margin-top:4px;">' + item.title + '</span>';
    } else {
        // General / session: company as the main title, topic_headline as the subtitle (same layout as workshop).
        document.getElementById('modalTitle').innerHTML =
            '<span style="font-size:22px;font-weight:800;color:#e2e8f0;">' + item.title + '</span>' +
            ((item.topic_headline && item.topic_headline !== '-') ? '<span style="font-size:14px;font-weight:500;color:#94a3b8;display:block;margin-top:4px;">' + item.topic_headline + '</span>' : '');
    }

    // Type badge with fallback logic
    const badge = document.getElementById('modalTypeBadge');
    let type = item.agenda_type;
    if (!type && item.category === 'workshop') type = 'workshop';
    if (!type && item.track_id) type = 'track';
    if (!type && item.workshop_id) type = 'workshop';
    if (!type) type = 'session';
    badge.textContent = type.toUpperCase();
    if (type === 'workshop') {
        badge.style.background = 'rgba(251,191,36,0.15)';
        badge.style.color = '#fbbf24';
        badge.style.border = '1px solid rgba(251,191,36,0.2)';
    } else if (type === 'track') {
        badge.style.background = 'rgba(129,140,248,0.15)';
        badge.style.color = '#818cf8';
        badge.style.border = '1px solid rgba(129,140,248,0.2)';
    } else if (type === 'keynote') {
        badge.style.background = 'rgba(52,211,153,0.15)';
        badge.style.color = '#34d399';
        badge.style.border = '1px solid rgba(52,211,153,0.2)';
    } else {
        badge.style.background = 'rgba(148,163,184,0.15)';
        badge.style.color = '#94a3b8';
        badge.style.border = '1px solid rgba(148,163,184,0.2)';
    }

    // Capacity
    const capEl = document.getElementById('modalCapacity');
    if (item.capacity > 0) {
        capEl.textContent = 'Capacity: ' + (item.approved_count || 0) + '/' + item.capacity;
    } else {
        capEl.textContent = '';
    }

    // Speakers from relationship with all details
    window._currentAgendaItem = item;   // keep current item for the speaker bio popup
    let speakersHtml = '';
    if (item.speakers && item.speakers.length > 0) {
        speakersHtml += '<h4 style="font-size:12px;font-weight:700;color:#94a3b8;margin-bottom:14px;text-transform:uppercase;letter-spacing:1px;"><svg style="width:14px;height:14px;vertical-align:-2px;margin-right:6px;" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-width="2" d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path stroke-width="2" d="M19 10v2a7 7 0 0 1-14 0v-2"/><line stroke-width="2" x1="12" y1="19" x2="12" y2="23"/><line stroke-width="2" x1="8" y1="23" x2="16" y2="23"/></svg> Speaker' + (item.speakers.length > 1 ? 's' : '') + '</h4>';
        item.speakers.forEach(function(sp, idx) {
            speakersHtml += '<div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid rgba(255,255,255,0.06);">';
            var avatarInner = '';
            if (sp.photo) {
                var photoUrl = sp.photo;
                if (!photoUrl.startsWith('http') && !photoUrl.startsWith('/')) {
                    photoUrl = '{{ asset('storage') }}/' + photoUrl;
                }
                avatarInner = '<img src="'+photoUrl+'" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.1);flex-shrink:0;margin-top:2px;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">'
                    + '<div style="display:none;width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;margin-top:2px;">'+sp.name.charAt(0).toUpperCase()+'</div>';
            } else {
                avatarInner = '<div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;margin-top:2px;">'+sp.name.charAt(0).toUpperCase()+'</div>';
            }
            speakersHtml += '<button type="button" onclick="openSpeakerBio('+idx+')" title="View speaker bio" style="padding:0;border:none;background:none;cursor:pointer;border-radius:50%;flex-shrink:0;transition:transform 0.15s,box-shadow 0.15s;" onmouseover="this.style.transform=\'scale(1.08)\';this.style.boxShadow=\'0 0 0 3px rgba(244,114,182,0.25)\'" onmouseout="this.style.transform=\'\';this.style.boxShadow=\'\'">'+avatarInner+'</button>';
            speakersHtml += '<div style="flex:1;min-width:0;">';
            speakersHtml += '<p style="font-weight:700;font-size:14px;color:#e2e8f0;cursor:pointer;display:inline-block;border-bottom:1px solid transparent;transition:border-color 0.15s,color 0.15s;" onmouseover="this.style.borderColor=\'#f472b6\';this.style.color=\'#f472b6\'" onmouseout="this.style.borderColor=\'transparent\';this.style.color=\'#e2e8f0\'" onclick="openSpeakerBio('+idx+')">'+sp.name+'</p>';
            speakersHtml += '<p style="font-size:12px;color:#64748b;margin-bottom:6px;">'+(sp.title||'')+(sp.company ? ' <span style="color:#475569;">·</span> '+sp.company : '')+'</p>';

            // Presentation title
            if (sp.pivot && sp.pivot.presentation_title) {
                speakersHtml += '<p style="font-weight:600;font-size:13px;color:#f472b6;margin-bottom:4px;"><svg style="width:13px;height:13px;vertical-align:-2px;margin-right:5px;" fill="none" stroke="#f472b6" viewBox="0 0 24 24"><path stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline stroke-width="2" points="14 2 14 8 20 8"/><line stroke-width="2" x1="9" y1="13" x2="15" y2="13"/><line stroke-width="2" x1="9" y1="17" x2="13" y2="17"/></svg> ' + sp.pivot.presentation_title + '</p>';
            }
            // Presentation description
            if (sp.pivot && sp.pivot.presentation_description) {
                speakersHtml += '<p style="font-size:12px;color:#94a3b8;line-height:1.6;margin-bottom:8px;">' + sp.pivot.presentation_description.replace(/\n/g,'<br>') + '</p>';
            }
            // Key highlights
            if (sp.pivot && sp.pivot.key_highlights) {
                speakersHtml += '<ul style="margin-top:4px;padding-left:16px;font-size:12px;color:#94a3b8;line-height:1.9;">';
                sp.pivot.key_highlights.split('\n').filter(function(l){return l.trim();}).forEach(function(l){
                    speakersHtml += '<li style="padding-left:2px;">' + l.replace(/^\d+\.\s*/, '') + '</li>';
                });
                speakersHtml += '</ul>';
            }
            speakersHtml += '</div></div>';
        });
    }
    document.getElementById('modalSpeakers').innerHTML = speakersHtml || '<p style="font-size:13px;color:#64748b;text-align:center;">No speaker assigned</p>';

    // Description (workshop description takes precedence if linked)
    const descEl = document.getElementById('modalDesc');
    var descText = item.workshop_description || item.track_description || item.description || '';
    if (descText) {
        // Strip inline color/font styles from Summernote output
        descText = descText.replace(/<span[^>]*style="[^"]*color:[^"]*"[^>]*>/gi, '<span>');
        descText = descText.replace(/style="[^"]*color:[^;"]*[^"]*"/gi, '');
        descText = descText.replace(/<font[^>]*color[^>]*>/gi, '<span>');
        descText = descText.replace(/<\/font>/gi, '</span>');
        descEl.innerHTML = '<strong style="color:#e2e8f0;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">Session Description</strong><br><br><span style="color:#cbd5e1;">' + descText.replace(/\n/g,'<br>') + '</span>';
    } else {
        descEl.innerHTML = '';
    }

    // Hide global highlights section (now per-speaker)
    document.getElementById('modalHighlights').innerHTML = '';

    // Registration section
    const regSection = document.getElementById('modalRegSection');
    const isLoggedIn = typeof window._agendaRegistrations !== 'undefined';
    const canReg = item.is_registrable;

    if (!canReg) {
        regSection.innerHTML = '<p style="font-size:13px;color:#64748b;text-align:center;">Registration not available for this session.</p>';
    } else if (!isLoggedIn) {
        var loginUrl = '{{ route('login', request()->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'])) }}';
        regSection.innerHTML = '<div style="text-align:center;padding:12px 0;">' +
            '<p style="font-size:13px;color:#94a3b8;">Please <a href="'+loginUrl+'" style="color:#f472b6;font-weight:600;text-decoration:underline;text-underline-offset:2px;">login</a> to register for this session.</p>' +
        '</div>';
    } else {
        var regStatus = window._agendaRegistrations[id] || null;
        // Fallback: check workshop registration status if linked to a workshop
        if (!regStatus && item.workshop_id && window._workshopRegistrations) {
            regStatus = window._workshopRegistrations[item.workshop_id] || null;
        }
        if (regStatus === 'approved') {
        regSection.innerHTML = '<div style="text-align:center;"><div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.2);margin-bottom:14px;"><svg style="width:16px;height:16px;" fill="none" stroke="#34d399" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2.5" d="M8 12l3 3 5-5"/></svg> You are registered</div></div>';
    } else if (regStatus === 'pending') {
        regSection.innerHTML = '<div style="text-align:center;"><div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(251,191,36,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.2);margin-bottom:14px;"><svg style="width:16px;height:16px;" fill="none" stroke="#fbbf24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M12 6v6l4 2"/></svg> Pending Approval</div>' +
            '<form action="'+window._agendaUnregisterUrl.replace('__ID__',id)+'" method="POST">@csrf<button style="padding:10px 28px;background:rgba(239,68,68,0.1);color:#ef4444;font-weight:600;font-size:13px;border:1px solid rgba(239,68,68,0.2);border-radius:999px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background=\'rgba(239,68,68,0.2)\'" onmouseout="this.style.background=\'rgba(239,68,68,0.1)\'">Cancel</button></form></div>';
    } else if (regStatus === 'rejected') {
        regSection.innerHTML = '<div style="text-align:center;"><div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.2);margin-bottom:14px;"><svg style="width:16px;height:16px;" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" d="M15 9l-6 6M9 9l6 6"/></svg> Rejected</div>' +
            '<form action="'+window._agendaRegisterUrl.replace('__ID__',id)+'" method="POST">@csrf<button style="padding:12px 36px;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;font-weight:700;font-size:14px;border:none;border-radius:999px;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:all 0.25s;" onmouseover="this.style.transform=\'translateY(-2px)\';this.style.boxShadow=\'0 12px 30px rgba(233,30,99,0.5)\'" onmouseout="this.style.transform=\'\';this.style.boxShadow=\'0 8px 24px rgba(233,30,99,0.35)\""><svg style="width:15px;height:15px;vertical-align:-2px;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M1 4v6h6"/><path stroke-width="2.5" d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg> Re-register</button></form></div>';
    } else {
        const capInfo = item.capacity > 0 ? '<div style="font-size:12px;color:#64748b;margin-bottom:10px;">'+ (item.approved_count || 0) +'/'+ item.capacity +' seats filled</div>' : '';
        regSection.innerHTML = '<div style="text-align:center;">'+capInfo+
            '<form action="'+window._agendaRegisterUrl.replace('__ID__',id)+'" method="POST">@csrf<button style="padding:12px 44px;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;font-weight:700;font-size:14px;letter-spacing:0.03em;border:none;border-radius:999px;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:all 0.25s;" onmouseover="this.style.transform=\'translateY(-2px)\';this.style.boxShadow=\'0 12px 30px rgba(233,30,99,0.5)\'" onmouseout="this.style.transform=\'\';this.style.boxShadow=\'0 8px 24px rgba(233,30,99,0.35)\'">Register Now</button></form></div>';
    }
        }

    document.getElementById('agendaModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeAgendaModal() {
    document.getElementById('agendaModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('agendaModal').addEventListener('click', function(e) {
    if (e.target === this) closeAgendaModal();
});

// ── Speaker bio popup ──
function escHtml(v) {
    return String(v == null ? '' : v)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
function openSpeakerBio(idx) {
    var item = window._currentAgendaItem;
    if (!item || !item.speakers || !item.speakers[idx]) return;
    var sp = item.speakers[idx];
    var body = document.getElementById('speakerBioBody');
    if (!body) return;
    var photoUrl = sp.photo || '';
    if (photoUrl && !photoUrl.startsWith('http') && !photoUrl.startsWith('/')) {
        photoUrl = '{{ asset('storage') }}/' + photoUrl;
    }
    var bio = escHtml(sp.bio || '').trim();
    var isPlaceholder = bio.length === 0 || /^(tbc|to be confirm(ed)?|work in progress|wip|pending)$/i.test(bio.replace(/[^a-zA-Z]/g, ''));
    var avatarInner = photoUrl
        ? '<img src="'+photoUrl+'" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.12);flex-shrink:0;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">'
          + '<div style="display:none;width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);align-items:center;justify-content:center;color:#fff;font-size:26px;font-weight:700;flex-shrink:0;">'+escHtml((sp.name||'?').charAt(0).toUpperCase())+'</div>'
        : '<div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#ff3d6e,#e91e63);display:flex;align-items:center;justify-content:center;color:#fff;font-size:26px;font-weight:700;flex-shrink:0;">'+escHtml((sp.name||'?').charAt(0).toUpperCase())+'</div>';
    body.innerHTML =
        '<div style="display:flex;align-items:center;gap:18px;margin-bottom:22px;">'
        + avatarInner
        + '<div style="min-width:0;">'
        + '<p style="font-size:19px;font-weight:800;color:#e2e8f0;line-height:1.3;">' + escHtml(sp.name) + '</p>'
        + ((sp.title || sp.company) ? '<p style="font-size:13px;color:#94a3b8;margin-top:3px;">' + escHtml(sp.title || '') + (sp.title && sp.company ? ' <span style="color:#475569;">·</span> ' : '') + escHtml(sp.company || '') + '</p>' : '')
        + '</div></div>'
        + (isPlaceholder
            ? '<p style="font-size:13px;color:#64748b;font-style:italic;">Bio not yet available for this speaker.</p>'
            : '<div style="font-size:13px;color:#cbd5e1;line-height:1.8;white-space:pre-wrap;">' + bio + '</div>');
    document.getElementById('speakerBioModal').style.display = 'flex';
}
function closeSpeakerBio() {
    document.getElementById('speakerBioModal').style.display = 'none';
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('speakerBioModal').style.display === 'flex') {
            closeSpeakerBio();
        } else {
            closeAgendaModal();
        }
    }
});

// ── Make agenda items clickable ──
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('#agenda table');
    if (!table) return;
    table.addEventListener('click', function(e) {
        const cell = e.target.closest('td');
        if (!cell) return;
        const agendaId = cell.getAttribute('data-agenda-id');
        if (agendaId && window._agendaData[agendaId]) {
            openAgendaModal(agendaId);
            return;
        }
        // Fallback: match by text content
        const title = cell.textContent.trim();
        if (!title || title === '—' || title === 'Time') return;
        for (const [id, item] of Object.entries(window._agendaData)) {
            if (item.title === title || item.workshop_name === title || item.track_name === title) {
                openAgendaModal(id);
                return;
            }
        }
    });
});
</script>

<!-- SPONSORS -->
<section id="sponsors" class="reveal">
  <div class="container">
    <p class="section-eyebrow">Sponsors</p>
    <h2 class="section-title">Our trusted technology partners</h2>

    <div class="sponsors-block" style="margin-top:48px">
      <div class="sponsor-tier">Platinum</div>
      <div class="sponsor-grid">
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/anaplan.png') }}" alt="Anaplan"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/aws.png') }}" alt="AWS"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/cloudera.png') }}" alt="Cloudera"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/google_cloud.png') }}" alt="Google Cloud"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/IBM.png') }}" alt="IBM"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/Microsoft.png') }}" alt="Microsoft"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/PaloAlto Network.png') }}" alt="Palo Alto"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/REHDAT.png') }}" alt="Red Hat"></div>
        <div class="sponsor"><img src="{{ asset('img/PLATINUM/SALESFORCE.png') }}" alt="Salesforce"></div>
      </div>
    </div>

    <div class="sponsors-block">
      <div class="sponsor-tier">Workshop</div>
      <div class="sponsor-grid">
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/Alibaba_Cloud.png') }}" alt="Alicloud"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/Cloudflare.png') }}" alt="Cloudflare"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/Confluent.png') }}" alt="Confluent"></div>
        <!-- <div class="sponsor"><img src="{{ asset('img/WORKSHOP/Creatio.png') }}" alt="Creatio"></div> -->
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/google_cloud.png') }}" alt="Google Cloud"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/NetApp.png') }}" alt="NetApp"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/REHDAT.png') }}" alt="Red Hat"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/SANGFOR.png') }}" alt="Sangfor"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/singleStore.png') }}" alt="SingleStore"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/Workday.png') }}" alt="Workday"></div>
      </div>
    </div>
    <div class="sponsors-block">
      <div class="sponsor-tier">Gold</div>
      <div class="sponsor-grid">
        <div class="sponsor"><img src="{{ asset('img/GOLD/BytePlus.png?v=2') }}" alt="Byteplus"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Confluent.png') }}" alt="Confluent"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Cyble.png') }}" alt="Cyble"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Datadog.png') }}" alt="Datadog"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Dynatrace.png') }}" alt="Dynatrace"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/EDB.png?v=2') }}" alt="EDB Postgres"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Fortinet.png') }}" alt="Fortinet"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/HPE.png') }}" alt="HPE"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/HP.png') }}" alt="HP Inc"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Huawei.png') }}" alt="Huawei"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/KONG.png') }}" alt="KONG"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/LARK.png') }}" alt="Lark"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Proofpoint.png') }}" alt="Proofpoint"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Tenable.png') }}" alt="Tenable"></div>
      </div>
    </div>


    <div class="sponsors-block">
      <div class="sponsor-tier">Exhibitor</div>
      <div class="sponsor-grid">
        <!-- <div class="sponsor"><img src="{{ asset('img/metrodata.png') }}" alt="Metrodata Electronics"></div> -->
        <div class="sponsor"><img src="{{ asset('img/EXHIBITOR/Asus-Business.png') }}" alt="Asus Business"></div>
        <div class="sponsor"><img src="{{ asset('img/EXHIBITOR/Splunk.png') }}" alt="Splunk"></div>
        <div class="sponsor"><img src="{{ asset('img/EXHIBITOR/FanRuan.png') }}" alt="FanRuan"></div>
        <div class="sponsor"><img src="{{ asset('img/EXHIBITOR/Snowflake.png') }}" alt="Snowflake"></div>
      </div>
    </div>
    <div class="sponsors-block">
      <div class="sponsor-tier">Proud Collaborators</div>
      <div class="sponsor-grid">
        <!-- <div class="sponsor"><img src="{{ asset('img/metrodata.png') }}" alt="Metrodata Electronics"></div> -->
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/SMI.png') }}" alt="SMI"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/MII.png') }}" alt="MII"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/SOLTIUS.png') }}" alt="Soltius"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/MY ICON TECHNOLOGY.png') }}" alt="MIT"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/SINERGI TRANSFORMASI DIGITAL-01.png') }}" alt="Sinergi"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/FMI.png') }}" alt="FMI"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/PACKET_SYSTEMS.png') }}" alt="Packet Systems"></div>
        <div class="sponsor"><img src="{{ asset('img/METRODATA-GROUP/CMI.png') }}" alt="CMI"></div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<!-- CTA -->
<!-- <section class="cta reveal">
  <div class="container">
    <h2>Register now and get your full experience of MSD 2026</h2>
    <p>Free admission with mandatory RSVP. Seats are limited.</p>
    <a href="#register" class="btn">Register Now</a>
  </div>
</section> -->

@unless (Auth::guard('registrant')->check())
<!-- REGISTER -->
<section id="register" class="register reveal">
  <div class="container">
    <p class="section-eyebrow">Register</p>
    <h2 class="section-title">Registration Form</h2>
    <!-- <div class="reg-notice">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>Registration is not yet open. Please check back on <strong>July 20, 2026</strong>.</span>
    </div> -->
    <div class="form-wrap">
      <form id="regForm" class="form-grid" method="POST" action="{{ route('register.submit') }}" data-force-open="{{ $registrationForcedOpen ? 'true' : 'false' }}">
        @csrf
        <div class="field"><label>First Name</label><input required name="firstName" placeholder="First Name" /><span class="field-err" data-field="firstName"></span></div>
        <div class="field"><label>Last Name</label><input required name="lastName" placeholder="Last Name" /><span class="field-err" data-field="lastName"></span></div>
        <div class="field">
          <label>Job Function</label>
          <select name="job_title" required>
            <option value="">Select Job Function</option>
            <option>Intern</option>
            <option>Staff</option>
            <option>Supervisor</option>
            <option>Manager</option>
            <option>Senior Manager</option>
            <option>General Manager</option>
            <option>Head of Department</option>
            <option>Chief</option>
            <option>Director</option>
            <option>President</option>
            <option>Vice President</option>
          </select>
          <span class="field-err" data-field="job_title"></span>
        </div>
        <div class="field">
          <label>Job Title</label>
          <select id="jobRoleSelector" name="job_role_selector" required>
            <option value="">Select Job Title</option>
            <option>Student</option>
            <option>Sales</option>
            <option>Pre-Sales / Solution Architect</option>
            <option>Engineering</option>
            <option>Marketing</option>
            <option>Management</option>
            <option>Finance / Accounting</option>
            <option>Information Technology</option>
            <option>Operations</option>
            <option>Human Resources</option>
            <option>Legal / Compliance</option>
            <option>Procurement</option>
            <option>Research & Development</option>
            <option>Customer Service / Support</option>
            <option>Consulting</option>
            <option>Business Development</option>
            <option>Administration</option>
          </select>
          <span class="field-err" data-field="job_role"></span>
        </div>
        <div class="field" id="jobRoleSpecificField" style="display:none;">
          <label>Job Role</label>
          <select id="jobRoleSpecific">
            <option value="">Select Job Role</option>
            <option>IT Infrastructure</option>
            <option>Infrastructure Engineer</option>
            <option>System Administrator</option>
            <option>Network Administrator</option>
            <option>Network Engineer</option>
            <option>IT Operations</option>
            <option>IT Support</option>
            <option>Helpdesk</option>
            <option>Desktop Support</option>
            <option>NOC Engineer</option>
            <option>Data Center Engineer</option>
            <option>IT Security</option>
            <option>Cyber Security Analyst</option>
            <option>Security Engineer</option>
            <option>Security Operations Center</option>
            <option>DevSecOps Engineer</option>
            <option>IT Governance, Risk &amp; Compliance (GRC)</option>
            <option>Software Engineer</option>
            <option>Software Developer</option>
            <option>Full Stack Developer</option>
            <option>Front-End Developer</option>
            <option>Back-End Developer</option>
            <option>Mobile Developer</option>
            <option>Web Developer</option>
            <option>Application Developer</option>
            <option>Technical Lead</option>
            <option>Engineering Manager</option>
            <option>Enterprise Architect</option>
            <option>Solution Architect</option>
            <option>Technical Architect</option>
            <option>Cloud Architect</option>
            <option>Application Architect</option>
            <option>Data Analyst</option>
            <option>Business Intelligence (BI) Analyst</option>
            <option>Data Engineer</option>
            <option>Data Scientist</option>
            <option>AI Engineer</option>
            <option>Machine Learning Engineer</option>
            <option>Analytics</option>
            <option>ERP</option>
            <option>ERP Basis</option>
            <option>ERP Functional</option>
            <option>ERP Consultant</option>
            <option>Business Application</option>
            <option>Cloud Engineer</option>
            <option>Cloud Administrator</option>
            <option>DevOps Engineer</option>
            <option>Site Reliability Engineer (SRE)</option>
            <option>Platform Engineer</option>
            <option>Engineer</option>
            <option>Database Administrator (DBA)</option>
            <option>Database Engineer</option>
            <option>Database Architect</option>
            <option>IT Project</option>
            <option>IT Governance</option>
            <option>IT Compliance</option>
            <option>IT Audit</option>
            <option>IT Risk</option>
            <option>Digital Transformation</option>
            <option>Digital Innovation</option>
            <option>Digital Technology</option>
            <option>IT Transformation</option>
            <option>IT Business Analyst</option>
            <option>System Analyst</option>
            <option>IT Business Solution</option>
            <option>IT Product Owner</option>
            <option>IT Product</option>
            <option>QA Engineer</option>
            <option>Software</option>
            <option>IT Engineer</option>
            <option>IT Automation Engineer</option>
            <option>IT Quality Assurance</option>
          </select>
          <span class="field-err" data-field="job_role_specific"></span>
        </div>
        <input type="hidden" name="job_role" id="jobRoleFinal">
        <div class="field"><label>Company Name</label><input required name="company" placeholder="Company Name" /><span class="field-err" data-field="company"></span></div>
        <div class="field"><label>Business Email</label><input required type="email" name="email" placeholder="yourname@company.com" /><span class="field-err" data-field="email"></span><small style="color:#94a3b8;font-size:11px;"></small></div>
        <div class="field"><label>Mobile Phone</label><div class="phone-wrapper" style="display:flex;align-items:stretch;gap:0;"><span class="phone-prefix" id="phonePrefix1" style="display:flex;align-items:center;padding:12px 10px;background:rgba(255,255,255,.08);border:1px solid var(--line,#d1d5db);border-right:none;border-radius:10px 0 0 10px;font-size:14px;color:rgba(255,255,255,.45);white-space:nowrap;flex-shrink:0;transition:border-color .25s,box-shadow .25s,background .25s;">+62</span><input required name="phone" placeholder="815-xxx-xxxx" class="phone-input" id="phoneInput1" style="flex:1;border-radius:0 10px 10px 0;border-left:none;" oninput="updatePhonePrefix(this)" onfocus="this.parentNode.querySelector('.phone-prefix').style.borderColor='var(--pink,#e91e63)';this.parentNode.querySelector('.phone-prefix').style.boxShadow='0 0 0 3px rgba(233,30,99,.15)';this.parentNode.querySelector('.phone-prefix').style.background='rgba(255,255,255,.12)'" onblur="this.parentNode.querySelector('.phone-prefix').style.borderColor='';this.parentNode.querySelector('.phone-prefix').style.boxShadow='';updatePhonePrefix(this)" /></div><span class="field-err" data-field="phone"></span></div>
<script>function updatePhonePrefix(el){var v=el.value.replace(/[^0-9]/g,'').replace(/^0/,'');el.value=v;var p=el.parentNode.querySelector('.phone-prefix');if(v){p.style.background='#fff';p.style.color='#374151'}else{p.style.background='rgba(255,255,255,.08)';p.style.color='rgba(255,255,255,.45)'}}
document.addEventListener('DOMContentLoaded',function(){var e=document.getElementById('phoneInput1');if(e&&e.value)updatePhonePrefix(e)});</script>
        <div class="field">
          <label>Industry</label>
          <select name="industry" required>
            <option value="">Select Industry</option>
            <option>AGRICULTURE, FORESTRY</option>
            <option>CHEMICALS</option>
            <option>CONSTRUCTION, PROPERTY & REAL ESTATE</option>
            <option>DISTRIBUTION</option>
            <option>EDUCATION</option>
            <option>FINANCIAL SERVICES</option>
            <option>FISHING & MARINE</option>
            <option>FOREIGN SERVICES</option>
            <option>GOVERNMENT SERVICES</option>
            <option>HEALTHCARE</option>
            <option>HIGH TECHNOLOGY</option>
            <option>HOSPITALITY / TOURISM</option>
            <option>MANUFACTURING</option>
            <option>MEDIA</option>
            <option>MINING & METALS</option>
            <option>OIL & GAS</option>
            <option>PROFESSIONAL & BUSINESS SERVICES</option>
            <option>RETAIL, WHOLESALE</option>
            <option>TELECOMMUNICATIONS</option>
            <option>TRANSPORTATION</option>
            <option>UTILITIES / PUBLIC SERVICES</option>
          </select>
          <span class="field-err" data-field="industry"></span>
        </div>
        <div class="field">
          <label>How did you hear about this event?</label>
          <select name="referral_source" required>
            <option value="">Select one</option>
            <option>LinkedIn</option>
            <option>Instagram</option>
            <option>Kompas Newspaper</option>
            <option>Metrodata Website</option>
            <option>Email</option>
            <option>Metrodata Group Sales Representative / Colleague</option>
          </select>
          <span class="field-err" data-field="referral_source"></span>
        </div>
        <div class="field">
          <label>Number of Employee</label>
          <select name="employees" required>
            <option value="">Select Number of Employee</option>
            <option>1 – 50</option><option>51 – 200</option>
            <option>201 – 500</option><option>501 – 1000</option>
            <option>1000+</option>
          </select>
          <span class="field-err" data-field="employees"></span>
        </div>
        <div class="field full gdpr-group">
          <label class="checkbox-label">
            <input type="checkbox" name="gdpr" required checked>
            <span>By submitting this form, I understand Metrodata will process my personal information in accordance with their <strong><a href="https://www.metrodata.co.id/privacy-policy" target="_blank">Privacy Notice</a></strong>. Additionally, I consent to my information being shared with <strong><a href="https://jovenindo.com/privacy-policy" target="_blank">Event Partners</a></strong> in accordance. I understand I may withdraw my consent or update my information at any time.</span>
          </label>
        </div>
        {{-- UTM hidden fields --}}
        <input type="hidden" name="utm_source" id="utm_source" value="">
        <input type="hidden" name="utm_medium" id="utm_medium" value="">
        <input type="hidden" name="utm_campaign" id="utm_campaign" value="">
        <input type="hidden" name="utm_content" id="utm_content" value="">
        <div class="field full" style="margin-top:8px">
          <button type="submit" class="btn btn-submit">Submit Registration <span class="btn-arrow">→</span></button>
        </div>
      </form>
      <aside class="info">
        <div class="countdown-wrap">
      <p class="countdown-label">Registration Opens In</p>
      <div class="countdown" id="countdown">
        <div class="countdown-item"><span class="countdown-num" id="count-days">00</span><span class="countdown-lbl">Days</span></div>
        <div class="countdown-sep">:</div>
        <div class="countdown-item"><span class="countdown-num" id="count-hours">00</span><span class="countdown-lbl">Hours</span></div>
        <div class="countdown-sep">:</div>
        <div class="countdown-item"><span class="countdown-num" id="count-minutes">00</span><span class="countdown-lbl">Minutes</span></div>
        <div class="countdown-sep">:</div>
        <div class="countdown-item"><span class="countdown-num" id="count-seconds">00</span><span class="countdown-lbl">Seconds</span></div>
      </div>
    </div>
        <h4>Registration Information</h4>
        <ul>
          <li>Metrodata Solution Day is <strong>free admission</strong> with mandatory RSVP.</li>
          <li>Access will be granted exclusively to participants who receive a <strong>confirmation email</strong>.</li>
          <li>Please have your confirmation email ready for verification upon arrival.</li>
          <li><strong>Workshop participation</strong> requires separate registration due to limited seating.</li>
        </ul>
      </aside>
    </div>
  </div>
</section>
@endunless

<footer>
  <div class="container">
    © 2026 <strong>Metrodata Solution Day</strong> — Jakarta, 20 August 2026 · Shangri-La Hotel
  </div>
</footer>

</div>

<script src="{{ asset('js/main.js') }}?v=12"></script>
<script>
// Job Function / Job Title / Job Role logic:
// - "Job Role" dropdown only appears when Job Title = "Information Technology"
// - DB job_role = "{Job Title} - {Job Role}"
(function() {
  var jobTitleSelect = document.getElementById('jobRoleSelector');
  var jobRoleField = document.getElementById('jobRoleSpecificField');
  var jobRoleSelect = document.getElementById('jobRoleSpecific');
  var jobRoleFinal = document.getElementById('jobRoleFinal');
  var regForm = document.getElementById('regForm');

  function updateJobRole() {
    if (!jobTitleSelect || !jobRoleFinal) return;
    var title = jobTitleSelect.value || '';
    var isIT = title === 'Information Technology';

    if (jobRoleField) jobRoleField.style.display = isIT ? '' : 'none';
    if (jobRoleSelect) {
      jobRoleSelect.required = isIT;
      if (!isIT) jobRoleSelect.value = '';
    }

    // Read the specific role AFTER possible clearing (non-IT)
    var specific = jobRoleSelect ? jobRoleSelect.value : '';
    jobRoleFinal.value = specific ? (title + ' - ' + specific) : title;
  }

  if (jobTitleSelect) jobTitleSelect.addEventListener('change', updateJobRole);
  if (jobRoleSelect) jobRoleSelect.addEventListener('change', updateJobRole);
  if (regForm) regForm.addEventListener('reset', updateJobRole);

  // Init (e.g., after a failed submit with old values restored)
  updateJobRole();
})();
</script>
<style>
.field-err { display:block; font-size:12px; color:#ef4444; margin-top:2px; min-height:0; }
.field-err:empty { display:none; }
input.field-error, select.field-error { border-color:#ef4444 !important; }
</style>

{{-- Success / Notification Modal --}}
<div id="successModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(5,13,42,0.7); backdrop-filter:blur(8px); padding:16px;">
  <div style="background:rgba(255,255,255,0.06); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.1); border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08); width:100%; max-width:420px; overflow:hidden; animation:fadeInUp 0.35s ease-out;">
    <div style="padding:36px 32px 28px; text-align:center;">
      <div id="notifIcon" style="width:72px; height:72px; background:rgba(16,185,129,0.15); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 22px; border:1px solid rgba(16,185,129,0.2);">
        <svg id="notifIconSvg" style="width:34px; height:34px; color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <h2 id="notifTitle" style="font-size:19px; font-weight:700; color:#e2e8f0; margin-bottom:6px; letter-spacing:-0.01em;">Notification</h2>
      <p id="notifMessage" style="font-size:14px; color:#94a3b8; line-height:1.6; margin-bottom:24px;"></p>
      <button onclick="closeSuccessModal()" style="width:100%; padding:12px 0; background:linear-gradient(135deg,#ff3d6e,#e91e63); color:#fff; font-weight:700; font-size:14px; letter-spacing:0.03em; border:none; border-radius:999px; cursor:pointer; box-shadow:0 8px 24px rgba(233,30,99,0.35); transition:transform 0.25s,box-shadow 0.25s;"
              onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,0.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,0.35)'">
        Close
      </button>
    </div>
  </div>
</div>

<script>
// ── Theme the notification icon based on flash type ──
@if (session('success'))
document.addEventListener('DOMContentLoaded', function() {
    var icon = document.getElementById('notifIcon');
    var svg = document.getElementById('notifIconSvg');
    var title = document.getElementById('notifTitle');
    var modal = document.getElementById('successModal');
    icon.style.background = 'rgba(16,185,129,0.15)';
    icon.style.borderColor = 'rgba(16,185,129,0.2)';
    svg.style.color = '#10b981';
    svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
    title.textContent = 'Registration Successful';
    document.getElementById('notifMessage').innerHTML = '{!! str_replace(["'"], ["\\'"], session('success')) !!}';
    modal.setAttribute('data-scroll-to', '#agenda-sections');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
});
@elseif (session('error'))
document.addEventListener('DOMContentLoaded', function() {
    var icon = document.getElementById('notifIcon');
    var svg = document.getElementById('notifIconSvg');
    var title = document.getElementById('notifTitle');
    icon.style.background = 'rgba(239,68,68,0.15)';
    icon.style.borderColor = 'rgba(239,68,68,0.2)';
    svg.style.color = '#ef4444';
    svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>';
    title.textContent = 'Notice';
    document.getElementById('notifMessage').innerHTML = '{!! str_replace(["'"], ["\\'"], session('error')) !!}';
    document.getElementById('successModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
});
@elseif (session('info'))
document.addEventListener('DOMContentLoaded', function() {
    var icon = document.getElementById('notifIcon');
    var svg = document.getElementById('notifIconSvg');
    var title = document.getElementById('notifTitle');
    icon.style.background = 'rgba(59,130,246,0.15)';
    icon.style.borderColor = 'rgba(59,130,246,0.2)';
    svg.style.color = '#3b82f6';
    svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
    title.textContent = 'Workshop Invitation';
    document.getElementById('notifMessage').innerHTML = '{!! str_replace(["'"], ["\\'"], session('info')) !!}';
    document.getElementById('successModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(function() {
        var el = document.getElementById('register');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 800);
});
@endif
</script>

<style>
  @keyframes fadeInUp { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }

  /* ── Mobile: hero fills viewport, content starts below fold naturally ── */
  .br-mobile { display: none; }
  @media (max-width: 767px) {
    .hero {
      min-height: 100dvh;
      overflow: hidden;
    }
    .br-mobile { display: block; }
  }
</style>

</body>
</html>
