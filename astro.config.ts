import path from 'path';
import { fileURLToPath } from 'url';

import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import tailwind from '@astrojs/tailwind';
import partytown from '@astrojs/partytown';

import icon from 'astro-icon';
import compress from 'astro-compress';
import type { AstroIntegration } from 'astro';

import wohlfuehlgesundheit from './integrations/wohlfuehlgesundheit';

import jopSoftwarecookieconsent from '@jop-software/astro-cookieconsent';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const hasExternalScripts = true;
const whenExternalScripts = (items: (() => AstroIntegration) | (() => AstroIntegration)[] = []) =>
  hasExternalScripts ? (Array.isArray(items) ? items.map((item) => item()) : [items()]) : [];

export default defineConfig({
  output: 'static',

  // Production Build Optimizations
  compressHTML: true,
  build: {
    assets: '_astro',
    inlineStylesheets: 'auto',
  },

  integrations: [
    tailwind({
      applyBaseStyles: false,
    }),
    sitemap(),
    icon({
      include: {
        tabler: ['*'],
      },
    }),
    ...whenExternalScripts(() =>
      partytown({
        config: { forward: ['dataLayer.push'] },
      })
    ),
    compress({
      CSS: true,
      HTML: {
        'html-minifier-terser': {
          removeAttributeQuotes: false,
        },
      },
      Image: false,
      JavaScript: true,
      SVG: false,
      Logger: 1,
    }),
    wohlfuehlgesundheit({
      config: './src/config/site.yaml',
    }),
    jopSoftwarecookieconsent({
      guiOptions: {
        consentModal: {
          layout: 'cloud',
          position: 'bottom center',
          equalWeightButtons: true,
          flipButtons: false,
        },
        preferencesModal: {
          layout: 'box',
          position: 'right',
          equalWeightButtons: true,
          flipButtons: false,
        },
      },
      categories: {
        necessary: {
          enabled: true,
          readOnly: true,
        },
        analytics: {
          autoClear: {
            cookies: [{ name: /^_ga/ }, { name: /^_gid/ }],
          },
        },
        marketing: {},
      },
      language: {
        default: 'de',
        translations: {
          de: {
            consentModal: {
              title: 'Wir verwenden Cookies',
              description:
                'Wir nutzen Cookies, um unsere Website und Services zu verbessern. Du kannst deine Zustimmung jederzeit anpassen.',
              acceptAllBtn: 'Alle akzeptieren',
              acceptNecessaryBtn: 'Nur notwendige',
              showPreferencesBtn: 'Einstellungen',
            },
            preferencesModal: {
              title: 'Cookie-Einstellungen',
              acceptAllBtn: 'Alle akzeptieren',
              acceptNecessaryBtn: 'Nur notwendige',
              savePreferencesBtn: 'Speichern',
              closeIconLabel: 'Schließen',
              sections: [
                {
                  title: 'Notwendig',
                  description: 'Diese Cookies sind für die grundlegende Funktionalität erforderlich.',
                  linkedCategory: 'necessary',
                },
                {
                  title: 'Statistik',
                  description: 'Hilft uns zu verstehen, wie Besucher unsere Website nutzen.',
                  linkedCategory: 'analytics',
                },
                {
                  title: 'Marketing',
                  description: 'Wird verwendet, um personalisierte Inhalte und Werbung anzuzeigen.',
                  linkedCategory: 'marketing',
                },
              ],
            },
          },
        },
      },
    }),
  ],

  image: {
    domains: ['cdn.pixabay.com'],
  },

  vite: {
    define: {
      'import.meta.env.BUILD_TIME': JSON.stringify(new Date().toISOString()),
    },
    resolve: {
      alias: {
        '~': path.resolve(__dirname, './src'),
      },
    },

    // Proxy API requests to PHP dev server
    server: {
      proxy: {
        '/api': {
          target: 'http://localhost:8000',
          changeOrigin: true,
        },
      },
    },

    // Fix source map errors
    build: {
      sourcemap: false, // Disable source maps for production
      cssCodeSplit: true,
      modulePreload: {
        polyfill: false, // Disable polyfill for modern browsers
      },
      rollupOptions: {
        output: {
          // Optimize asset names
          assetFileNames: 'assets/[name].[hash][extname]',
          chunkFileNames: 'assets/[name].[hash].js',
          entryFileNames: 'assets/[name].[hash].js',
          // Optimize chunk splitting for better caching
          manualChunks(id) {
            // Vendor chunks for better caching
            if (id.includes('node_modules')) {
              if (id.includes('@fontsource')) {
                return 'fonts';
              }
              if (id.includes('astro')) {
                return 'astro-runtime';
              }
              return 'vendor';
            }
          },
        },
        onwarn(warning, warn) {
          // Suppress known Astro internal warning - can be removed once PR #14876 is merged
          // See: https://github.com/withastro/astro/issues/14752
          if (
            warning.code === 'UNUSED_EXTERNAL_IMPORT' &&
            warning.exporter === '@astrojs/internal-helpers/remote' &&
            warning.names?.some((name) =>
              ['matchHostname', 'matchPathname', 'matchPort', 'matchProtocol'].includes(name)
            )
          ) {
            return;
          }
          warn(warning);
        },
      },
    },

    // CSS Optimizations
    css: {
      devSourcemap: false,
    },
  },
});
