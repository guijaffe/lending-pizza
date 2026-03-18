/**
 * Giusto Gusto — Landing Page
 */
(function () {
  'use strict';

  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  // ---- Preloader ----

  function initPreloader() {
    const video = $('#bg-video');
    const start = Date.now();
    const MIN_MS = 1200;

    const hide = () => {
      const el = $('#preloader');
      if (!el || el.classList.contains('is-hidden')) return;
      el.classList.add('is-hidden');
      document.body.classList.add('is-loaded');
    };

    const finish = (() => {
      let done = false;
      return () => {
        if (done) return;
        done = true;
        const wait = Math.max(0, MIN_MS - (Date.now() - start));
        setTimeout(hide, wait);
      };
    })();

    if (!video || video.readyState >= 3) return finish();

    video.addEventListener('canplay', finish, { once: true });
    video.addEventListener('error', finish, { once: true });
    setTimeout(finish, 4000);
  }

  // ---- Sections ----

  let activeCard = null;

  function toggleBg(show, sectionId) {
    const vid = $('#bg-video');
    // Hide all section backgrounds first
    $$('.section-bg').forEach(bg => bg.classList.remove('is-visible'));
    // Hide default fallback
    const fallback = $('#bg-image');
    if (fallback) fallback.classList.remove('is-visible');

    if (show && sectionId) {
      const sectionBg = $(`.section-bg[data-section-bg="${sectionId}"]`);
      if (sectionBg) {
        sectionBg.classList.add('is-visible');
      } else if (fallback) {
        fallback.classList.add('is-visible');
      }
    }
    if (vid) vid.style.opacity = show ? '0' : '';
  }

  function setActive(id) {
    $$('.bottom-bar__item').forEach(i =>
      i.classList.toggle('is-active', i.getAttribute('href') === `#${id}`)
    );
  }

  function clearActive() {
    $$('.bottom-bar__item').forEach(i => i.classList.remove('is-active'));
  }

  function openSection(id) {
    const card = $(`#${id}`);
    if (!card) return;

    // Close previous
    if (activeCard && activeCard !== card) {
      activeCard.classList.remove('is-open');
      activeCard.setAttribute('aria-hidden', 'true');
    }

    const isOpen = card.classList.toggle('is-open');
    card.setAttribute('aria-hidden', String(!isOpen));

    if (isOpen) {
      activeCard = card;
      setActive(id);
      toggleBg(true, id);
      document.body.classList.add('section-open');
    } else {
      activeCard = null;
      clearActive();
      toggleBg(false, null);
      document.body.classList.remove('section-open');
    }
  }

  function closeAll() {
    $$('.section-card.is-open').forEach(c => {
      c.classList.remove('is-open');
      c.setAttribute('aria-hidden', 'true');
    });
    activeCard = null;
    clearActive();
    toggleBg(false, null);
    document.body.classList.remove('section-open');
  }

  // ---- Init ----

  document.addEventListener('DOMContentLoaded', () => {
    const yearEl = $('#year');
    if (yearEl) yearEl.textContent = new Date().getFullYear();

    // Track QR code scans via Metrika goal
    if (location.hash === '#qr') {
      history.replaceState(null, '', location.pathname);
      if (typeof ym === 'function') ym(107738729, 'reachGoal', 'qr_scan');
    }

    initPreloader();

    // Bottom bar
    $$('.bottom-bar__item[data-section]').forEach(item => {
      item.addEventListener('click', e => {
        e.preventDefault();
        openSection(item.dataset.section + '-section');
      });
    });

    // Close buttons
    $$('[data-close-section]').forEach(btn => {
      btn.addEventListener('click', () => {
        const card = btn.closest('.section-card');
        if (!card) return;
        card.classList.remove('is-open');
        card.setAttribute('aria-hidden', 'true');
        if (activeCard === card) activeCard = null;
        clearActive();
        toggleBg(false, null);
        document.body.classList.remove('section-open');
      });
    });

    // ESC / click outside
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });
    $('#video-wrap')?.addEventListener('click', closeAll);
    $('#hero')?.addEventListener('click', e => { if (!e.target.closest('.btn')) closeAll(); });
  });
})();
