/**
 * Scroll animations with animate.css and Intersection Observer
 *
 * This utility enables smooth fade-in animations on scroll
 */

export interface ScrollAnimationOptions {
  /** Animation on reveal (e.g. 'fadeInUp', 'fadeIn', 'fadeInLeft') */
  animationIn?: string;
  /** Animation duration in milliseconds */
  duration?: number;
  /** Delay before animation start in milliseconds */
  delay?: number;
  /** Threshold for Intersection Observer (0-1) */
  threshold?: number;
  /** Root margin for Intersection Observer */
  rootMargin?: string;
  /** Play animation only once */
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
 * Initialize scroll animations for all elements with data-animate attribute
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

        // Remove element from observer if once=true
        const once = element.dataset.animateOnce !== 'false';
        if (once) {
          observer.unobserve(element);
        }
      }
    });
  }, observerOptions);

  elements.forEach((element) => {
    // Make element initially invisible
    element.style.opacity = '0';
    observer.observe(element);
  });
}

/**
 * Animate a single element
 */
function animateElement(element: HTMLElement): void {
  const animation = element.dataset.animate || DEFAULT_OPTIONS.animationIn!;
  const duration = parseInt(element.dataset.animateDuration || String(DEFAULT_OPTIONS.duration), 10);
  const delay = parseInt(element.dataset.animateDelay || String(DEFAULT_OPTIONS.delay), 10);

  // Apply delay
  setTimeout(() => {
    element.style.opacity = '1';
    element.classList.add('animate__animated', `animate__${animation}`);

    // Set animation duration
    element.style.setProperty('--animate-duration', `${duration}ms`);

    // Cleanup after animation
    const handleAnimationEnd = () => {
      element.classList.remove('animate__animated', `animate__${animation}`);
      element.removeEventListener('animationend', handleAnimationEnd);
    };

    element.addEventListener('animationend', handleAnimationEnd);
  }, delay);
}

/**
 * Helper function to manually animate an element
 */
export function animateOnScroll(selector: string, options: ScrollAnimationOptions = {}): void {
  const mergedOptions = { ...DEFAULT_OPTIONS, ...options };
  const elements = document.querySelectorAll<HTMLElement>(selector);

  elements.forEach((element, index) => {
    element.dataset.animate = mergedOptions.animationIn;
    element.dataset.animateDuration = String(mergedOptions.duration);
    element.dataset.animateDelay = String(mergedOptions.delay! + index * 100); // Staggered delay
    element.dataset.animateOnce = String(mergedOptions.once);
  });

  initScrollAnimations();
}
