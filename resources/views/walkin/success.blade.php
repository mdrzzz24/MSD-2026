<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Received — Metrodata Solution Day 2026</title>
    <meta name="description" content="Your walk-in registration for Metrodata Solution Day 2026 has been received." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('img/Website-BG.jpg?v2') }}" fetchpriority="high">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=15">
    <style>
        .hero-compact{padding-bottom:0}
        .walkin-wrap{max-width:720px;margin:0 auto}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    </style>
</head>
<body>
<header class="hero hero-compact" id="top">
  <div class="hero-light hero-light--blue"></div>
  <div class="container hero-content">
    <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" style="height:clamp(56px,9vw,88px);width:auto">
    <h1>Winning with AI:<br>Build, Run, and Scale for Measurable Impact</h1>
  </div>
</header>

<section class="why reveal">
  <div class="container">
    <div class="walkin-wrap">
      <div class="form-wrap" id="walkinSuccessContent" style="margin-top:0;text-align:center;display:block;opacity:0;transform:translateY(10px);transition:opacity .5s ease,transform .5s ease">
        <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(251,191,36,.12);color:#fbbf24;border:1px solid rgba(251,191,36,.25);margin-bottom:18px">
          <svg style="width:16px;height:16px" fill="none" stroke="#fbbf24" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
          Pending Approval
        </div>
        <h2 style="color:#fff;font-size:clamp(22px,3vw,28px);font-weight:800;margin-bottom:12px">Registration Received!</h2>
        <p style="color:var(--muted);font-size:14.5px;line-height:1.7;max-width:460px;margin:0 auto 24px">You have filled in the walk-in registration form. Your registration is <strong style="color:#fbbf24">Pending</strong> approval — our team will confirm it and issue your badge.</p>
        <div style="background:rgba(255,255,255,.06);border:1px solid var(--line);border-radius:12px;padding:18px 20px;max-width:420px;margin:0 auto 24px;text-align:left;font-size:14px">
          <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Name</span><strong style="color:#fff">{{ $registrant->name }}</strong></div>
          <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Email</span><span style="color:#fff">{{ $registrant->email }}</span></div>
          <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Unique Code</span><span style="color:#fbbf24;font-family:monospace;font-weight:600">{{ $registrant->unique_code }}</span></div>
        </div>
        <a href="{{ route('home1') }}" class="btn">Back to Event Website</a>
      </div>

      <footer>
        <p><strong>Metrodata Solution Day 2026</strong> — Winning with AI · Jakarta, 20 August 2026 · Shangri-La Hotel</p>
      </footer>
    </div>
  </div>
</section>

{{-- Success notification modal --}}
<div id="walkinSuccessModal" style="display:flex;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(5,13,42,.78);backdrop-filter:blur(10px);padding:16px;">
  <div style="background:rgba(255,255,255,.06);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.08);width:100%;max-width:420px;overflow:hidden;animation:fadeInUp .35s ease-out;text-align:center;padding:36px 32px 28px;">
    <div style="width:72px;height:72px;background:rgba(16,185,129,.15);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 22px;border:1px solid rgba(16,185,129,.2);">
      <svg style="width:34px;height:34px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h2 style="font-size:19px;font-weight:700;color:#e2e8f0;margin-bottom:6px;letter-spacing:-.01em;">Registration Successful</h2>
    <p style="font-size:14px;color:#94a3b8;line-height:1.6;margin-bottom:24px;">Thank you, <strong style="color:#fff">{{ $registrant->name }}</strong>! Your walk-in registration form has been submitted successfully.</p>
    <button onclick="closeWalkinSuccess()" style="width:100%;padding:12px 0;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;font-weight:700;font-size:14px;letter-spacing:.03em;border:none;border-radius:999px;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,.35);transition:transform .25s,box-shadow .25s;"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,.35)'">Close</button>
  </div>
</div>

<script>
// Lock scrolling while the notification is up, then reveal the details on close.
document.addEventListener('DOMContentLoaded', function () {
    document.body.style.overflow = 'hidden';
});

function closeWalkinSuccess() {
    var modal = document.getElementById('walkinSuccessModal');
    modal.style.display = 'none';
    var content = document.getElementById('walkinSuccessContent');
    content.style.opacity = '1';
    content.style.transform = 'translateY(0)';
    document.body.style.overflow = '';
}
</script>
</body>
</html>
