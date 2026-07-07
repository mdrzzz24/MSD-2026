/* ============================================================
   Metrodata Solution Day 2026 — main3.js (home3 animations)
   Different animation approach from home2
   ============================================================ */

// ── Mobile menu toggle ──
const toggle = document.getElementById('navToggle');
const links = document.getElementById('navLinks');

if (toggle && links) {
  toggle.addEventListener('click', () => links.classList.toggle('open'));
  links.querySelectorAll('a').forEach(a =>
    a.addEventListener('click', () => links.classList.remove('open'))
  );
}

// ── Active nav link on scroll ──
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

// ── Scroll reveal observer ──
const revealClasses = [
  'reveal-up', 'reveal-scale', 'reveal-side', 'reveal-side-r',
  'reveal-eyebrow'
];

const revealObserver3 = new IntersectionObserver(
  (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver3.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
);

// Collect all elements with reveal classes
revealClasses.forEach(cls => {
  document.querySelectorAll('.' + cls).forEach(el => revealObserver3.observe(el));
});

// ── Stats: Sequential stagger ──
const statsGrid3 = document.getElementById('statsGrid3');
if (statsGrid3) {
  const statCards = statsGrid3.querySelectorAll('.stat-stagger');
  let statsStarted = false;

  function staggerStats(index) {
    if (index >= statCards.length) {
      // All done — reveal features
      const featGrid = document.getElementById('featGrid');
      if (featGrid) {
        featGrid.querySelectorAll('.feat-card').forEach((card, i) => {
          setTimeout(() => card.classList.add('visible'), i * 100);
        });
      }
      return;
    }
    const card = statCards[index];
    card.classList.add('visible');
    const numEl = card.querySelector('.num');
    if (numEl) {
      const target = parseInt(numEl.textContent.replace(/[,+]/g, ''), 10);
      if (!isNaN(target)) {
        const hasPlus = numEl.textContent.includes('+');
        const duration = 800;
        const start = performance.now();
        const tick = (now) => {
          const p = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - p, 3);
          const val = hasPlus
            ? Math.round(eased * target).toLocaleString() + '+'
            : Math.round(eased * target).toLocaleString();
          numEl.textContent = val;
          if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      }
    }
    setTimeout(() => staggerStats(index + 1), 200);
  }

  const statsObserver3 = new IntersectionObserver(
    (entries) => {
      if (!statsStarted && entries[0].isIntersecting) {
        statsStarted = true;
        setTimeout(() => staggerStats(0), 200);
        statsObserver3.unobserve(entries[0].target);
      }
    },
    { threshold: 0.3 }
  );
  statsObserver3.observe(statsGrid3);
}

// ── Agenda table: Staggered row reveal ──
const agendaRows = document.querySelectorAll('.agenda-row');
if (agendaRows.length) {
  const agendaObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // Reveal rows one by one
          agendaRows.forEach((row, i) => {
            setTimeout(() => {
              row.classList.add('visible');
            }, i * 80);
          });
          agendaObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  );
  agendaObserver.observe(document.getElementById('agendaTable'));
}

// ── Why items: Staggered reveal ──
const whyItems = document.querySelectorAll('.why-stagger');
if (whyItems.length) {
  const whyObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          whyItems.forEach((item, i) => {
            setTimeout(() => item.classList.add('visible'), i * 80 + 200);
          });
          whyObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15 }
  );
  const whyContainer = document.querySelector('.why-grid');
  if (whyContainer) whyObserver.observe(whyContainer);
}

// ── Sponsor cards: Staggered scale reveal ──
const sponsorCards = document.querySelectorAll('.sponsor-card');
if (sponsorCards.length) {
  const sponsorObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          sponsorCards.forEach((card, i) => {
            setTimeout(() => card.classList.add('visible'), i * 40);
          });
          sponsorObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  );
  const sponsorGrid = document.getElementById('sponsorGrid3');
  if (sponsorGrid) sponsorObserver.observe(sponsorGrid);
}

// ── Registration Form Submit ──
const regForm = document.getElementById('regForm');
if (regForm) {
  regForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = regForm.querySelector('.btn-submit');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Submitting...';

    try {
      const formData = new FormData(regForm);
      const response = await fetch(regForm.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      const data = await response.json();

      if (response.ok && data.success) {
        showSuccessModal3();
        regForm.reset();
        const gdpr = regForm.querySelector('[name="gdpr"]');
        if (gdpr) gdpr.checked = true;
      } else {
        let errorMsg = '';
        for (const [field, messages] of Object.entries(data.errors || {})) {
          errorMsg += messages.join('\n') + '\n';
        }
        alert(errorMsg || 'An error occurred. Please try again.');
      }
    } catch (err) {
      alert('A network error occurred. Please try again.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
}

// ── Success Modal ──
function showSuccessModal3() {
  const modal = document.getElementById('successModal');
  if (modal) modal.style.display = 'flex';
}
function closeSuccessModal() {
  const modal = document.getElementById('successModal');
  if (modal) modal.style.display = 'none';
}
document.addEventListener('click', function(e) {
  const modal = document.getElementById('successModal');
  if (modal && e.target === modal) closeSuccessModal();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeSuccessModal();
});
