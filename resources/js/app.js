import { initCatalogMenu } from './site/catalog-menu.js';
import { initCountdown } from './site/countdown.js';
import { initDeliveryMaps } from './site/delivery-map.js';
import { initDemoForms } from './site/forms.js';
import { initHeader } from './site/header.js';
import { initSlider } from './site/slider.js';

function init() {
  initCatalogMenu();
  initHeader();
  initSlider();
  initCountdown();
  initDemoForms();
  initDeliveryMaps();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
  init();
}
