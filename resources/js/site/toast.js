/**
 * Всплывающие уведомления.
 */
let hideTimer = 0;
let frame = 0;
let token = 0;

export function showToast(message, duration = 2600) {
  const el = document.querySelector('[data-va-toast]');
  if (!el) return;

  token += 1;
  const current = token;

  window.clearTimeout(hideTimer);
  window.cancelAnimationFrame(frame);

  el.textContent = message;
  el.hidden = false;

  frame = window.requestAnimationFrame(() => {
    el.classList.add('is-visible');
  });

  hideTimer = window.setTimeout(() => {
    if (current !== token) return;
    el.classList.remove('is-visible');
    hideTimer = window.setTimeout(() => {
      if (current !== token) return;
      el.hidden = true;
    }, 260);
  }, duration);
}
