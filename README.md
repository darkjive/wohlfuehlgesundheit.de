# Stefanie Leidel - Holistische Darmtherapie

Website: Wohlfühlgesundheit Holistische Darmtherapie , entwickelt mit Astro und Tailwind CSS.

## Projektstruktur

```
/
├── integrations/          # Custom Astro Integrations
│   └── astrowind/        # AstroWind Template Integration
│       ├── index.ts
│       ├── types.d.ts
│       └── utils/
├── public/
│   ├── api/              # PHP Backend APIs
│   │   ├── anamnese-booking.php    # Anamnesebogen & Zoom Integration
│   │   ├── contact-form.php        # Kontaktformular Handler
│   │   ├── bootstrap.php           # API Bootstrap
│   │   ├── config.php              # API Konfiguration
│   │   ├── env-loader.php          # Environment Loader
│   │   ├── get-csrf-token.php      # CSRF Token Generator
│   │   ├── phpmailer-helper.php    # E-Mail Helper
│   │   └── security.php            # Security Functions
│   ├── vendor/           # PHP Composer Dependencies
│   ├── _headers          # Security Headers
│   ├── .htaccess         # Apache Konfiguration
│   └── robots.txt
├── src/
│   ├── assets/
│   │   ├── favicons/
│   │   ├── images/
│   │   └── styles/
│   │       └── tailwind.css
│   ├── components/
│   │   ├── common/       # Gemeinsame Komponenten
│   │   │   ├── Header.astro
│   │   │   ├── Footer.astro
│   │   │   ├── Logo-lg.astro
│   │   │   ├── Logo-sm.astro
│   │   │   └── ...
│   │   ├── ui/           # UI Komponenten
│   │   │   ├── Button.astro
│   │   │   ├── FormContact.astro
│   │   │   └── ...
│   │   ├── widgets/      # Widget Komponenten
│   │   │   ├── anamnese-form/    # Anamnesebogen Komponenten
│   │   │   │   ├── PersonalInfo.astro
│   │   │   │   ├── PersonalData.astro
│   │   │   │   ├── MedicalHistory.astro
│   │   │   │   ├── HealthQuestions.astro
│   │   │   │   ├── NutritionLifestyle.astro
│   │   │   │   ├── Digestion.astro
│   │   │   │   ├── Expectations.astro
│   │   │   │   ├── Readiness.astro
│   │   │   │   ├── ZoomBooking.astro
│   │   │   │   └── Consent.astro
│   │   │   ├── Hero.astro
│   │   │   ├── Features.astro
│   │   │   ├── CallToAction.astro
│   │   │   └── ...
│   │   ├── BasicScripts.astro
│   │   ├── CustomStyles.astro
│   │   └── Favicons.astro
│   ├── config/           # Konfigurationsdateien
│   │   ├── site.yaml     # Site-Konfiguration
│   │   └── navigation.ts # Navigation
│   ├── layouts/
│   │   ├── Layout.astro
│   │   ├── MarkdownLayout.astro
│   │   └── PageLayout.astro
│   ├── pages/
│   │   ├── index.astro
│   │   ├── ueber-mich.astro
│   │   ├── angebot.astro
│   │   ├── termin-buchen.astro
│   │   ├── kontakt.astro
│   │   ├── danke.astro
│   │   └── 404.astro
│   ├── utils/            # Utility Functions
│   │   ├── images.ts
│   │   ├── images-optimization.ts
│   │   ├── permalinks.ts
│   │   └── utils.ts
│   ├── env.d.ts
│   └── types.d.ts
├── composer.json         # PHP Dependencies
├── package.json
├── astro.config.ts
└── tailwind.config.js
```

## Installation und Entwicklung

Alle Befehle werden im Hauptverzeichnis des Projekts ausgeführt:

| Befehl               | Aktion                                                         |
| -------------------- | -------------------------------------------------------------- |
| `npm install`        | Installiert Abhängigkeiten                                     |
| `npm start`          | **Empfohlen**: Startet PHP + Astro Dev-Server (localhost:4321) |
| `npm run dev:full`   | Gleich wie `npm start` - startet beide Server                  |
| `npm run dev`        | Startet nur Astro Dev-Server (localhost:4321) - **ohne PHP!**  |
| `npm run dev:php`    | Startet nur PHP Dev-Server (localhost:8000)                    |
| `npm run build`      | Erstellt die produktionsreife Website in ./dist/               |
| `npm run preview`    | Vorschau der gebauten Website vor dem Deployment               |
| `npm run check`      | Überprüft das Projekt auf Fehler                               |
| `npm run fix`        | Führt ESLint aus und formatiert Code mit Prettier              |
| `npm run astro ...`  | Führt Astro CLI-Befehle aus                                    |
| `npm run deploy ...` | Führt Deployment-Befehle aus                                   |

### Lokale Entwicklung mit PHP-Backend

**Wichtig**: Diese Website nutzt PHP für Backend-APIs (Formulare, CSRF-Token, Zoom-Integration). Für die lokale Entwicklung müssen **beide Server** laufen:

1. **PHP-Development-Server** (Port 8000) - führt PHP-Dateien aus
2. **Astro-Development-Server** (Port 4321) - dient die Website

**Empfohlener Start:**

```bash
npm start
# oder
npm run dev:full
```

Dies startet automatisch beide Server. Die Website ist dann unter `http://localhost:4321` erreichbar und alle `/api/*` Anfragen werden automatisch an den PHP-Server weitergeleitet.

**Manuelle Variante** (in separaten Terminals):

```bash
# Terminal 1: PHP-Server starten
npm run dev:php

# Terminal 2: Astro-Server starten
npm run dev
```

## Konfiguration

Die Hauptkonfigurationsdatei befindet sich unter `./src/config/site.yaml`:

```yaml
  name: ''
  site: 'https://wohlfühlgesundheit.de'
  base: '/'
  trailingSlash: false

metadata:
  title:
    default:
    template: '%s | Wohlfühlgesundheit - Holistische Darmtherapie'
  description: 'Wohlfühlgesundheit - Holistische Darmtherapie, Ihre Holistische Darmtherapeutin. Entdecken Sie Tipps und Programme zur Verbesserung Ihrer Gesundheit und Ihres Wohlbefindens. Starten Sie Ihre Reise zu einem gesünderen Lebensstil noch heute!'
  robots:
    index: true
    follow: true
  openGraph:
    site_name: Wohlfühlgesundheit - Holistische Darmtherapie
    images:
      - url: '~/assets/images/default.png'
        width: 1200
        height: 628
    type: website
  twitter:
    handle: '@onwidget'
    site: '@onwidget'
    cardType: summary_large_image

ui:
  theme: 'system' # "system" | "light" | "dark"
```

## Anpassungen

### Styling

Für Anpassungen der Schriftarten, Farben oder anderen Design-Elementen:

- `src/components/CustomStyles.astro`
- `src/assets/styles/tailwind.css`

### Inhalte

- **Seiten**: `src/pages/` - Alle Astro-Seiten (index, über-mich, termin-buchen, kontakt, etc.)
- **Komponenten**: `src/components/` - Wiederverwendbare Komponenten
  - `common/` - Gemeinsame Komponenten (Header, Footer, Logos)
  - `ui/` - UI-Komponenten (Button, Forms, etc.)
  - `widgets/` - Komplexe Widgets (Hero, Features, etc.)
- **Konfiguration**: `src/config/` - Site- und Navigation-Konfiguration
- **API**: `public/api/` - PHP Backend für Formulare und Zoom Integration

## Deployment

### Manuelles Deployment

1. Produktionsbuild erstellen:

   ```bash
   npm run build
   ```

2. Der `dist/` Ordner enthält alle statischen Dateien für das Deployment

3. Deployment starten

   ```bash
   npm run deploy
   ```

## Technische Details

### Frontend

- **Framework**: Astro 5.x (Static Site Generator)
- **Styling**: Tailwind CSS 3.x mit Typography Plugin
- **Programmiersprache**: TypeScript
- **Icons**: Astro Icon (Tabler Icons & Flat Color Icons)
- **Schriftarten**:
  - Noto Serif Display (Variable Font)
  - Quicksand (Variable Font)
- **View Transitions**: Astro View Transitions für sanfte Seitenübergänge

### Backend & APIs

Die Website nutzt ein Custom PHP Backend für die Formular-Verarbeitung und externe Integrationen:

- **PHP API** (`public/api/`)
  - **anamnese-booking.php**: Hauptendpoint für Anamnesebogen & Zoom-Buchung
  - **contact-form.php**: Kontaktformular-Handler
  - **bootstrap.php**: Zentrale Initialisierung & Dependency Injection
  - **config.php**: API-Konfiguration (E-Mail, Zoom, etc.)
  - **env-loader.php**: Umgebungsvariablen & Secrets Management
  - **security.php**: CSRF-Protection, Rate Limiting, Input Validation
  - **phpmailer-helper.php**: E-Mail-Versand über PHPMailer

- **Zoom Integration**:
  - Server-to-Server OAuth 2.0 Authentifizierung
  - Automatische Meeting-Erstellung via Zoom API
  - E-Mail-Benachrichtigungen mit Zoom-Zugangsdaten
  - Terminverwaltung & Kalender-Integration (ICS)
  - Warteraum-Funktion für erhöhte Sicherheit

- **Sicherheitsfeatures**:
  - CSRF-Token-Validierung für alle Forms
  - Rate Limiting gegen Spam & Brute-Force
  - Input Sanitization & Validation
  - Security Headers (CSP, HSTS, X-Frame-Options)
  - .htaccess Hardening

### Integrationen & Plugins

- **@astrojs/sitemap**: XML-Sitemap-Generierung
- **@astrojs/rss**: RSS-Feed-Support
- **@astrojs/partytown**: Optimierung von Third-Party-Scripts
- **@jop-software/astro-cookieconsent**: DSGVO-konforme Cookie-Verwaltung
- **@astrolib/analytics**: Analytics-Integration
- **@astrolib/seo**: SEO-Optimierung
- **astro-compress**: Asset-Komprimierung (CSS, HTML, JS)
- **astro-embed**: Embed-Support für externe Inhalte
- **astro-emoji**: Emoji-Support

### Analytics & SEO

- **Google Analytics**: G-TT6VB0HM46
- **Google Site Verification**: orcPxI47GSa-cRvY11tUe6iGg2IO_RPvnA1q95iEM3M
- **OpenGraph & Twitter Cards**: Vollständige Social-Media-Integration
- **Robots.txt**: Suchmaschinen-Steuerung

### Besondere Features

#### 1. Zoom-Booking-System

Vollautomatisches Terminbuchungssystem mit:

- Zoom-Meeting-Erstellung via API
- Automatische E-Mail-Benachrichtigungen (HTML für Kunden, Text für Admin)
- Terminvalidierung und Zeitzonenmanagement (Europe/Berlin)
- Warteraum-Funktion für mehr Sicherheit
- Flexible Terminlängen (20/40 Minuten)

#### 2. Umfangreicher Anamnesebogen

Mehrstufiges Formular mit 10 detaillierten Schritten (`src/components/widgets/anamnese-form/`):

1. **PersonalInfo.astro**: Persönliche Grunddaten
2. **PersonalData.astro**: Erweiterte persönliche Informationen
3. **MedicalHistory.astro**: Medizinische Vorgeschichte & Diagnosen
4. **HealthQuestions.astro**: Gesundheitsspezifische Fragen
5. **NutritionLifestyle.astro**: Ernährungs- & Lifestyle-Analyse
6. **Digestion.astro**: Verdauungssystem & Beschwerden
7. **Expectations.astro**: Erwartungen & Zielsetzungen
8. **Readiness.astro**: Bereitschafts-Assessment für Therapie
9. **ZoomBooking.astro**: Terminbuchung & Zoom-Meeting-Auswahl
10. **Consent.astro**: DSGVO-konforme Einwilligungen & Datenschutz

**Features**:

- Progressive Form mit Fortschrittsanzeige
- Client-seitige Validierung
- Auto-Save Funktionalität (LocalStorage)
- Responsive Design für mobile & Desktop
- Direkte Integration mit Zoom-API für Terminbuchung

#### 3. Dark Mode

- System-Theme-Erkennung
- Manueller Light/Dark Mode Toggle
- Persistente Theme-Speicherung

#### 4. Cookie Consent Management

- DSGVO-konform
- Kategorien: Notwendig, Statistik, Marketing
- Vollständig auf Deutsch lokalisiert
- Anpassbare Einstellungen

#### 5. View Transitions

Die Website nutzt Astro's native View Transitions API:

- **Sanfte Seitenübergänge**: Nahtlose Übergänge zwischen Seiten
- **Accessibility**: Respektiert `prefers-reduced-motion` für Barrierefreiheit
- **Performance**: Natives Browser-Feature ohne externe Bibliotheken

#### 6. Performance-Optimierungen

- Statische Site-Generierung für beste Performance
- Asset-Komprimierung (HTML, CSS, JS)
- Lazy Loading für Bilder
- CSS Code-Splitting
- Optimierte Font-Loading-Strategie
- Native View Transitions (kein JavaScript-Overhead)

## Seiten

Die Website umfasst folgende Hauptseiten (`src/pages/`):

- **index.astro**: Startseite mit Hero, Features, FAQs
- **ueber-mich.astro**: Über Stefanie Leidel & ihre Qualifikationen
- **termin-buchen.astro**: Anamnesebogen & Zoom-Terminbuchung
- **kontakt.astro**: Kontaktformular
- **danke.astro**: Danke-Seite nach erfolgreicher Formular-Absendung
- **404.astro**: Custom 404-Fehlerseite

Zusätzliche rechtliche Seiten (aus Navigation):

- Impressum, Datenschutz, AGB, Widerrufsbelehrung

## Custom Integration

Das Projekt nutzt eine Custom Astro Integration (`integrations/astrowind/`):

- **Zweck**: Lädt die Site-Konfiguration aus `src/config/site.yaml` und stellt sie als virtuelle Module bereit
- **Features**:
  - Hot-Reload bei Änderungen an `site.yaml`
  - Automatische Sitemap-Integration in robots.txt
  - Config-Bereitstellung über Vite Virtual Modules
  - Zugriff auf SITE, I18N, METADATA, UI, ANALYTICS Konfiguration

### Deployment

**Voraussetzungen**:

- **Node.js**: v18+ für Astro Build
- **PHP**: 7.4+ für Backend-API
- **Composer**: Für PHP-Dependencies
- **Webserver**: Apache/Nginx mit PHP-Support
- **HTTPS**: Erforderlich für sichere Datenübertragung (Formulare, Zoom-API)

**Build & Deployment**:

1. Dependencies installieren: `npm install` und `composer install`
2. Build erstellen: `npm run build`
3. `dist/` Ordner auf Webserver deployen
4. Sicherstellen dass `public/api/` PHP-Dateien ausführbar sind
5. `.env` Datei mit Credentials konfigurieren (Zoom, E-Mail, etc.)

## Lizenz

Dieses Projekt basiert auf dem AstroWind Template und steht unter der MIT-Lizenz.
