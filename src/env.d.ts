// eslint-disable-next-line @typescript-eslint/triple-slash-reference
/// <reference path="../.astro/types.d.ts" />
/// <reference types="astro/client" />
/// <reference types="vite/client" />
/// <reference types="../vendor/integration/types.d.ts" />

// Global type declarations
declare global {
  const anime: typeof import('animejs').default;
  interface Window {
    animeLoaded?: boolean;
  }
}
