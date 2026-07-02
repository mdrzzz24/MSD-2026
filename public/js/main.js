/* ============================================================
   Metrodata Solution Day 2026 — main.js
   ============================================================ */

// Mobile menu toggle
const toggle = document.getElementById('navToggle');
const links = document.getElementById('navLinks');

if (toggle && links) {
  toggle.addEventListener('click', () => links.classList.toggle('open'));
  links.querySelectorAll('a').forEach(a =>
    a.addEventListener('click', () => links.classList.remove('open'))
  );
}

// Active nav link on scroll
const sectionIds = ['overview', 'agenda', 'sponsors', 'register'];
const sections = sectionIds.map(id => document.getElementById(id));
const navAnchors = links ? links.querySelectorAll('a') : [];

window.addEventListener('scroll', () => {
  const y = window.scrollY + 120;
  let current = 'overview';
  sections.forEach(s => { if (s && s.offsetTop <= y) current = s.id; });
  navAnchors.forEach(a =>
    a.classList.toggle('active', a.getAttribute('href') === '#' + current)
  );
});

// Scroll reveal animation — Intersection Observer
const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
);

document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
  .forEach(el => revealObserver.observe(el));

// Counter animation for stats
function animateCounter(el) {
  const target = parseInt(el.textContent.replace(/[,+]/g, ''), 10);
  if (isNaN(target)) return;
  const duration = 1200;
  const start = performance.now();

  const tick = (now) => {
    const progress = Math.min((now - start) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.round(eased * target).toLocaleString() + (el.textContent.includes('+') ? '+' : '');
    if (progress < 1) requestAnimationFrame(tick);
  };
  requestAnimationFrame(tick);
}

const statObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.querySelectorAll('.num').forEach(n => animateCounter(n));
        statObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.5 }
);

const statsGrid = document.querySelector('.stats-grid');
if (statsGrid) statObserver.observe(statsGrid);

// Registration form
const regForm = document.getElementById('regForm');
if (regForm) {
  regForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());

    if (!data.firstName || !data.email || !data.company) {
      alert('Please fill in all required fields.');
      return;
    }

    if (!document.querySelector('[name="gdpr"]')?.checked) {
      alert('Please agree to the Privacy Notice to continue.');
      return;
    }

    alert(
      'Thank you, ' +
        data.firstName +
        '! A confirmation email will be sent to ' +
        data.email +
        '.'
    );
    e.target.reset();
    // Recheck GDPR
    const gdpr = document.querySelector('[name="gdpr"]');
    if (gdpr) gdpr.checked = true;
  });
}
