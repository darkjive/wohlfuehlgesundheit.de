/**
 * Scroll-Animationen mit animate.css und Intersection Observer
 *
 * Dieses Utility ermöglicht sanfte Fade-In-Animationen beim Scrollen
 */

export interface ScrollAnimationOptions {
  /** Animation beim Einblenden (z.B. 'fadeInUp', 'fadeIn', 'fadeInLeft') */
  animationIn?: string;
  /** Animation-Dauer in Millisekunden */
  duration?: number;
  /** Verzögerung vor Animation-Start in Millisekunden */
  delay?: number;
  /** Schwellenwert für Intersection Observer (0-1) */
  threshold?: number;
  /** Root Margin für Intersection Observer */
  rootMargin?: string;
  /** Animation nur einmal abspielen */
  once?: boolean;
}

const DEFAULT_OPTIONS: ScrollAnimationOptions = {
  animationIn: 'fadeIn',
  duration: 1000,
  delay: 0,
  threshold: 0.1,
  rootMargin: '0px 0px -100px 0px',
  once: true,
};

/**
 * Initialisiert Scroll-Animationen für alle Elemente mit data-animate Attribut
 */
export function initScrollAnimations(): void {
  if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
    return;
  }

  const elements = document.querySelectorAll<HTMLElement>('[data-animate]');

  if (elements.length === 0) {
    return;
  }

  const observerOptions: IntersectionObserverInit = {
    threshold: DEFAULT_OPTIONS.threshold,
    rootMargin: DEFAULT_OPTIONS.rootMargin,
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const element = entry.target as HTMLElement;
        animateElement(element);

        // Element aus Observer entfernen, wenn once=true
        const once = element.dataset.animateOnce !== 'false';
        if (once) {
          observer.unobserve(element);
        }
      }
    });
  }, observerOptions);

  elements.forEach((element) => {
    // Element initial unsichtbar machen
    element.style.opacity = '0';
    observer.observe(element);
  });
}

/**
 * Animiert ein einzelnes Element
 */
function animateElement(element: HTMLElement): void {
  const animation = element.dataset.animate || DEFAULT_OPTIONS.animationIn!;
  const duration = parseInt(element.dataset.animateDuration || String(DEFAULT_OPTIONS.duration), 10);
  const delay = parseInt(element.dataset.animateDelay || String(DEFAULT_OPTIONS.delay), 10);

  // Verzögerung anwenden
  setTimeout(() => {
    element.style.opacity = '1';
    element.classList.add('animate__animated', `animate__${animation}`);

    // Animation-Dauer setzen
    element.style.setProperty('--animate-duration', `${duration}ms`);

    // Cleanup nach Animation
    const handleAnimationEnd = () => {
      element.classList.remove('animate__animated', `animate__${animation}`);
      element.removeEventListener('animationend', handleAnimationEnd);
    };

    element.addEventListener('animationend', handleAnimationEnd);
  }, delay);
}

/**
 * Hilfsfunktion zum manuellen Animieren eines Elements
 */
export function animateOnScroll(selector: string, options: ScrollAnimationOptions = {}): void {
  const mergedOptions = { ...DEFAULT_OPTIONS, ...options };
  const elements = document.querySelectorAll<HTMLElement>(selector);

  elements.forEach((element, index) => {
    element.dataset.animate = mergedOptions.animationIn;
    element.dataset.animateDuration = String(mergedOptions.duration);
    element.dataset.animateDelay = String(mergedOptions.delay! + index * 100); // Gestaffelte Verzögerung
    element.dataset.animateOnce = String(mergedOptions.once);
  });

  initScrollAnimations();
}
