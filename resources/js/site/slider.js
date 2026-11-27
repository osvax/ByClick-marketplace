/**
 * Слайдер промо-баннеров: автопрокрутка, стрелки, точки, свайп, клавиатура.
 */
const AUTOPLAY_MS = 6500;
const SWIPE_THRESHOLD = 45;

export function initSlider() {
  const root = document.querySelector('[data-va-slider]');
  if (!root) return;

  const track = root.querySelector('[data-va-track]');
  const slides = [...root.querySelectorAll('[data-va-slide]')];
  const dots = [...root.querySelectorAll('[data-va-dot]')];
  const prev = root.querySelector('[data-va-slide-prev]');
  const next = root.querySelector('[data-va-slide-next]');
  if (!track || slides.length < 2) return;

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let index = 0;
  let timer = 0;
  let paused = false;
  let startX = null;

  const goTo = (nextIndex) => {
    index = (nextIndex + slides.length) % slides.length;
    track.style.transform = `translateX(-${index * 100}%)`;

    slides.forEach((slide, i) => {
      const active = i === index;
      slide.classList.toggle('is-active', active);
      slide.inert = !active;
    });

    dots.forEach((dot, i) => {
      const active = i === index;
      dot.classList.toggle('is-active', active);
      dot.setAttribute('aria-selected', String(active));
    });
  };

  const stop = () => {
    window.clearInterval(timer);
    timer = 0;
  };

  const start = () => {
    stop();
    if (reduceMotion) return;
    timer = window.setInterval(() => {
      if (!paused) goTo(index + 1);
    }, AUTOPLAY_MS);
  };

  const restart = () => {
    start();
  };

  prev && prev.addEventListener('click', () => {
    goTo(index - 1);
    restart();
  });

  next && next.addEventListener('click', () => {
    goTo(index + 1);
    restart();
  });

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
      goTo(i);
      restart();
    });
  });

  root.addEventListener('mouseenter', () => {
    paused = true;
  });
  root.addEventListener('mouseleave', () => {
    paused = false;
  });
  root.addEventListener('focusin', () => {
    paused = true;
  });
  root.addEventListener('focusout', (event) => {
    if (!root.contains(event.relatedTarget)) paused = false;
  });

  root.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') {
      goTo(index - 1);
      restart();
    }
    if (event.key === 'ArrowRight') {
      goTo(index + 1);
      restart();
    }
  });

  root.addEventListener('pointerdown', (event) => {
    if (event.pointerType === 'mouse' && event.button !== 0) return;
    startX = event.clientX;
  });

  root.addEventListener('pointerup', (event) => {
    if (startX === null) return;
    const delta = event.clientX - startX;
    startX = null;
    if (Math.abs(delta) < SWIPE_THRESHOLD) return;
    goTo(delta > 0 ? index - 1 : index + 1);
    restart();
  });

  root.addEventListener('pointercancel', () => {
    startX = null;
  });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stop();
    else start();
  });

  goTo(0);
  start();
}
