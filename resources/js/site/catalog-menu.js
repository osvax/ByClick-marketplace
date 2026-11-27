/**
 * Мега-меню каталога: слева основные категории, справа — подкатегории.
 * Разметка категорий рендерится на сервере, скрипт только переключает панели.
 */
export function initCatalogMenu() {
  const panel = document.querySelector('[data-va-catalog-panel]');
  const main = panel?.querySelector('[data-va-catalog-main]');
  const sub = panel?.querySelector('[data-va-catalog-sub]');
  if (!panel || !main || !sub) return;

  const buttons = [...main.querySelectorAll('[data-va-cat-index]')];
  const panels = [...sub.querySelectorAll('[data-va-cat-panel]')];
  if (!buttons.length || !panels.length) return;

  let activeIndex = 0;

  const select = (index, focus = false) => {
    const target = panels[index];
    if (!target) return;

    activeIndex = index;

    buttons.forEach((button, buttonIndex) => {
      const active = buttonIndex === index;
      button.setAttribute('aria-selected', String(active));
      button.tabIndex = active ? 0 : -1;
      button.classList.toggle('is-active', active);
    });

    panels.forEach((item, itemIndex) => {
      item.hidden = itemIndex !== index;
    });

    const button = buttons[index];
    if (button?.id) {
      sub.setAttribute('aria-labelledby', button.id);
    }

    if (focus) buttons[index]?.focus();
  };

  buttons.forEach((button, index) => {
    button.addEventListener('mouseenter', () => select(index));
    button.addEventListener('focus', () => select(index));
    button.addEventListener('click', () => select(index));
    button.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp' && event.key !== 'Home' && event.key !== 'End') {
        return;
      }
      event.preventDefault();
      let next = index;
      if (event.key === 'ArrowDown') next = (index + 1) % buttons.length;
      if (event.key === 'ArrowUp') next = (index - 1 + buttons.length) % buttons.length;
      if (event.key === 'Home') next = 0;
      if (event.key === 'End') next = buttons.length - 1;
      select(next, true);
    });
  });

  const catalogButton = document.querySelector('[data-va-catalog-btn]');
  catalogButton?.addEventListener('click', () => {
    if (!panel.hidden) select(activeIndex);
  });

  select(0);
}
