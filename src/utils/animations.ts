/**
 * Animation Utilities using anime.js
 * Provides smooth, calm animations with viewport detection
 */

import type anime from 'animejs';

// Animation configuration types
export interface AnimationConfig {
  type: AnimationType;
  delay?: number;
  duration?: number;
  easing?: string;
  stagger?: number;
}

export type AnimationType =
  | 'fade-up'
  | 'fade-down'
  | 'fade-left'
  | 'fade-right'
  | 'fade-in'
  | 'scale-up'
  | 'scale-down'
  | 'slide-up'
  | 'slide-down'
  | 'slide-left'
  | 'slide-right'
  | 'zoom-in'
  | 'zoom-out'
  | 'rotate-in'
  | 'gentle-float';

// Default animation settings for calm, smooth feel
export const DEFAULT_ANIMATION_DURATION = 1200; // Longer for calmer feel
export const DEFAULT_EASING = 'easeOutQuad'; // Gentle easing
export const DEFAULT_THRESHOLD = 0.15; // Start animation when 15% visible
export const DEFAULT_ROOT_MARGIN = '0px 0px -10% 0px'; // Start slightly before entering viewport

/**
 * Get animation properties based on type
 */
export function getAnimationProperties(type: AnimationType): Record<string, any> {
  const animations: Record<AnimationType, Record<string, any>> = {
    'fade-up': {
      opacity: [0, 1],
      translateY: [40, 0],
    },
    'fade-down': {
      opacity: [0, 1],
      translateY: [-40, 0],
    },
    'fade-left': {
      opacity: [0, 1],
      translateX: [40, 0],
    },
    'fade-right': {
      opacity: [0, 1],
      translateX: [-40, 0],
    },
    'fade-in': {
      opacity: [0, 1],
    },
    'scale-up': {
      opacity: [0, 1],
      scale: [0.9, 1],
    },
    'scale-down': {
      opacity: [0, 1],
      scale: [1.1, 1],
    },
    'slide-up': {
      translateY: [60, 0],
      opacity: [0, 1],
    },
    'slide-down': {
      translateY: [-60, 0],
      opacity: [0, 1],
    },
    'slide-left': {
      translateX: [60, 0],
      opacity: [0, 1],
    },
    'slide-right': {
      translateX: [-60, 0],
      opacity: [0, 1],
    },
    'zoom-in': {
      opacity: [0, 1],
      scale: [0.8, 1],
    },
    'zoom-out': {
      opacity: [0, 1],
      scale: [1.2, 1],
    },
    'rotate-in': {
      opacity: [0, 1],
      rotate: [-5, 0],
      scale: [0.95, 1],
    },
    'gentle-float': {
      opacity: [0, 1],
      translateY: [20, 0],
      scale: [0.98, 1],
    },
  };

  return animations[type] || animations['fade-up'];
}

/**
 * Initialize scroll animations with Intersection Observer
 */
export function initScrollAnimations(animeInstance: typeof anime): () => void {
  // Check if reduced motion is preferred
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    // Show all elements immediately without animation
    const elements = document.querySelectorAll('[data-animate]');
    elements.forEach((el) => {
      (el as HTMLElement).style.opacity = '1';
    });
    return () => {};
  }

  const animatedElements = document.querySelectorAll('[data-animate]');

  if (!animatedElements.length) {
    return () => {};
  }

  // Create observer for viewport detection
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const element = entry.target as HTMLElement;

          // Get animation configuration from data attributes
          const type = (element.getAttribute('data-anim-type') || 'fade-up') as AnimationType;
          const delay = parseInt(element.getAttribute('data-anim-delay') || '0', 10);
          const duration = parseInt(
            element.getAttribute('data-anim-duration') || String(DEFAULT_ANIMATION_DURATION),
            10
          );
          const easing = element.getAttribute('data-anim-easing') || DEFAULT_EASING;
          const stagger = parseInt(element.getAttribute('data-anim-stagger') || '0', 10);

          // Get animation properties
          const animationProps = getAnimationProperties(type);

          // Animate children if stagger is specified
          const targets = stagger > 0 ? element.children : element;

          // Execute animation
          animeInstance({
            targets,
            ...animationProps,
            duration,
            easing,
            delay: stagger > 0 ? animeInstance.stagger(stagger, { start: delay }) : delay,
            complete: () => {
              element.setAttribute('data-animated', 'true');
              // Ensure final state is set
              (element as HTMLElement).style.opacity = '1';
              (element as HTMLElement).style.transform = 'none';
            },
          });

          // Stop observing this element (animate once)
          observer.unobserve(element);
        }
      });
    },
    {
      threshold: DEFAULT_THRESHOLD,
      rootMargin: DEFAULT_ROOT_MARGIN,
    }
  );

  // Observe all elements
  animatedElements.forEach((element) => {
    observer.observe(element);
  });

  // Return cleanup function
  return () => {
    observer.disconnect();
  };
}

/**
 * Add animation attributes to elements based on selector
 * Useful for automatically animating elements
 */
export function autoAnimateElements() {
  // Define element groups with their default animations
  const elementGroups = [
    // Headings - gentle scale and fade
    { selector: 'h1:not([data-animate])', type: 'gentle-float', delay: 0 },
    { selector: 'h2:not([data-animate])', type: 'fade-up', delay: 100 },
    { selector: 'h3:not([data-animate])', type: 'fade-up', delay: 150 },
    { selector: 'h4:not([data-animate])', type: 'fade-up', delay: 150 },
    { selector: 'h5:not([data-animate])', type: 'fade-up', delay: 150 },
    { selector: 'h6:not([data-animate])', type: 'fade-up', delay: 150 },

    // Content elements
    { selector: 'p:not([data-animate])', type: 'fade-in', delay: 200 },
    { selector: 'ul:not([data-animate])', type: 'fade-up', delay: 200, stagger: 50 },
    { selector: 'ol:not([data-animate])', type: 'fade-up', delay: 200, stagger: 50 },

    // Images and media
    { selector: 'img:not([data-animate])', type: 'scale-up', delay: 100 },
    { selector: 'figure:not([data-animate])', type: 'zoom-in', delay: 150 },
    { selector: 'picture:not([data-animate])', type: 'scale-up', delay: 100 },

    // Interactive elements
    { selector: 'button:not([data-animate])', type: 'scale-up', delay: 250 },
    { selector: 'a.btn:not([data-animate])', type: 'scale-up', delay: 250 },
    { selector: 'form:not([data-animate])', type: 'fade-up', delay: 200 },

    // Cards and containers
    { selector: 'article:not([data-animate])', type: 'fade-up', delay: 100 },
    { selector: '.card:not([data-animate])', type: 'zoom-in', delay: 100 },
    { selector: '[class*="grid"] > *:not([data-animate])', type: 'fade-up', delay: 0, stagger: 80 },

    // Blockquotes and special content
    { selector: 'blockquote:not([data-animate])', type: 'fade-left', delay: 200 },
    { selector: 'code:not([data-animate])', type: 'fade-in', delay: 250 },
    { selector: 'pre:not([data-animate])', type: 'fade-up', delay: 200 },
  ];

  elementGroups.forEach(({ selector, type, delay, stagger }) => {
    const elements = document.querySelectorAll(selector);
    elements.forEach((element) => {
      // Skip if already animated or in navigation/header
      const isInHeader = element.closest('header, nav, [data-no-animate]');
      if (isInHeader) return;

      // Add animation attributes
      element.setAttribute('data-animate', 'true');
      element.setAttribute('data-anim-type', type);
      if (delay) element.setAttribute('data-anim-delay', String(delay));
      if (stagger) element.setAttribute('data-anim-stagger', String(stagger));
    });
  });
}
