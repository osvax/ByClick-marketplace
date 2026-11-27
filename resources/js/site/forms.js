/**
 * Демонстрационные формы: показывают уведомление вместо отправки на сервер.
 */
import { showToast } from './toast.js';

export function initDemoForms() {
  document.querySelectorAll('[data-va-demo-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      showToast(form.dataset.message || 'Демо-режим: форма отправлена');
      form.reset();
    });
  });
}
