/**
 * Таймер обратного отсчёта до конца дня.
 */
const pad = (value) => String(value).padStart(2, '0');

export function initCountdown() {
  const root = document.querySelector('[data-va-countdown]');
  if (!root) return;

  const cells = {
    h: root.querySelector('[data-va-cd="h"]'),
    m: root.querySelector('[data-va-cd="m"]'),
    s: root.querySelector('[data-va-cd="s"]'),
  };

  if (!cells.h || !cells.m || !cells.s) return;

  const tick = () => {
    const now = new Date();
    const end = new Date(now);
    end.setHours(24, 0, 0, 0);

    const diff = Math.max(0, end.getTime() - now.getTime());
    const hours = Math.floor(diff / 3_600_000);
    const minutes = Math.floor((diff % 3_600_000) / 60_000);
    const seconds = Math.floor((diff % 60_000) / 1000);

    cells.h.textContent = pad(hours);
    cells.m.textContent = pad(minutes);
    cells.s.textContent = pad(seconds);
  };

  tick();
  window.setInterval(tick, 1000);
}
