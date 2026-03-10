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
      if (video) video.classList.add('is-visible');
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

  function toggleBg(show) {
    const img = $('#bg-image');
    const vid = $('#bg-video');
    if (img) img.classList.toggle('is-visible', show);
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
      toggleBg(true);
    } else {
      activeCard = null;
      clearActive();
      toggleBg(false);
    }
  }

  function closeAll() {
    $$('.section-card.is-open').forEach(c => {
      c.classList.remove('is-open');
      c.setAttribute('aria-hidden', 'true');
    });
    activeCard = null;
    clearActive();
    toggleBg(false);
  }

  // ---- Init ----

  document.addEventListener('DOMContentLoaded', () => {
    const yearEl = $('#year');
    if (yearEl) yearEl.textContent = new Date().getFullYear();

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
        toggleBg(false);
      });
    });

    // ESC / click outside
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });
    $('#video-wrap')?.addEventListener('click', closeAll);
    $('#hero')?.addEventListener('click', e => { if (!e.target.closest('.btn')) closeAll(); });
  });
})();
