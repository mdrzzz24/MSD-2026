<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Metrodata Solution Day 2026 — Winning with AI</title>
<meta name="description" content="MSD 2026: Winning with AI — Build, Run, and Scale for Measurable Impact. Jakarta, 20 August 2026, Shangri-La Hotel." />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
/* ============================================================
   home3 — AI FUTURISTIC Theme
   ============================================================ */

:root {
  --section-gap: 120px;
  --ai-cyan: #00d4ff;
  --ai-purple: #a855f7;
  --ai-glow: rgba(0,212,255,.15);
}

/* ── Hero: Full background.png ── */
.hero {
  background: url('{{ asset('img/background.png') }}') no-repeat center center;
  background-size: cover;
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  color: #fff;
  padding: 120px 0 80px;
  overflow: hidden;
}
/* Dark overlay for readability */
.hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(5,13,42,.8), rgba(10,26,74,.6), rgba(5,13,42,.85));
  z-index: 1;
}
.hero > * { position: relative; z-index: 2; }
/* Animated grid overlay */
.hero::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(0,212,255,.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,212,255,.03) 1px, transparent 1px);
  background-size: 60px 60px;
  z-index: 1;
  animation: gridPulse 8s ease-in-out infinite;
}
@keyframes gridPulse {
  0%,100% { opacity: .3; }
  50% { opacity: .6; }
}
.hero-content { text-align: center; max-width: 960px; margin: 0 auto; position: relative; z-index: 3; }

/* ── Floating AI particles ── */
.ai-particle {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  z-index: 2;
}
.ai-particle:nth-child(1) {
  top: 15%; left: 8%;
  width: 6px; height: 6px;
  background: var(--ai-cyan);
  box-shadow: 0 0 12px var(--ai-cyan);
  animation: floatAI 6s ease-in-out infinite;
}
.ai-particle:nth-child(2) {
  top: 25%; right: 12%;
  width: 4px; height: 4px;
  background: var(--ai-purple);
  box-shadow: 0 0 10px var(--ai-purple);
  animation: floatAI 8s ease-in-out infinite 1s;
}
.ai-particle:nth-child(3) {
  bottom: 30%; left: 15%;
  width: 8px; height: 8px;
  background: var(--ai-cyan);
  box-shadow: 0 0 16px var(--ai-cyan);
  animation: floatAI 7s ease-in-out infinite 2s;
}
.ai-particle:nth-child(4) {
  bottom: 20%; right: 20%;
  width: 5px; height: 5px;
  background: var(--ai-purple);
  box-shadow: 0 0 12px var(--ai-purple);
  animation: floatAI 9s ease-in-out infinite 0.5s;
}
.ai-particle:nth-child(5) {
  top: 50%; left: 5%;
  width: 3px; height: 3px;
  background: #fff;
  box-shadow: 0 0 8px #fff;
  animation: floatAI 5s ease-in-out infinite 3s;
}
@keyframes floatAI {
  0%,100% { transform: translateY(0) scale(1); opacity: .6; }
  50% { transform: translateY(-30px) scale(1.5); opacity: 1; }
}

/* ── Hero logo + badge ── */
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 20px;
  border: 1px solid rgba(0,212,255,.3);
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: var(--ai-cyan);
  background: rgba(0,212,255,.06);
  backdrop-filter: blur(4px);
  margin-bottom: 24px;
}
.hero-badge .dot-live {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--ai-cyan);
  animation: pulseLive 1.5s ease-in-out infinite;
}
@keyframes pulseLive {
  0%,100% { opacity: 1; box-shadow: 0 0 6px var(--ai-cyan); }
  50% { opacity: .3; box-shadow: 0 0 0 var(--ai-cyan); }
}
.hero-logo-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}
.hero-logo-row img { height: clamp(48px,8vw,80px); width: auto; }
.hero h1 {
  font-size: clamp(28px,4vw,52px);
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -.03em;
  margin: 0 auto 12px;
  max-width: 800px;
  background: linear-gradient(135deg, #fff 50%, var(--ai-cyan) 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.hero .tagline {
  font-size: clamp(15px,1.3vw,20px);
  color: rgba(255,255,255,.7);
  font-weight: 400;
  margin-bottom: 28px;
  letter-spacing: .02em;
}
.hero-meta {
  display: flex;
  justify-content: center;
  gap: 28px;
  flex-wrap: wrap;
  font-size: 14px;
  margin-bottom: 32px;
  color: rgba(255,255,255,.8);
}
.hero-meta svg { width: 18px; height: 18px; stroke: var(--ai-cyan); fill: none; stroke-width: 2; }

/* ── AI-themed button ── */
.btn-ai {
  display: inline-block;
  padding: 16px 44px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: .03em;
  background: linear-gradient(135deg, var(--ai-cyan), #0891b2);
  color: #0a1a4a;
  border: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: transform .3s, box-shadow .3s;
}
.btn-ai:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(0,212,255,.3);
}
.btn-ai::before {
  content: '';
  position: absolute;
  top: -50%; left: -50%;
  width: 200%; height: 200%;
  background: conic-gradient(from 0deg, transparent, rgba(255,255,255,.2), transparent, rgba(255,255,255,.2), transparent);
  animation: btnRotate 4s linear infinite;
  opacity: 0;
  transition: opacity .3s;
}
.btn-ai:hover::before { opacity: 1; }
@keyframes btnRotate { to { transform: rotate(360deg); } }

/* ── Animations ── */
@keyframes fadeUpAI {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes scaleAI {
  from { opacity: 0; transform: scale(.7); }
  to { opacity: 1; transform: scale(1); }
}
@keyframes slideAI {
  from { opacity: 0; transform: translateX(-40px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes glowAI {
  0%,100% { filter: drop-shadow(0 0 8px rgba(0,212,255,.2)); }
  50% { filter: drop-shadow(0 0 20px rgba(0,212,255,.5)); }
}

.hero-badge { animation: fadeUpAI .7s ease both; }
.hero-logo-row { animation: scaleAI .8s cubic-bezier(.16,1,.3,1) .15s both; }
.hero h1 { animation: fadeUpAI .8s ease .3s both; }
.hero .tagline { animation: fadeUpAI .8s ease .45s both; }
.hero-meta { animation: fadeUpAI .8s ease .6s both; }
.btn-ai { animation: scaleAI .6s cubic-bezier(.16,1,.3,1) .75s both; }

/* ── Scroll reveals ── */
.reveal-up { opacity: 0; transform: translateY(40px); transition: opacity .8s ease, transform .8s cubic-bezier(.16,1,.3,1); }
.reveal-up.visible { opacity: 1; transform: translateY(0); }
.reveal-scale { opacity: 0; transform: scale(.85); transition: opacity .7s ease, transform .7s cubic-bezier(.16,1,.3,1); }
.reveal-scale.visible { opacity: 1; transform: scale(1); }
.reveal-side { opacity: 0; transform: translateX(-30px); transition: opacity .7s ease, transform .7s cubic-bezier(.16,1,.3,1); }
.reveal-side.visible { opacity: 1; transform: translateX(0); }
.reveal-side-r { opacity: 0; transform: translateX(30px); transition: opacity .7s ease, transform .7s cubic-bezier(.16,1,.3,1); }
.reveal-side-r.visible { opacity: 1; transform: translateX(0); }

.stat-stagger { opacity: 0; transform: translateY(25px); transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1); }
.stat-stagger.visible { opacity: 1; transform: translateY(0); }
.feat-card { opacity: 0; transform: translateY(20px); transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1); }
.feat-card.visible { opacity: 1; transform: translateY(0); }
.why-stagger { opacity: 0; transform: translateX(-20px); transition: opacity .5s ease, transform .5s cubic-bezier(.16,1,.3,1); }
.why-stagger:nth-child(even) { transform: translateX(20px); }
.why-stagger.visible { opacity: 1; transform: translateX(0); }
.agenda-row { opacity: 0; transform: translateY(10px); transition: opacity .35s ease, transform .35s ease; }
.agenda-row.visible { opacity: 1; transform: translateY(0); }
.sponsor-card { opacity: 0; transform: scale(.85); transition: opacity .4s ease, transform .4s cubic-bezier(.16,1,.3,1); }
.sponsor-card.visible { opacity: 1; transform: scale(1); }

/* ── Eyebrow ── */
.section-eyebrow {
  position: relative; display: inline-block;
  color: var(--ai-cyan) !important;
}
.section-eyebrow::after {
  content: ''; position: absolute; bottom: -4px; left: 0;
  width: 0; height: 2px;
  background: linear-gradient(90deg, var(--ai-cyan), transparent);
  transition: width .7s cubic-bezier(.16,1,.3,1);
}
.section-eyebrow.reveal-eyebrow::after { width: 100%; }

/* ══════════════════════════════════
   AI NEURAL SECTIONS
   ══════════════════════════════════ */
section { padding: var(--section-gap) 0; }

/* ── OVERVIEW: AI chip card ── */
#overview .two-col { align-items: center; }
.ai-chip {
  background: linear-gradient(145deg, rgba(0,212,255,.06), rgba(168,85,247,.04));
  border: 1px solid rgba(0,212,255,.1);
  border-radius: 20px;
  padding: 32px;
  position: relative;
  overflow: hidden;
}
.ai-chip::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--ai-cyan), var(--ai-purple), transparent);
}
#overview .section-title { font-size: clamp(28px,3.5vw,44px); line-height: 1.2; letter-spacing: -.02em; }
#overview .section-lead { font-size: 17px; line-height: 1.8; color: rgba(255,255,255,.7); }

/* ── STATS: Neural nodes ── */
#statsGrid3 {
  display: grid; grid-template-columns: repeat(5,1fr); gap: 12px; margin-top: 48px;
}
#statsGrid3 .stat {
  background: linear-gradient(145deg, rgba(0,212,255,.05), rgba(168,85,247,.03));
  border: 1px solid rgba(0,212,255,.08);
  border-radius: 16px;
  text-align: center;
  padding: 28px 16px;
  position: relative;
  transition: border-color .3s, box-shadow .3s;
}
#statsGrid3 .stat:hover {
  border-color: var(--ai-cyan);
  box-shadow: 0 0 30px rgba(0,212,255,.08);
}
#statsGrid3 .stat .num {
  font-size: clamp(40px,4.5vw,56px);
  font-weight: 900;
  background: linear-gradient(135deg, #fff, var(--ai-cyan));
  -webkit-background-clip: text; background-clip: text;
  color: transparent;
  line-height: 1;
}
#statsGrid3 .stat .lbl {
  font-size: 12px; color: rgba(255,255,255,.45);
  text-transform: uppercase; letter-spacing: .12em;
  margin-top: 6px; font-weight: 600;
}

/* ── FEATURES: Neural grid ── */
.feat-grid {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 16px;
  margin-top: 48px;
}
.feat-card {
  background: linear-gradient(145deg, rgba(255,255,255,.04), rgba(0,212,255,.02));
  border: 1px solid rgba(0,212,255,.06);
  border-radius: 14px;
  padding: 28px 24px;
  text-align: center;
  transition: border-color .3s, transform .3s, box-shadow .3s;
  position: relative;
  overflow: hidden;
}
.feat-card:hover {
  border-color: var(--ai-cyan);
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0,212,255,.08);
}
.feat-card .feat-icon {
  width: 48px; height: 48px;
  margin: 0 auto 14px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(0,212,255,.08);
  border-radius: 12px;
  border: 1px solid rgba(0,212,255,.1);
}
.feat-card .feat-icon svg {
  width: 24px; height: 24px;
  color: var(--ai-cyan);
}
.feat-card h4 { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 6px; }
.feat-card p { font-size: 14px; color: rgba(255,255,255,.55); line-height: 1.6; }

/* ── WHY: Neural network nodes ── */
.why-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-top: 40px;
}
.why-item-3 {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  padding: 22px 24px;
  background: linear-gradient(135deg, rgba(0,212,255,.04), rgba(168,85,247,.02));
  border: 1px solid rgba(0,212,255,.06);
  border-radius: 12px;
  transition: border-color .3s, transform .3s;
}
.why-item-3:hover {
  border-color: var(--ai-cyan);
  transform: translateX(4px);
}
.why-item-3 .dot-3 {
  flex: none;
  width: 36px; height: 36px;
  background: linear-gradient(135deg, var(--ai-cyan), var(--ai-purple));
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 800; color: #0a1a4a;
}
.why-item-3 h5 { font-size: 15px; font-weight: 600; color: #fff; line-height: 1.4; }

/* ── ABOUT: AI brain card ── */
.ai-brain {
  background: linear-gradient(145deg, rgba(0,212,255,.04), rgba(168,85,247,.03));
  border: 1px solid rgba(0,212,255,.08);
  border-radius: 24px;
  padding: 56px 48px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.ai-brain::before {
  content: '';
  position: absolute;
  top: -50%; left: -50%;
  width: 200%; height: 200%;
  background: radial-gradient(circle at 30% 50%, rgba(0,212,255,.04), transparent 50%),
              radial-gradient(circle at 70% 50%, rgba(168,85,247,.04), transparent 50%);
  animation: brainPulse 8s ease-in-out infinite;
}
@keyframes brainPulse {
  0%,100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}
@keyframes fadeInUp {
  from { opacity:0; transform:scale(0.9); }
  to { opacity:1; transform:scale(1); }
}
.ai-brain > * { position: relative; z-index: 1; }
.ai-brain .section-title { font-size: clamp(24px,3vw,36px); color: #fff; max-width: 800px; margin: 0 auto 20px; }
.ai-brain p { color: rgba(255,255,255,.6); font-size: 16px; line-height: 1.8; max-width: 720px; margin: 0 auto; }
.ai-brain strong { color: var(--ai-cyan); }

/* ── AGENDA: Data table ── */
#agenda .table-wrap {
  background: linear-gradient(145deg, rgba(0,212,255,.03), rgba(168,85,247,.02));
  border: 1px solid rgba(0,212,255,.08);
  border-radius: 14px;
}
#agenda table th {
  background: rgba(5,13,42,.8);
  color: var(--ai-cyan);
  font-weight: 600; font-size: 11px;
  letter-spacing: .08em; text-transform: uppercase;
  padding: 14px 10px;
  border-bottom: 1px solid rgba(0,212,255,.1);
}
#agenda table td {
  border-bottom: 1px solid rgba(255,255,255,.03);
  padding: 10px; font-size: 13px;
}
#agenda td.time { font-weight: 700; color: var(--ai-cyan); font-size: 12px; }
#agenda tbody tr:hover { background: rgba(0,212,255,.04) !important; }

/* ── SPONSORS: Neural badges ── */
.sponsor-grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 8px;
}
.sponsor-card {
  display: flex;
  align-items: center; justify-content: center;
  padding: 14px 10px;
  background: rgba(0,212,255,.03);
  border: 1px solid rgba(0,212,255,.06);
  border-radius: 8px;
  font-weight: 500; font-size: 12px;
  color: rgba(255,255,255,.6);
  transition: all .3s;
}
.sponsor-card:hover {
  border-color: var(--ai-cyan);
  color: #fff;
  background: rgba(0,212,255,.08);
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,212,255,.06);
}
.sponsor-tier-3 {
  font-size: 11px; font-weight: 700; letter-spacing: .18em;
  text-transform: uppercase; color: var(--ai-cyan); margin-bottom: 12px;
}
.sponsors-block-3 + .sponsors-block-3 { margin-top: 32px; }

/* ── REGISTER: Holographic form ── */
#register .form-wrap {
  display: grid;
  grid-template-columns: 1.3fr 1fr;
  gap: 40px;
  align-items: start;
  background: linear-gradient(145deg, rgba(0,212,255,.04), rgba(168,85,247,.02));
  border: 1px solid rgba(0,212,255,.08);
  border-radius: 20px;
  padding: 40px;
}
#register .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
#register .field { gap: 4px; }
#register label {
  font-size: 11px; font-weight: 600; letter-spacing: .06em;
  color: rgba(255,255,255,.5);
}
#register input, #register select {
  background: rgba(0,212,255,.04);
  border: 1px solid rgba(0,212,255,.08);
  border-radius: 8px;
  padding: 12px 14px;
  font-size: 14px; color: #fff;
  transition: all .25s;
}
#register input:focus, #register select:focus {
  border-color: var(--ai-cyan);
  background: rgba(0,212,255,.06);
  box-shadow: 0 0 0 3px rgba(0,212,255,.06);
}
#register .btn-ai { width: 100%; text-align: center; }
#register .info {
  background: rgba(0,212,255,.03);
  border: 1px solid rgba(0,212,255,.06);
  border-radius: 12px;
  padding: 24px;
}
#register .info h4 {
  font-size: 12px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; color: var(--ai-cyan); margin-bottom: 14px;
}
#register .info ul { padding-left: 14px; }
#register .info ul li { font-size: 13px; color: rgba(255,255,255,.5); margin-bottom: 8px; line-height: 1.5; }
#register .info ul li::marker { color: var(--ai-cyan); }

/* ── FOOTER ── */
footer {
  padding: 40px 0;
  text-align: center;
  font-size: 13px;
  color: rgba(255,255,255,.35);
  border-top: 1px solid rgba(0,212,255,.06);
}

/* ── Responsive ── */
@media (max-width: 900px) {
  :root { --section-gap: 70px; }
  #statsGrid3 { grid-template-columns: repeat(2,1fr); }
  .feat-grid { grid-template-columns: 1fr; }
  .why-grid { grid-template-columns: 1fr; }
  #register .form-wrap { grid-template-columns: 1fr; padding: 24px; }
  #register .form-grid { grid-template-columns: 1fr; }
  .ai-brain { padding: 32px 24px; }
}
/* ── Field errors ── */
.field-err { display:block; font-size:11px; color:#ef4444; margin-top:2px; min-height:0; }
.field-err:empty { display:none; }
input.field-error, select.field-error { border-color:#ef4444 !important; }
</style>
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
  {{-- AI floating particles --}}
  <div class="ai-particle"></div>
  <div class="ai-particle"></div>
  <div class="ai-particle"></div>
  <div class="ai-particle"></div>
  <div class="ai-particle"></div>
  <div class="container hero-content">
    <div class="hero-badge"><span class="dot-live"></span> Metrodata Proudly Present</div>
    <div class="hero-logo-row">
      <img src="{{ asset('img/logo-msd.svg') }}" alt="MSD" style="height:clamp(50px,9vw,90px);width:auto">
    </div>
    <h1>Winning with AI: Build, Run, and Scale for Measurable Impact</h1>
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
    <a href="#register" class="btn-ai">Register Now →</a>
  </div>
</header>

<!-- OVERVIEW -->
<section id="overview" class="reveal-up">
  <div class="container">
    <div class="two-col">
      <div class="reveal-side">
        <div class="ai-chip">
          <h2 class="section-title">Winning with AI: Build, Run, and Scale for Measurable Impact</h2>
          <p style="color:var(--ai-cyan);font-weight:600">Accelerating AI for Real Business and Operational Value</p>
        </div>
      </div>
      <div class="reveal-side-r" style="transition-delay:0.15s">
        <p class="section-lead">
          AI has entered a new era. In 2026, the conversation is no longer about what AI can do,
          but about how organizations can scale AI to create lasting business value. The challenge
          has shifted from isolated pilots to enterprise-wide adoption, where success depends on
          trusted data, secure and governed platforms, and an integrated technology ecosystem.
        </p>
      </div>
    </div>

    <p class="section-eyebrow reveal-eyebrow" style="margin-top:80px">What to Expect</p>
    <h3 class="section-title" style="font-size:28px">A melting pot of innovators and gamechangers</h3>

    <div class="stats-grid" id="statsGrid3">
      <div class="stat stat-stagger"><div class="num">9</div><div class="lbl">Keynotes</div></div>
      <div class="stat stat-stagger"><div class="num">9</div><div class="lbl">Workshops</div></div>
      <div class="stat stat-stagger"><div class="num">32</div><div class="lbl">Sessions</div></div>
      <div class="stat stat-stagger"><div class="num">32</div><div class="lbl">Exhibits</div></div>
      <div class="stat stat-stagger"><div class="num">1,200+</div><div class="lbl">Professionals</div></div>
    </div>

        <div class="feat-grid" id="featGrid">
      <div class="feat-card" style="transition-delay:0s">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 18h6"/>
            <path d="M10 22h4"/>
            <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/>
          </svg>
        </div>
        <h4>Visionary Keynotes</h4>
        <p>From global technology leaders, industry experts, and national stakeholders.</p>
      </div>
      <div class="feat-card" style="transition-delay:0.08s">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
            <circle cx="12" cy="10" r="2"/>
            <path d="M10 10H6"/>
            <path d="M18 10h-4"/>
          </svg>
        </div>
        <h4>Tech Sessions</h4>
        <p>Featuring real-world case studies and best practices.</p>
      </div>
      <div class="feat-card" style="transition-delay:0.16s">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
          </svg>
        </div>
        <h4>Interactive Workshops</h4>
        <p>Hands-on learning from technology experts.</p>
      </div>
      <div class="feat-card" style="transition-delay:0.24s">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
            <path d="M8 9l2 2 4-4"/>
          </svg>
        </div>
        <h4>Innovation Exhibits</h4>
        <p>Showcasing the latest enterprise technologies.</p>
      </div>
      <div class="feat-card" style="transition-delay:0.32s">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="18" cy="5" r="3"/>
            <circle cx="6" cy="12" r="3"/>
            <circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
          </svg>
        </div>
        <h4>Strategic Networking</h4>
        <p>Connect with industry leaders and partners.</p>
      </div>
      <div class="feat-card" style="transition-delay:0.40s">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
        </div>
        <h4>Exclusive Tech Offers</h4>
        <p>Available only during the event.</p>
      </div>
    </div></div>
  </div>
</section>

<!-- WHY ATTEND -->
<section class="reveal-up">
  <div class="container">
    <p class="section-eyebrow reveal-eyebrow">Why Should You Attend</p>
    <h2 class="section-title">Insights, connections, and solutions for your transformation journey</h2>
    <p class="section-lead" style="margin-bottom:0">
      Whether you are defining business strategy, accelerating AI adoption, or modernizing enterprise
      technology, MSD 2026 offers valuable insights, meaningful connections, and practical solutions.
    </p>
    <div class="why-grid">
      <div class="why-item-3 why-stagger" style="transition-delay:0s"><div class="dot-3">1</div><h5>C-Level Executives &amp; Business Strategists</h5></div>
      <div class="why-item-3 why-stagger" style="transition-delay:0.08s"><div class="dot-3">2</div><h5>CIOs, CTOs &amp; Digital Transformation Leaders</h5></div>
      <div class="why-item-3 why-stagger" style="transition-delay:0.16s"><div class="dot-3">3</div><h5>Business Executives &amp; Decision Makers</h5></div>
      <div class="why-item-3 why-stagger" style="transition-delay:0.24s"><div class="dot-3">4</div><h5>IT Professionals, Architects &amp; Developers</h5></div>
      <div class="why-item-3 why-stagger" style="transition-delay:0.32s"><div class="dot-3">5</div><h5>Government Officials, Regulators &amp; Academia</h5></div>
      <div class="why-item-3 why-stagger" style="transition-delay:0.40s"><div class="dot-3">6</div><h5>SMEs, Startups &amp; Innovation Leaders</h5></div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section class="reveal-scale">
  <div class="container">
    <div class="ai-brain">
      <p class="section-eyebrow reveal-eyebrow">About</p>
      <h2 class="section-title" style="margin-top:8px">Metrodata Solution Day</h2>
      <p>
        Metrodata Solution Day (MSD) is Metrodata Group's flagship thought leadership platform where business and technology
        leaders come together to shape Indonesia's digital future.
      </p>
      <p style="margin-top:20px">
        Celebrating its <strong>21st edition</strong>, MSD explores AI, digital
        transformation, and emerging technologies through visionary keynotes, executive discussions,
        innovation showcases, and expert-led sessions — empowering organizations to transform innovation
        into measurable business outcomes.
      </p>
    </div>
  </div>
</section>

<!-- AGENDA -->
<section id="agenda" class="reveal-up">
  <div class="container">
    <p class="section-eyebrow reveal-eyebrow">Agenda</p>
    <h2 class="section-title">A full day of learning, exchange, and discovery</h2>
    <div class="table-wrap">
      <table id="agendaTable">
        <thead>
          <tr>
            <th>Time</th><th>Ballroom A</th><th>Ballroom B</th><th>Ballroom C</th>
            <th>Sumatra</th><th>Java</th><th>Sulawesi</th><th>Kalimantan</th><th>Maluku</th>
          </tr>
        </thead>
        <tbody>
          <tr class="agenda-row"><td class="time">08.00 – 08.30</td><td class="full" colspan="8">Registration, Morning Refreshment, Networking & Exhibition</td></tr>
          <tr class="agenda-row"><td class="time">08.30 – 10.30</td><td class="full" colspan="8">General Sessions</td></tr>
          <tr class="agenda-row"><td class="time">10.30 – 12.00</td><td>—</td><td>—</td><td>—</td><td><span class="tag ws">Workshop A1</span></td><td><span class="tag ws">Workshop B1</span></td><td><span class="tag ws">Workshop C1</span></td><td>—</td><td>—</td></tr>
          <tr class="agenda-row"><td class="time">12.00 – 13.00</td><td class="full" colspan="8">Lunch, Networking & Exhibition</td></tr>
          <tr class="agenda-row"><td class="time">13.00 – 13.30</td><td><span class="tag plat">Platinum 1</span></td><td><span class="tag plat">Platinum 4</span></td><td><span class="tag plat">Platinum 7</span></td><td><span class="tag ws">Workshop A2</span></td><td><span class="tag ws">Workshop B2</span></td><td><span class="tag ws">Workshop C2</span></td><td><span class="tag gold">Gold A1</span></td><td><span class="tag gold">Gold B1</span></td></tr>
          <tr class="agenda-row"><td class="time">13.35 – 14.05</td><td><span class="tag plat">Platinum 2</span></td><td><span class="tag plat">Platinum 5</span></td><td><span class="tag plat">Platinum 7</span></td><td>—</td><td>—</td><td>—</td><td><span class="tag gold">Gold A2</span></td><td><span class="tag gold">Gold B2</span></td></tr>
          <tr class="agenda-row"><td class="time">14.10 – 14.40</td><td><span class="tag plat">Platinum 3</span></td><td><span class="tag plat">Platinum 6</span></td><td><span class="tag plat">Platinum 9</span></td><td>—</td><td>—</td><td>—</td><td><span class="tag gold">Gold A3</span></td><td><span class="tag gold">Gold B3</span></td></tr>
          <tr class="agenda-row"><td class="time">14.40 – 15.00</td><td class="full" colspan="8">Break Session, Exhibition Booths</td></tr>
          <tr class="agenda-row"><td class="time">15.00 – 15.30</td><td><span class="tag plat">Platinum 4</span></td><td><span class="tag plat">Platinum 1</span></td><td><span class="tag plat">Platinum 8</span></td><td><span class="tag ws">Workshop A3</span></td><td><span class="tag ws">Workshop B3</span></td><td><span class="tag ws">Workshop C3</span></td><td><span class="tag gold">Gold C1</span></td><td><span class="tag gold">Gold D1</span></td></tr>
          <tr class="agenda-row"><td class="time">15.35 – 16.05</td><td><span class="tag plat">Platinum 5</span></td><td><span class="tag plat">Platinum 2</span></td><td><span class="tag plat">Platinum 9</span></td><td>—</td><td>—</td><td>—</td><td><span class="tag gold">Gold C2</span></td><td><span class="tag gold">Gold D2</span></td></tr>
          <tr class="agenda-row"><td class="time">16.05 – 16.35</td><td><span class="tag plat">Platinum 6</span></td><td><span class="tag plat">Platinum 3</span></td><td><span class="tag plat">Platinum 8</span></td><td>—</td><td>—</td><td>—</td><td><span class="tag gold">Gold C3</span></td><td><span class="tag gold">Gold D3</span></td></tr>
          <tr class="agenda-row"><td class="time">16.30 – 17.00</td><td>—</td><td>—</td><td>—</td><td>—</td><td>—</td><td>—</td><td><span class="tag gold">Gold C4</span></td><td><span class="tag gold">Gold D4</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- SPONSORS -->
<section id="sponsors" class="reveal-scale">
  <div class="container">
    <p class="section-eyebrow reveal-eyebrow">Sponsors</p>
    <h2 class="section-title">Our trusted technology partners</h2>

    <div class="sponsors-block-3" style="margin-top:48px">
      <div class="sponsor-tier-3">Platinum</div>
      <div class="sponsor-grid-3" id="sponsorGrid3">
        <div class="sponsor-card">Anaplan</div><div class="sponsor-card">AWS</div><div class="sponsor-card">Cloudera</div>
        <div class="sponsor-card">Google Cloud</div><div class="sponsor-card">IBM</div><div class="sponsor-card">Microsoft</div>
        <div class="sponsor-card">Palo Alto</div><div class="sponsor-card">Red Hat</div><div class="sponsor-card">Salesforce</div>
      </div>
    </div>

    <div class="sponsors-block-3">
      <div class="sponsor-tier-3">Gold</div>
      <div class="sponsor-grid-3">
        <div class="sponsor-card">Byteplus</div><div class="sponsor-card">Confluent</div><div class="sponsor-card">Cyble</div>
        <div class="sponsor-card">Datadog</div><div class="sponsor-card">Dynatrace</div><div class="sponsor-card">EDB Postgres</div>
        <div class="sponsor-card">Fortinet</div><div class="sponsor-card">HPE</div><div class="sponsor-card">HP Inc</div>
        <div class="sponsor-card">Huawei Cloud</div><div class="sponsor-card">KONG</div><div class="sponsor-card">Lark</div>
        <div class="sponsor-card">Proofpoint</div><div class="sponsor-card">Tenable</div>
      </div>
    </div>

    <div class="sponsors-block-3">
      <div class="sponsor-tier-3">Workshop</div>
      <div class="sponsor-grid-3">
        <div class="sponsor-card">Alicloud</div><div class="sponsor-card">Cloudflare</div><div class="sponsor-card">Confluent</div>
        <div class="sponsor-card">Creatio</div><div class="sponsor-card">Google Cloud</div><div class="sponsor-card">NetApp</div>
        <div class="sponsor-card">Red Hat</div><div class="sponsor-card">Sangfor</div><div class="sponsor-card">SingleStore</div>
      </div>
    </div>

    <div class="sponsors-block-3">
      <div class="sponsor-tier-3">Proud Collaborators</div>
      <div class="sponsor-grid-3">
        <div class="sponsor-card">Metrodata Electronics</div><div class="sponsor-card">MII</div>
        <div class="sponsor-card">SMI</div><div class="sponsor-card">Soltius</div>
        <div class="sponsor-card">FMI</div><div class="sponsor-card">Sinergi</div>
        <div class="sponsor-card">MIT</div><div class="sponsor-card">CMI</div>
      </div>
    </div>
  </div>
</section>

<!-- REGISTER -->
<section id="register" class="reveal-up">
  <div class="container">
    <p class="section-eyebrow reveal-eyebrow">Register</p>
    <h2 class="section-title">Registration Form</h2>
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
            <option>Banking & Finance</option><option>Government</option>
            <option>Manufacturing</option><option>Retail</option>
            <option>Telco</option><option>Other</option>
          </select>
          <span class="field-err" data-field="industry"></span>
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
            <span>By submitting this form, I understand Metrodata will process my personal information in accordance with their <strong>Privacy Notice</strong>. Additionally, I consent to my information being shared with <strong>Event Partners</strong> in accordance. I understand I may withdraw my consent or update my information at any time.</span>
          </label>
        </div>
        <div class="field full" style="margin-top:4px">
          <label class="checkbox-label">
            <input type="checkbox" name="attended_before" value="1">
            <span>I have attended Metrodata Solution Day <strong>before</strong>.</span>
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
          <button type="submit" class="btn-ai">Submit Registration →</button>
        </div>
      </form>
      <aside class="info" style="margin-top:0">
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
      <p style="font-size:14px; color:#6b7280; margin-bottom:12px;">Your data has been received. Please wait for confirmation from the admin via email.</p>
      <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px; font-size:14px; color:#d97706; margin-bottom:24px; text-align:left;">
        <strong>📧 Check your email after approval</strong><br>
        Once your registration is <strong>approved</strong>, you will receive an email containing your login <strong>password</strong> to access the participant dashboard at <strong style="word-break:break-all;">{{ request()->getSchemeAndHttpHost() }}/registrant/login</strong>.
      </div>
      <button onclick="closeSuccessModal()" style="width:100%; padding:10px 0; background:#4f46e5; color:#fff; font-weight:600; font-size:14px; border:none; border-radius:12px; cursor:pointer; transition:background 0.2s;"
              onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
        Close
      </button>
    </div>
  </div>
</div>

<script src="{{ asset('js/main3.js') }}"></script>
</body>
</html>
