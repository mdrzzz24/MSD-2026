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

// Stats card sequential pop-in animation
const statsGrid = document.querySelector('.stats-grid');
if (statsGrid) {
  const statCards = statsGrid.querySelectorAll('.stat-pop');
  let started = false;

  // Build overlay inside stats-grid
  const overlay = document.createElement('div');
  overlay.className = 'stats-overlay hide';
  overlay.innerHTML = '<div class="overlay-card"><div class="overlay-num">0</div><div class="overlay-lbl"></div></div>';
  statsGrid.appendChild(overlay);
  const overlayNum = overlay.querySelector('.overlay-num');
  const overlayLbl = overlay.querySelector('.overlay-lbl');

  function showOverlay(label) {
    overlay.classList.remove('hide');
    overlayNum.textContent = '0';
    overlayLbl.textContent = label;
  }
  function hideOverlay() {
    overlay.classList.add('hide');
  }

  function animateNum(el, target, hasPlus, duration, cb) {
    const start = performance.now();
    const tick = (now) => {
      const p = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      const val = (hasPlus ? Math.round(eased * target).toLocaleString() + '+' : Math.round(eased * target).toLocaleString());
      el.textContent = val;
      if (p < 1) requestAnimationFrame(tick);
      else if (cb) cb();
    };
    requestAnimationFrame(tick);
  }

  function animateStatCard(index) {
    if (index >= statCards.length) {
      // All stats done — show features
      const features = document.querySelector('.reveal-features');
      if (features) {
        setTimeout(() => features.classList.add('visible'), 100);
      }
      return;
    }
    const card = statCards[index];
    const numEl = card.querySelector('.num');
    const lblEl = card.querySelector('.lbl');
    const target = parseInt(numEl.textContent.replace(/[,+]/g, ''), 10);
    const hasPlus = numEl.textContent.includes('+');
    const lbl = lblEl.textContent;

    // Step 1: Show overlay large in center
    showOverlay(lbl);

    // Step 2: Count up on overlay
    setTimeout(() => {
      animateNum(overlayNum, target, hasPlus, 700, () => {
        // Step 3: Hide overlay, reveal card in grid
        setTimeout(() => {
          hideOverlay();
          card.classList.add('stat-done');
          // Step 4: Next card after delay
          setTimeout(() => {
            animateStatCard(index + 1);
          }, 200);
        }, 150);
      });
    }, 150);
  }

  const statObserver = new IntersectionObserver(
    (entries) => {
      if (!started && entries[0].isIntersecting) {
        started = true;
        animateStatCard(0);
        statObserver.unobserve(entries[0].target);
      }
    },
    { threshold: 0.3 }
  );

  statObserver.observe(statsGrid);
}

// Registration form — submit to server, show modal on success
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
        // Show success modal instead of redirect
        showSuccessModal();
        regForm.reset();
        const gdpr = regForm.querySelector('[name="gdpr"]');
        if (gdpr) gdpr.checked = true;
      } else {
        // Show validation errors
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

// Success modal helpers
function showSuccessModal() {
  const modal = document.getElementById('successModal');
  if (modal) {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }
}

function closeSuccessModal() {
  const modal = document.getElementById('successModal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
  const modal = document.getElementById('successModal');
  if (modal && e.target === modal) closeSuccessModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeSuccessModal();
});
