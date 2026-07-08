<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Metrodata Solution Day 2026 — Winning with AI</title>
<meta name="description" content="MSD 2026: Winning with AI — Build, Run, and Scale for Measurable Impact. Jakarta, 20 August 2026, Shangri-La Hotel." />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v=2">
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <div class="container nav-inner">
    <a href="#" class="logo">
      <img src="{{ asset('img/logo-metrodata.png') }}" alt="Metrodata" style="height:48px;width:auto">
    </a>
    <button class="nav-toggle" aria-label="Menu" id="navToggle">☰</button>
    <div class="nav-links" id="navLinks">
      <a href="#overview" class="active">Overview</a>
      <a href="#agenda">Agenda</a>
      <a href="#sponsors">Sponsors</a>
      <a href="#register">Register</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="hero" id="top">
  <div class="hero-icon hero-icon--top-right">
    <img src="{{ asset('img/ICON 2.png') }}" alt="">
  </div>

  <div class="hero-icon hero-icon--bottom-right">
    <img src="{{ asset('img/ICON 1.png') }}" alt="">
  </div>
  <div class="hero-icon hero-icon--bottom-left">
    <img src="{{ asset('img/ICON 4.png') }}" alt="">
  </div>
  <div class="hero-light hero-light--blue"></div>
  <div class="hero-light hero-light--pink"></div>
  <div class="container hero-content">
    <p class="eyebrow"><strong>Metrodata</strong> Proudly Present</p>
    <div class="hero-title-group">
      <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" class="logo-glow" style="height:clamp(60px,10vw,120px);width:auto">
    </div>
    <h1>Winning with AI:<br>Build, Run, and Scale for Measurable Impact</h1>
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
    <button onclick="openRemindModal()" class="btn">Register Now</button>
  </div>
</header>

<!-- OVERVIEW -->
<section id="overview" class="reveal">
  <div class="section-icon section-icon--top-right">
    <!-- <img src="{{ asset('img/ICON 4.png') }}" alt=""> -->
  </div>
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
  <div class="section-icon section-icon--bottom-right">
    <img src="{{ asset('img/ICON 5.png') }}" alt="">
  </div>
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
  <div class="section-icon section-icon--top-right">
    <img src="{{ asset('img/ICON 6.png') }}" alt="">
  </div>
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

<!-- AGENDA -->
<section id="agenda" class="why reveal">
  <div class="section-icon section-icon--bottom-right">
    <img src="{{ asset('img/ICON 7.png') }}" alt="">
  </div>
  <div class="container">
    <p class="section-eyebrow">Agenda</p>
    <h2 class="section-title">A full day of learning, exchange, and discovery</h2>

    @if(isset($timeSlots) && $timeSlots->isNotEmpty())
      @php
        $roomNames = $rooms->pluck('name')->toArray();
        // Group rooms by floor for dynamic header
        $floorGroups = $rooms->groupBy(fn($r) => $r->floorRelation?->name ?? 'Other');
      @endphp
      <div class="table-wrap">
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
                  <td class="full" colspan="{{ $rooms->count() }}">
                    @if ($fullRow->category)
                      <span class="tag {{ \App\Models\AgendaItem::categoryClass($fullRow->category) }}">{{ $fullRow->title }}</span>
                    @else
                      {{ $fullRow->title }}
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
                            $tag = $item->category
                                ? '<span class="tag ' . \App\Models\AgendaItem::categoryClass($item->category) . '">' . e($item->title) . '</span>'
                                : e($item->title);
                            $cells[] = '<td' . $attrs . '>' . $tag . '</td>';
                        } else {
                            $cells[] = '<td>—</td>';
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
  </div>
</section>

<!-- SPONSORS -->
<section id="sponsors" class="reveal">
  <div class="section-icon section-icon--bottom-left">
    <img src="{{ asset('img/ICON 3.png') }}" alt="">
  </div>
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
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/Creatio.png') }}" alt="Creatio"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/google_cloud.png') }}" alt="Google Cloud"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/NetApp.png') }}" alt="NetApp"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/REHDAT.png') }}" alt="Red Hat"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/SANGFOR.png') }}" alt="Sangfor"></div>
        <div class="sponsor"><img src="{{ asset('img/WORKSHOP/singleStore.png') }}" alt="SingleStore"></div>
      </div>
    </div>
    <div class="sponsors-block">
      <div class="sponsor-tier">Gold</div>
      <div class="sponsor-grid">
        <div class="sponsor"><img src="{{ asset('img/GOLD/BytePlus.png') }}" alt="Byteplus"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Confluent.png') }}" alt="Confluent"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Cyble.png') }}" alt="Cyble"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Datadog.png') }}" alt="Datadog"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Dynatrace.png') }}" alt="Dynatrace"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/EDB.png') }}" alt="EDB Postgres"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Fortinet.png') }}" alt="Fortinet"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/HPE.png') }}" alt="HPE"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/HP.png') }}" alt="HP Inc"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Huawei Cloud.png') }}" alt="Huawei Cloud"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/KONG.png') }}" alt="KONG"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/LARK.png') }}" alt="Lark"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Proofpoint.png') }}" alt="Proofpoint"></div>
        <div class="sponsor"><img src="{{ asset('img/GOLD/Tenable.png') }}" alt="Tenable"></div>
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

<!-- REGISTER -->
<!-- REGISTER -->
<section id="register" class="register reveal">
  <div class="section-icon section-icon--top-left">
    <img src="{{ asset('img/ICON 1.png') }}" alt="">
  </div>
  <div class="container">
    <p class="section-eyebrow">Register</p>
    <h2 class="section-title">Registration Form</h2>
    <!-- <div class="reg-notice">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>Registration is not yet open. Please check back on <strong>July 20, 2026</strong>.</span>
    </div> -->
    <div class="form-wrap">
      <form id="regForm" class="form-grid" method="POST" action="{{ route('register.submit') }}">
        @csrf
        <div class="field"><label>First Name</label><input required name="firstName" placeholder="First Name" /><span class="field-err" data-field="firstName"></span></div>
        <div class="field"><label>Last Name</label><input required name="lastName" placeholder="Last Name" /><span class="field-err" data-field="lastName"></span></div>
        <div class="field"><label>Job Title</label><input required name="title" placeholder="Job Title" /><span class="field-err" data-field="title"></span></div>
        <div class="field"><label>Company Name</label><input required name="company" placeholder="Company Name" /><span class="field-err" data-field="company"></span></div>
        <div class="field"><label>Business Email</label><input required type="email" name="email" placeholder="Business Email" /><span class="field-err" data-field="email"></span></div>
        <div class="field"><label>Mobile Phone</label><input required name="phone" placeholder="Mobile Phone" /><span class="field-err" data-field="phone"></span></div>
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
        <div class="field full" style="margin-top:4px">
          <label>Referral Code (optional)</label>
          <input name="referral_code" placeholder="Enter referral code if you have one">
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
      <!-- <p class="current-time" id="currentTime">--</p> -->
    </div>
        <h4>Registration Information</h4>
        <ul>
          <li>Metrodata Solution Day is <strong>free admission</strong> with mandatory RSVP.</li>
          <li>Access will be granted exclusively to participants who receive a <strong>confirmation email</strong>.</li>
          <li>Please have your confirmation email ready for verification upon arrival.</li>
          <li>Enter a valid <strong>referral code</strong> to receive priority access.</li>
          <li><strong>Workshop participation</strong> requires separate registration due to limited seating.</li>
        </ul>
      </aside>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    © 2026 <strong>Metrodata Solution Day</strong> — Jakarta, 20 August 2026 · Shangri-La Hotel
  </div>
</footer>

{{-- Remind Modal --}}
<div id="remindModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,0.6); backdrop-filter:blur(6px); padding:16px;">
  <div style="background:rgba(10,26,74,.96); border:1px solid rgba(255,255,255,.15); border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.5); width:100%; max-width:420px; animation:fadeInUp 0.3s ease-out;">
    <div style="padding:32px 32px 36px;">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <span style="color:var(--pink); font-weight:700; letter-spacing:.2em; font-size:13px; text-transform:uppercase;">Reminder</span>
        <button onclick="closeRemindModal()" style="background:rgba(255,255,255,.1); border:0; color:#fff; font-size:18px; cursor:pointer; line-height:1; padding:4px 10px; border-radius:8px; transition:background .2s;" 
                onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">&times;</button>
      </div>
      <div style="margin-bottom:28px; text-align:center;">
        <p class="countdown-label" style="font-size:12px;">Registration Opens In</p>
        <div class="countdown">
          <div class="countdown-item">
            <span class="countdown-num" id="remind-days">00</span>
            <span class="countdown-lbl">Days</span>
          </div>
          <div class="countdown-sep">:</div>
          <div class="countdown-item">
            <span class="countdown-num" id="remind-hours">00</span>
            <span class="countdown-lbl">Hrs</span>
          </div>
          <div class="countdown-sep">:</div>
          <div class="countdown-item">
            <span class="countdown-num" id="remind-minutes">00</span>
            <span class="countdown-lbl">Min</span>
          </div>
        </div>
      </div>
      <form id="remindForm" style="display:grid;gap:16px;">
        <div class="field">
          <label>Enter Name</label>
          <input required name="name" placeholder="John Doe">
        </div>
        <div class="field">
          <label>Email Address</label>
          <input required type="email" name="email" placeholder="your@email.com">
        </div>
        <div class="field">
          <label>Company Name</label>
          <input required name="company" placeholder="Your company">
        </div>
        <button type="button" id="remindBtn" class="btn btn-submit" style="margin-top:8px;">Remind me</button>
      </form>
    </div>
  </div>
</div>

<script src="{{ asset('js/main.js') }}"></script>
<script>
function openRemindModal() {
  document.getElementById('remindModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
  tickRemind();
}
function closeRemindModal() {
  document.getElementById('remindModal').style.display = 'none';
  document.body.style.overflow = '';
}
// Remind countdown
function tickRemind() {
  try {
    const dEl = document.getElementById('remind-days');
    const hEl = document.getElementById('remind-hours');
    const mEl = document.getElementById('remind-minutes');
    if (!dEl || !hEl || !mEl) return;
    const target = new Date(2026, 6, 20).getTime();
    const diff = target - Date.now();
    if (diff <= 0) {
      dEl.textContent = '00'; hEl.textContent = '00'; mEl.textContent = '00';
      return;
    }
    dEl.textContent = String(Math.floor(diff / 86400000)).padStart(2,'0');
    hEl.textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2,'0');
    mEl.textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0');
  } catch(e) { /* silent */ }
}
setTimeout(tickRemind, 0);
setInterval(tickRemind, 10000);

// Remind form submit + backdrop close — delegation on modal
document.getElementById('remindModal').addEventListener('click', function(e) {
  // Backdrop click
  if (e.target === this) { closeRemindModal(); return; }
  // Button click
  const btn = e.target.closest('#remindBtn');
  if (!btn) return;
  const form = document.getElementById('remindForm');
  if (!form.querySelector('[name="name"]').value.trim() ||
      !form.querySelector('[name="email"]').value.trim() ||
      !form.querySelector('[name="company"]').value.trim()) return;
  btn.disabled = true;
  btn.textContent = 'Sending...';
  setTimeout(() => {
    closeRemindModal();
    showRemindToast();
    btn.disabled = false;
    btn.textContent = 'Remind me';
    form.reset();
  }, 600);
});

// Toast notification
function showRemindToast() {
  const existing = document.getElementById('remindToast');
  if (existing) existing.remove();
  const toast = document.createElement('div');
  toast.id = 'remindToast';
  toast.innerHTML =
    '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" style="flex:none"><path d="M5 13l4 4L19 7"/></svg>' +
    '<span>Thank you! We\'ll remind you when registration opens.</span>';
  Object.assign(toast.style, {
    position:'fixed', bottom:'32px', left:'50%',
    transform:'translateX(-50%) translateY(20px)',
    opacity:'0',
    zIndex:'99999', display:'flex', alignItems:'center', gap:'12px',
    background:'rgba(5,13,42,.95)', border:'1px solid var(--pink)',
    borderRadius:'14px', padding:'16px 24px',
    boxShadow:'0 12px 40px rgba(0,0,0,0.5)',
    fontSize:'14px', fontWeight:'500', color:'#fff',
    maxWidth:'480px', width:'90%',
    transition:'opacity 0.4s ease-out, transform 0.4s ease-out',
    backdropFilter:'blur(8px)',
  });
  toast.querySelector('svg').style.color = '#22c55e';
  document.body.appendChild(toast);
  // Trigger entrance animation
  setTimeout(() => {
    toast.style.transform = 'translateX(-50%) translateY(0)';
    toast.style.opacity = '1';
  }, 20);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-50%) translateY(20px)';
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}
</script>
<style>
.field-err { display:block; font-size:12px; color:#ef4444; margin-top:2px; min-height:0; }
.field-err:empty { display:none; }
input.field-error, select.field-error { border-color:#ef4444 !important; }
</style>

{{-- Success Modal --}}
<div id="successModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); padding:16px;">
  <div style="background:#fff; border-radius:16px; box-shadow:0 25px 50px rgba(0,0,0,0.25); width:100%; max-width:448px; overflow:hidden; animation:fadeInUp 0.3s ease-out;">
    <div style="padding:32px; text-align:center;">
      <div style="width:64px; height:64px; background:#d1fae5; border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
        <svg style="width:32px; height:32px; color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <h2 style="font-size:20px; font-weight:700; color:#111827; margin-bottom:8px;">Registration Successful!</h2>
      <p style="font-size:14px; color:#6b7280; margin-bottom:20px;">Your data has been received. Please wait for confirmation from the admin via email.</p>
      <button onclick="closeSuccessModal()" style="width:100%; padding:10px 0; background:#4f46e5; color:#fff; font-weight:600; font-size:14px; border:none; border-radius:12px; cursor:pointer; transition:background 0.2s;"
              onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
        Close
      </button>
    </div>
  </div>
</div>

<style>
  @keyframes fadeInUp { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
</style>

</body>
</html>
