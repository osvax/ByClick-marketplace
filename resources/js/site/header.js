/**
 * Шапка: тень при прокрутке, каталог-выпадашка, мобильное меню, корзина.
 */
import { lockScroll, unlockScroll } from './scroll-lock.js';
import { showToast } from './toast.js';

function initStickyShadow(header) {
  let scheduled = false;

  const update = () => {
    header.classList.toggle('is-scrolled', window.scrollY > 8);
    scheduled = false;
  };

  update();
  window.addEventListener(
    'scroll',
    () => {
      if (scheduled) return;
      scheduled = true;
      window.requestAnimationFrame(update);
    },
    { passive: true },
  );
}

function initCatalog() {
  const button = document.querySelector('[data-va-catalog-btn]');
  const panel = document.querySelector('[data-va-catalog-panel]');
  if (!button || !panel) return;

  const setOpen = (open) => {
    panel.hidden = !open;
    button.setAttribute('aria-expanded', String(open));
  };

  button.addEventListener('click', () => {
    setOpen(panel.hidden);
  });

  document.addEventListener('click', (event) => {
    if (panel.hidden) return;
    if (panel.contains(event.target) || button.contains(event.target)) return;
    setOpen(false);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !panel.hidden) {
      setOpen(false);
      button.focus();
    }
  });

  panel.addEventListener('click', (event) => {
    if (event.target.closest('a')) setOpen(false);
  });
}

function initMenu() {
  const menu = document.querySelector('[data-va-menu]');
  if (!menu) return;

  const panel = menu.querySelector('.va_menu__panel');
  const openers = [...document.querySelectorAll('[data-va-menu-open]')];
  const closers = [...menu.querySelectorAll('[data-va-menu-close]')];
  let lastFocused = null;

  const open = () => {
    if (menu.classList.contains('is-open')) return;
    lastFocused = document.activeElement;
    menu.classList.add('is-open');
    menu.setAttribute('aria-hidden', 'false');
    openers.forEach((btn) => btn.setAttribute('aria-expanded', 'true'));
    lockScroll();

    const closeButton = menu.querySelector('[data-va-menu-close]');
    window.setTimeout(() => closeButton && closeButton.focus(), 60);
  };

  const close = () => {
    if (!menu.classList.contains('is-open')) return;
    menu.classList.remove('is-open');
    menu.setAttribute('aria-hidden', 'true');
    openers.forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
    unlockScroll();

    if (lastFocused && document.contains(lastFocused)) lastFocused.focus();
    else if (openers[0]) openers[0].focus();
  };

  openers.forEach((btn) => btn.addEventListener('click', open));
  closers.forEach((btn) => btn.addEventListener('click', close));

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', close);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') close();
  });

  if (panel) {
    panel.addEventListener('keydown', (event) => {
      if (event.key !== 'Tab') return;
      const focusable = [
        ...panel.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'),
      ].filter((el) => el.offsetParent !== null);
      if (!focusable.length) return;

      const first = focusable[0];
      const last = focusable[focusable.length - 1];

      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }
}

/**
 * Кнопка «Корзина» в шапке просит Livewire открыть drawer:
 * состояние и класс is-open живут в компоненте CartDrawer.
 */
function initCartDrawer() {
  document.addEventListener('click', (event) => {
    if (event.target.closest('[data-va-cart-open]')) {
      window.Livewire?.dispatch('open-cart');
    }
  });
}

/**
 * Кнопки «В корзину» на карточках и странице товара.
 * Количество берётся из data-va-qty (его выставляет Alpine на buybox).
 */
function initCartButtons() {
  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-va-add]');

    if (!button || !window.Livewire) return;

    const productId = Number(button.dataset.vaAdd);
    const quantity = Math.max(1, Number(button.dataset.vaQty || 1));

    if (!productId) return;

    window.Livewire.dispatch('cart-add', { productId, quantity });
  });
}

/**
 * Общие события Livewire: счётчик в шапке, блокировка скролла и тосты.
 */
function initLivewireEvents() {
  const register = () => {
    Livewire.on('cart-updated', ({ count }) => {
      document.querySelectorAll('[data-va-cart-count]').forEach((badge) => {
        badge.textContent = String(count ?? 0);
        badge.hidden = !count;
      });
    });

    Livewire.on('cart-visibility', ({ open }) => {
      document.body.classList.toggle('is-locked', Boolean(open));
    });

    Livewire.on('notify', ({ message }) => {
      if (message) showToast(message);
    });
  };

  if (window.Livewire) {
    register();
    return;
  }

  document.addEventListener('livewire:init', register, { once: true });
}

export function initHeader() {
  const header = document.querySelector('[data-va-header]');
  if (header) initStickyShadow(header);
  initCatalog();
  initMenu();
  initCartDrawer();
  initCartButtons();
  initLivewireEvents();

  document.querySelectorAll('[data-va-demo-toast]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const link = button.closest('a');
      if (link && link.getAttribute('href') === '#') event.preventDefault();
      showToast(button.dataset.vaDemoToast);
    });
  });
}
