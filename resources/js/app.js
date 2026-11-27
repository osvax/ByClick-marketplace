import { initCatalogMenu } from './site/catalog-menu.js';
import { initCountdown } from './site/countdown.js';
import { initDemoForms } from './site/forms.js';
import { initHeader } from './site/header.js';
import { initSlider } from './site/slider.js';

function init() {
  initCatalogMenu();
  initHeader();
  initSlider();
  initCountdown();
  initDemoForms();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
  init();
}
