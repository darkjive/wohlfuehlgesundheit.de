# Wohlfühlgesundheit - Holistische Darmtherapie

Moderne, performante Website für eine holistische Darmtherapie-Praxis mit integriertem Buchungssystem und Zoom-Meeting-Integration.

## Technologie-Stack

- **Frontend**: Astro 5.x (Static Site Generator)
- **Styling**: TailwindCSS 3.x mit Typography Plugin
- **Backend**: PHP 8.2+ mit Composer
- **Programmiersprache**: TypeScript
- **Externe Integrationen**: Zoom Meeting API, PHPMailer
- **Deployment**: Statische Website mit PHP-API-Backend

## Hauptfunktionen

✅ **Zoom-Terminbuchung**: Vollautomatisches Buchungssystem mit Meeting-Erstellung
✅ **Mehrstufiger Anamnesebogen**: 10-Schritte-Formular mit Auto-Save
✅ **Dark Mode**: System-Theme-Erkennung & manueller Toggle
✅ **DSGVO-konform**: Cookie Consent Management & Datenschutz
✅ **SEO-optimiert**: Meta-Tags, Sitemap, strukturierte Daten
✅ **Performance**: Statische Generierung, Asset-Komprimierung, Lazy Loading
✅ **Sicherheit**: CSRF-Schutz, Input-Validierung, sichere Datenübertragung
✅ **Barrierearm**: WCAG 2.2 Richtlinien, View Transitions mit reduced-motion Support

---

## Projektstruktur

```text
/
├── integrations/          # Benutzerdefinierte Astro-Integrationen
│   └── wohlfuehlgesundheit/  # Eigene Config-Integration
│       ├── index.ts
│       ├── types.d.ts
│       └── utils/
├── public/                # Öffentliche Assets (werden 1:1 kopiert)
│   ├── api/               # PHP-Backend-APIs
│   │   ├── anamnese-booking.php
│   │   ├── contact-form.php
│   │   ├── get-csrf-token.php
│   │   ├── bootstrap.php
│   │   ├── config.php
│   │   ├── env-loader.php
│   │   ├── phpmailer-helper.php
│   │   ├── security.php
│   │   ├── .htaccess
│   │   └── .env.example
│   ├── vendor/            # PHP-Composer-Abhängigkeiten
│   ├── _headers           # HTTP-Security-Header (Netlify/Cloudflare)
│   ├── .htaccess          # Apache-Server-Konfiguration
│   └── robots.txt         # Suchmaschinen-Steuerung
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
│   │   │   ├── Analytics.astro
│   │   │   ├── ApplyColorMode.astro
│   │   │   ├── CommonMeta.astro
│   │   │   ├── Image.astro
│   │   │   ├── ImageSwap.astro
│   │   │   ├── Metadata.astro
│   │   │   └── SiteVerification.astro
│   │   ├── ui/           # UI-Komponenten
│   │   │   ├── Background.astro
│   │   │   ├── Button.astro
│   │   │   ├── CollapsibleSection.astro
│   │   │   ├── Content.astro
│   │   │   ├── Form.astro
│   │   │   ├── Headline.astro
│   │   │   ├── ItemGrid.astro
│   │   │   ├── Timeline.astro
│   │   │   ├── ToggleMenu.astro
│   │   │   ├── ToggleTheme.astro
│   │   │   └── WidgetWrapper.astro
│   │   ├── widgets/      # Widget-Komponenten
│   │   │   ├── booking-form/    # Buchungsformular-Komponenten
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
│   │   │   ├── BookingForm.astro
│   │   │   ├── CallToAction.astro
│   │   │   ├── Complaints.astro
│   │   │   ├── ContactForm.astro
│   │   │   ├── FAQs.astro
│   │   │   ├── Features.astro
│   │   │   ├── Hero.astro
│   │   │   ├── HeroSimple.astro
│   │   │   ├── Quote.astro
│   │   │   ├── Steps.astro
│   │   │   └── Testimonials.astro
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
│   │   ├── termin-buchen.astro
│   │   ├── kontakt.astro
│   │   ├── 404.astro
│   │   ├── agb.md
│   │   ├── datenschutz.md
│   │   ├── impressum.md
│   │   └── widerrufsbelehrung.md
│   ├── utils/            # Hilfsfunktionen
│   │   ├── images.ts
│   │   ├── images-optimization.ts
│   │   ├── permalinks.ts
│   │   └── utils.ts
│   ├── env.d.ts
│   └── types.d.ts
├── composer.json         # PHP-Abhängigkeiten
├── package.json
├── astro.config.ts
└── tailwind.config.js
```

---

## Installation und Entwicklung

### Befehle

| Befehl              | Aktion                                                         |
| ------------------- | -------------------------------------------------------------- |
| `npm install`       | Installiert Abhängigkeiten                                     |
| `npm start`         | **Empfohlen**: Startet PHP + Astro Dev-Server (localhost:4321) |
| `npm run dev:full`  | Gleich wie `npm start` – startet beide Server                  |
| `npm run dev`       | Startet nur Astro Dev-Server (localhost:4321) – **ohne PHP**   |
| `npm run dev:php`   | Startet nur PHP Dev-Server (localhost:8000)                    |
| `npm run build`     | Erstellt die produktionsreife Website in `./dist/`             |
| `npm run preview`   | Vorschau der gebauten Website vor dem Deployment               |
| `npm run check`     | Überprüft das Projekt auf Fehler                               |
| `npm run fix`       | Führt ESLint aus und formatiert Code mit Prettier              |
| `npm run astro ...` | Führt Astro-CLI-Befehle aus                                    |
| `npm run deploy`    | Führt Deployment-Befehle aus                                   |

---

### Lokale Entwicklung mit PHP-Backend

**Wichtig**: Diese Website nutzt PHP für Backend-APIs (Formulare, CSRF-Token, Zoom-Integration). Für die lokale Entwicklung müssen **beide Server** laufen:

1. **PHP-Development-Server** (Port 8000) – führt PHP-Dateien aus
2. **Astro-Development-Server** (Port 4321) – dient die Website

**Empfohlener Start:**

```bash
npm start
# oder
npm run dev:full
```

Dies startet automatisch beide Server. Die Website ist dann unter `http://localhost:4321` erreichbar und alle `/api/*`-Anfragen werden automatisch an den PHP-Server weitergeleitet.

**Manuelle Variante** (in separaten Terminals):

```bash
# Terminal 1: PHP-Server starten
npm run dev:php
# Terminal 2: Astro-Server starten
npm run dev
```

---

## Code-Qualität & Best Practices

Das Projekt nutzt verschiedene Tools zur Sicherstellung der Code-Qualität:

### Linting & Formatting

- **ESLint**: TypeScript/JavaScript Linting mit Astro-Support
- **Prettier**: Code-Formatierung mit Astro- und Tailwind-Plugins
- **TypeScript**: Strikte Typisierung für bessere Code-Qualität

### Verfügbare Befehle

```bash
# Projekt auf Fehler überprüfen
npm run check

# Nur Astro-Dateien prüfen
npm run check:astro

# Nur ESLint ausführen
npm run check:eslint

# Nur Prettier prüfen
npm run check:prettier

# Code automatisch formatieren
npm run fix
# oder
npm run format
```

### Best Practices

- **KISS-Prinzip**: Keep It Simple, Stupid - kein Over-Engineering
- **Moderne ECMA-Syntax**: ES2020+ Features nutzen
- **TypeScript**: Strikte Typisierung wo sinnvoll
- **Komponenten-basiert**: Wiederverwendbare, modulare Komponenten
- **Performance First**: Optimierung für schnelle Ladezeiten
- **Accessibility**: WCAG 2.2 Richtlinien beachten
- **SEO-Optimierung**: Meta-Tags, strukturierte Daten, semantisches HTML
- **Sicherheit**: OWASP Top 10 beachten, Input-Validierung, CSRF-Schutz

---

## Konfiguration

Die Hauptkonfigurationsdatei befindet sich unter `./src/config/site.yaml`:

```yaml
site:
  name: 'Wohlfühlgesundheit - Holistische Darmtherapie'
  site: 'https://wohlfühlgesundheit.de'
  base: '/'
  trailingSlash: false
  googleSiteVerificationId: am4o936StJOXZfUTSRB262VSHDHaoPfc-ZImQ8Qoxkw

metadata:
  title:
    default: 'Wohlfühlgesundheit - Holistische Darmtherapie'
    template: '%s | Wohlfühlgesundheit - Holistische Darmtherapie'
  description: 'Wohlfühlgesundheit - Holistische Darmtherapie, Deine Holistische Darmtherapeutin. Entdecke Tipps und Programme zur Verbesserung deiner Gesundheit und deines Wohlbefindens.'
  robots:
    index: true
    follow: true
  openGraph:
    site_name: 'Wohlfühlgesundheit - Holistische Darmtherapie'
    images:
      - url: '~/assets/images/default.png'
        width: 1200
        height: 628
    type: website
  twitter:
    handle: '@onwidget'
    site: '@onwidget'
    cardType: summary_large_image

i18n:
  language: de
  textDirection: ltr

analytics:
  vendors:
    googleAnalytics:
      id: G-TT6VB0HM46

ui:
  theme: 'system' # Values: "system" | "light" | "dark" | "light:only" | "dark:only"
```

---

## Anpassungen

### Styling

Für Anpassungen der Schriftarten, Farben oder anderen Design-Elementen:

- `src/components/CustomStyles.astro`
- `src/assets/styles/tailwind.css`

### Inhalte

- **Seiten**:
  `src/pages/` – Alle Astro-Seiten (index, über-mich, termin-buchen, kontakt, rechtliche Seiten)
- **Komponenten**: `src/components/` – Wiederverwendbare Komponenten
  - `common/` – Gemeinsame Komponenten (Header, Footer, Logos, Meta-Tags, Analytics)
  - `ui/` – UI-Komponenten (Button, Form, Timeline, CollapsibleSection, ToggleTheme, etc.)
  - `widgets/` – Komplexe Widgets
    - `booking-form/` – 10-stufiges Buchungsformular
    - Weitere Widgets: Hero, HeroSimple, Features, Testimonials, Steps, FAQs, Quote, CallToAction, Complaints, ContactForm
- **Konfiguration**: `src/config/` – Site- und Navigation-Konfiguration
- **API**: `public/api/` – PHP-Backend für Formulare und Zoom-Integration
- **Utils**: `src/utils/` – Hilfsfunktionen für Bilder, Permalinks, allgemeine Utilities

---

## Deployment

### Manuelles Deployment

**Voraussetzungen**:

- **Node.js**: v18+ für Astro Build
- **PHP**: 7.4+ für Backend-API
- **Composer**: Für PHP-Dependencies
- **Webserver**: Apache/Nginx mit PHP-Support
- **HTTPS**: Erforderlich für sichere Datenübertragung

1. Produktionsbuild erstellen:

   ```bash
   npm run build
   ```

2. Der `dist/` Ordner enthält alle statischen Dateien für das Deployment.
3. Deployment starten:

   ```bash
   npm run deploy
   ```

---

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

Die Website nutzt ein benutzerdefiniertes PHP-Backend für die Formular-Verarbeitung und externe Integrationen:

- **PHP API** (`public/api/`):
  - `anamnese-booking.php` - Anamnesebogen & Zoom-Terminbuchung
  - `contact-form.php` - Kontaktformular-Verarbeitung
  - `get-csrf-token.php` - CSRF-Token-Generierung
  - `bootstrap.php` - API-Bootstrap & Initialisierung
  - `config.php` - Zentrale Konfigurationsverwaltung
  - `env-loader.php` - Sichere .env-Datei-Verwaltung
  - `phpmailer-helper.php` - E-Mail-Versand-Hilfsfunktionen
  - `security.php` - Sicherheitsfunktionen (CSRF, Validierung, Sanitization)
  - `.htaccess` - Apache-Sicherheitskonfiguration
  - `.env.example` - Beispiel-Umgebungsvariablen

- **Zoom-Integration**:
  - Server-to-Server OAuth 2.0 Authentifizierung
  - Automatische Meeting-Erstellung via Zoom API
  - E-Mail-Benachrichtigungen mit Zoom-Zugangsdaten
  - Terminverwaltung & Kalender-Integration (ICS)
  - Warteraum-Funktion für erhöhte Sicherheit

- **Sicherheitsfeatures**:
  - CSRF-Token-Validierung für alle Formulare
  - Server-seitige Input-Validierung und Sanitization
  - Rate-Limiting und Spam-Schutz
  - Sichere Datenübertragung (HTTPS erforderlich)
  - `.htaccess`-basierte Sicherheitsregeln
  - Umgebungsvariablen-Management via `.env`
  - Sichere Passwort-Hashing und Session-Management

### Integrationen & Plugins

- `@astrojs/sitemap`: XML-Sitemap-Generierung
- `@astrojs/partytown`: Optimierung von Third-Party-Scripts
- `@jop-software/astro-cookieconsent`: DSGVO-konforme Cookie-Verwaltung
- `@astrolib/analytics`: Analytics-Integration
- `@astrolib/seo`: SEO-Optimierung
- `astro-compress`: Asset-Komprimierung (CSS, HTML, JS)
- `astro-embed`: Embed-Support für externe Inhalte
- `astro-emoji`: Emoji-Support

### Analytics & SEO

- **Google Analytics**: Konfiguriert via `site.yaml` (Analytics ID: G-TT6VB0HM46)
- **Google Site Verification**: Konfiguriert via `site.yaml`
- **OpenGraph & Twitter Cards**: Vollständige Social-Media-Integration
- **Robots.txt & Sitemap**: Automatische Generierung via Astro-Sitemap-Plugin
- **SEO-Optimierung**: Meta-Tags, strukturierte Daten, canonical URLs

---

## Seiten

Die Website umfasst folgende Hauptseiten (`src/pages/`):

- **index.astro**: Startseite mit Hero, Features, Testimonials, Steps, FAQs
- **ueber-mich.astro**: Über die Therapeutin & ihre Qualifikationen
- **termin-buchen.astro**: Anamnesebogen & Zoom-Terminbuchung
- **kontakt.astro**: Kontaktformular
- **404.astro**: Custom 404-Fehlerseite

Rechtliche Seiten (Markdown):

- **impressum.md**: Impressum
- **datenschutz.md**: Datenschutzerklärung (DSGVO-konform)
- **agb.md**: Allgemeine Geschäftsbedingungen
- **widerrufsbelehrung.md**: Widerrufsbelehrung

---

## Besondere Features

### 1. Zoom-Booking-System

Vollautomatisches Terminbuchungssystem mit:

- Zoom-Meeting-Erstellung via API
- Automatische E-Mail-Benachrichtigungen (HTML für Kunden, Text für Admin)
- Terminvalidierung und Zeitzonenmanagement (Europe/Berlin)
- Warteraum-Funktion für mehr Sicherheit
- Flexible Terminlängen (20/40 Minuten)

### 2. Umfangreiches Buchungsformular

Mehrstufiges Formular mit 10 detaillierten Schritten (`src/components/widgets/booking-form/`):

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
- Auto-Save-Funktionalität (LocalStorage)
- Responsive Design für mobile & Desktop
- Direkte Integration mit Zoom-API für Terminbuchung

### 3. Dark Mode

- System-Theme-Erkennung
- Manueller Light/Dark Mode Toggle
- Persistente Theme-Speicherung
- Nahtlose Integration in alle Komponenten

### 4. Cookie Consent Management

- DSGVO-konform
- Kategorien: Notwendig, Statistik, Marketing
- Vollständig auf Deutsch lokalisiert
- Anpassbare Einstellungen

### 5. View Transitions

Die Website nutzt Astro's native View Transitions API:

- **Sanfte Seitenübergänge**: Nahtlose Übergänge zwischen Seiten
- **Accessibility**: Respektiert `prefers-reduced-motion` für Barrierefreiheit
- **Performance**: Natives Browser-Feature ohne externe Bibliotheken

### 6. Performance-Optimierungen

- Statische Site-Generierung für beste Performance
- Asset-Komprimierung (HTML, CSS, JS)
- Lazy Loading für Bilder
- CSS Code-Splitting
- Optimierte Font-Loading-Strategie
- Native View Transitions (kein JavaScript-Overhead)

---

## Widget-Komponenten

Die Website nutzt folgende wiederverwendbare Widget-Komponenten (`src/components/widgets/`):

### Content Widgets

- **Hero.astro**: Haupt-Hero-Sektion für die Startseite mit großem Bild, Titel und CTA
- **HeroSimple.astro**: Vereinfachte Hero-Sektion für Unterseiten
- **Features.astro**: Feature-Grid zur Darstellung von Dienstleistungen
- **Steps.astro**: Schritt-für-Schritt-Prozessdarstellung (z.B. Ablauf der Therapie)
- **Testimonials.astro**: Kunden-Rezensionen und Bewertungen
- **Quote.astro**: Hervorgehobene Zitate oder Statements
- **Complaints.astro**: Darstellung von behandelbaren Beschwerden
- **FAQs.astro**: Häufig gestellte Fragen mit Accordion-Funktionalität
- **CallToAction.astro**: Call-to-Action-Bereiche für Conversion-Optimierung

### Formular Widgets

- **ContactForm.astro**: Kontaktformular mit CSRF-Schutz
- **BookingForm.astro**: Haupt-Wrapper für das mehrstufige Buchungsformular
- **booking-form/** (Unterordner): 10 Schritte des Anamnesebogens

Alle Widgets sind:
- Vollständig responsive
- Dark-Mode-kompatibel
- Barrierearm gestaltet
- Mit TypeScript typisiert

---

## UI-Komponenten

Die Website nutzt folgende wiederverwendbare UI-Komponenten (`src/components/ui/`):

- **Button.astro**: Konfigurierbare Button-Komponente mit verschiedenen Varianten
- **Form.astro**: Formular-Wrapper mit standardisiertem Styling
- **Headline.astro**: Standardisierte Überschriften-Komponente
- **Timeline.astro**: Zeitstrahl-Darstellung für Prozesse oder Ereignisse
- **ItemGrid.astro**: Grid-Layout für Items/Features
- **Content.astro**: Content-Wrapper für Text-Inhalte
- **Background.astro**: Hintergrund-Komponente mit verschiedenen Styles
- **CollapsibleSection.astro**: Ausklappbare Sektionen (Accordion)
- **ToggleTheme.astro**: Dark/Light Mode Umschalter
- **ToggleMenu.astro**: Mobile Navigation Toggle
- **WidgetWrapper.astro**: Standard-Wrapper für alle Widgets

Alle UI-Komponenten:
- Nutzen TailwindCSS für Styling
- Sind vollständig typisiert (TypeScript)
- Unterstützen Props für Anpassungen
- Sind konsistent im Design

---

## Layouts

Die Website nutzt drei Haupt-Layouts (`src/layouts/`):

- **Layout.astro**: Basis-Layout mit HTML-Struktur, Meta-Tags, Scripts
- **PageLayout.astro**: Standard-Seiten-Layout mit Header, Footer, Navigation
- **MarkdownLayout.astro**: Spezialisiertes Layout für Markdown-Seiten (rechtliche Dokumente)

Alle Layouts:
- Nutzen Astro View Transitions
- Integrieren Dark Mode Support
- Enthalten SEO-Meta-Tags
- Sind responsive gestaltet

---

## Custom Integration

Das Projekt nutzt eine benutzerdefinierte Astro-Integration (`integrations/wohlfuehlgesundheit/`):

- **Zweck**: Lädt die Site-Konfiguration aus `src/config/site.yaml` und stellt sie als virtuelle Module bereit
- **Features**:
  - Hot-Reload bei Änderungen an `site.yaml`
  - Automatische Sitemap-Integration in robots.txt
  - Config-Bereitstellung über Vite Virtual Modules
  - Zugriff auf SITE, I18N, METADATA, UI, ANALYTICS-Konfiguration

**Build & Deployment**:

1. **Dependencies installieren**:
   ```bash
   npm install
   composer install
   ```

2. **Umgebungsvariablen konfigurieren**:
   - `.env.example` nach `.env` kopieren
   - Alle erforderlichen Variablen ausfüllen:
     - Zoom API Credentials (Account ID, Client ID, Client Secret)
     - E-Mail-Server-Einstellungen (SMTP)
     - Admin-E-Mail-Adressen
     - Session-Secrets

3. **Build erstellen**:
   ```bash
   npm run build
   ```

4. **Deployment**:
   - `dist/` Ordner auf Webserver deployen
   - Sicherstellen, dass `public/api/` PHP-Dateien ausführbar sind
   - `.env`-Datei im `public/api/` Verzeichnis platzieren
   - PHP 7.4+ und Composer auf Server installieren
   - Apache mit mod_rewrite oder Nginx konfigurieren
   - SSL/TLS-Zertifikat für HTTPS einrichten

---

## TODOs

### Build-Warnung Suppression

⚠️ **Temporäre Workaround aktiv**: In `astro.config.ts` wird derzeit eine Vite-Warnung unterdrückt:

```
"matchHostname", "matchPathname", "matchPort" and "matchProtocol" are imported from external module "@astrojs/internal-helpers/remote" but never used
```

**Ursache**: Dies ist ein bekannter Bug in Astro selbst (siehe [Issue #14752](https://github.com/withastro/astro/issues/14752))

**Fix**: PR [#14876](https://github.com/withastro/astro/pull/14876) behebt das Problem, wurde aber noch nicht gemerged.

**Action**: Sobald der Fix in einer neuen Astro-Version verfügbar ist:

1. Astro auf die neue Version updaten
2. Die `onwarn`-Funktion in `vite.build.rollupOptions` (Zeile 167-180 in `astro.config.ts`) entfernen
3. Diesen TODO-Eintrag löschen

---

## Lizenz

Dieses Projekt steht unter der **MIT-Lizenz**.
