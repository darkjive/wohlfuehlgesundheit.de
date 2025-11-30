# Holistische Darmtherapie – Website-Projekt

Website für eine holistische Darmtherapie-Praxis, entwickelt mit **Astro** und **Tailwind CSS**.

---

## Projektstruktur

```text
/
├── integrations/          # Benutzerdefinierte Astro-Integrationen
│   └── wohlfuehlgesundheit/  # Eigene Config-Integration
│       ├── index.ts
│       ├── types.d.ts
│       └── utils/
├── public/
│   ├── api/               # PHP-Backend-APIs
│   ├── vendor/            # PHP-Composer-Abhängigkeiten
│   ├── _headers           # HTTP-Header
│   ├── .htaccess          # Server-Konfiguration
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
│   │   ├── ui/           # UI-Komponenten
│   │   │   ├── Button.astro
│   │   │   ├── FormContact.astro
│   │   │   └── ...
│   │   ├── widgets/      # Widget-Komponenten
│   │   │   ├── anamnese-form/    # Anamnesebogen-Komponenten
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

| Befehl                    | Aktion                                                         |
| ------------------------- | -------------------------------------------------------------- |
| `npm install`             | Installiert Abhängigkeiten                                     |
| `npm start`               | **Empfohlen**: Startet PHP + Astro Dev-Server (localhost:4321) |
| `npm run dev:full`        | Gleich wie `npm start` – startet beide Server                  |
| `npm run dev`             | Startet nur Astro Dev-Server (localhost:4321) – **ohne PHP**   |
| `npm run dev:php`         | Startet nur PHP Dev-Server (localhost:8000)                    |
| `npm run build`           | Erstellt die produktionsreife Website in `./dist/`             |
| `npm run instagram:fetch` | Holt aktuelle Instagram-Posts und generiert JSON-Feed          |
| `npm run preview`         | Vorschau der gebauten Website vor dem Deployment               |
| `npm run check`           | Überprüft das Projekt auf Fehler                               |
| `npm run fix`             | Führt ESLint aus und formatiert Code mit Prettier              |
| `npm run astro ...`       | Führt Astro-CLI-Befehle aus                                    |
| `npm run deploy`          | Führt Deployment-Befehle aus                                   |

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

## Konfiguration

Die Hauptkonfigurationsdatei befindet sich unter `./src/config/site.yaml`:

```yaml
name: ''
site: 'https://BEISPIEL-URL.de'
base: '/'
trailingSlash: false
metadata:
  title:
    default: ''
    template: '%s | BEISPIEL-TITEL'
  description: 'BEISPIEL-BESCHREIBUNG'
  robots:
    index: true
    follow: true
  openGraph:
    site_name: 'BEISPIEL-OPENGRAPH-NAME'
    images:
      - url: '~/assets/images/default.png'
        width: 1200
        height: 628
    type: website
  twitter:
    handle: '@BEISPIEL-TWITTER'
    site: '@BEISPIEL-TWITTER'
    cardType: summary_large_image
ui:
  theme: 'system' # "system" | "light" | "dark"
```

---

## Anpassungen

### Styling

Für Anpassungen der Schriftarten, Farben oder anderen Design-Elementen:

- `src/components/CustomStyles.astro`
- `src/assets/styles/tailwind.css`

### Inhalte

- **Seiten**:
  `src/pages/` – Alle Astro-Seiten (index, über-mich, termin-buchen, kontakt, etc.)
- **Komponenten**: `src/components/` – Wiederverwendbare Komponenten
  - `common/` – Gemeinsame Komponenten (Header, Footer, Logos)
  - `ui/` – UI-Komponenten (Button, Forms, etc.)
  - `widgets/` – Komplexe Widgets (Hero, Features, etc.)
- **Konfiguration**: `src/config/` – Site- und Navigation-Konfiguration
- **API**: `public/api/` – PHP-Backend für Formulare und Zoom-Integration

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
  - Anamnesebogen & Zoom-Buchung
  - Kontaktformular-Verarbeitung
  - E-Mail-Versand
  - Sichere Konfigurationsverwaltung

- **Zoom-Integration**:
  - Server-to-Server OAuth 2.0 Authentifizierung
  - Automatische Meeting-Erstellung via Zoom API
  - E-Mail-Benachrichtigungen mit Zoom-Zugangsdaten
  - Terminverwaltung & Kalender-Integration (ICS)
  - Warteraum-Funktion für erhöhte Sicherheit

- **Sicherheitsfeatures**:
  - Umfassende Formular-Validierung
  - Spam-Schutz
  - Sichere Datenübertragung

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

- **Google Analytics**: [ID anonymisiert]
- **Google Site Verification**: [ID anonymisiert]
- **OpenGraph & Twitter Cards**: Vollständige Social-Media-Integration
- **Robots.txt**: Suchmaschinen-Steuerung

---

## Seiten

Die Website umfasst folgende Hauptseiten (`src/pages/`):

- **index.astro**: Startseite mit Hero, Features, FAQs
- **ueber-mich.astro**: Über die Therapeutin & ihre Qualifikationen
- **termin-buchen.astro**: Anamnesebogen & Zoom-Terminbuchung
- **kontakt.astro**: Kontaktformular
- **danke.astro**: Danke-Seite nach erfolgreicher Formular-Absendung
- **404.astro**: Custom 404-Fehlerseite

Zusätzliche rechtliche Seiten (aus Navigation):

- Impressum, Datenschutz, AGB, Widerrufsbelehrung

---

## Besondere Features

### 1. Zoom-Booking-System

Vollautomatisches Terminbuchungssystem mit:

- Zoom-Meeting-Erstellung via API
- Automatische E-Mail-Benachrichtigungen (HTML für Kunden, Text für Admin)
- Terminvalidierung und Zeitzonenmanagement (Europe/Berlin)
- Warteraum-Funktion für mehr Sicherheit
- Flexible Terminlängen (20/40 Minuten)

### 2. Instagram Feed Integration

Automatisches Instagram-Feed-System mit:

- Python-Backend (reelscraper) zum Abrufen von Instagram-Posts
- Statisches JSON-Feed für optimale Performance
- Responsive Grid-Darstellung mit Hover-Effekten
- Media-Type-Indikatoren (Bild, Video, Karussell)
- Automatische Build-Integration
- IONOS-Server-Support mit Cron-Jobs

**Verwendung:**

```bash
# Instagram-Feed manuell abrufen
npm run instagram:fetch

# Automatisch beim Build (bereits integriert)
npm run build
```

**Setup auf IONOS-Server:**
Siehe `api/IONOS_SETUP.md` für detaillierte Anweisungen zur Installation und Automatisierung mit Cron-Jobs.

**Komponente:**

- `src/components/widgets/InstagramFeed.astro` - Widget-Komponente
- `api/instagram_feed.py` - Python-Scraper
- `public/data/instagram-feed.json` - Generiertes JSON-Feed

### 3. Umfangreicher Anamnesebogen

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
- Auto-Save-Funktionalität (LocalStorage)
- Responsive Design für mobile & Desktop
- Direkte Integration mit Zoom-API für Terminbuchung

#### 4. Dark Mode

- System-Theme-Erkennung
- Manueller Light/Dark Mode Toggle
- Persistente Theme-Speicherung

#### 5. Cookie Consent Management

- DSGVO-konform
- Kategorien: Notwendig, Statistik, Marketing
- Vollständig auf Deutsch lokalisiert
- Anpassbare Einstellungen

#### 6. View Transitions

Die Website nutzt Astro's native View Transitions API:

- **Sanfte Seitenübergänge**: Nahtlose Übergänge zwischen Seiten
- **Accessibility**: Respektiert `prefers-reduced-motion` für Barrierefreiheit
- **Performance**: Natives Browser-Feature ohne externe Bibliotheken

#### 7. Performance-Optimierungen

- Statische Site-Generierung für beste Performance
- Asset-Komprimierung (HTML, CSS, JS)
- Lazy Loading für Bilder
- CSS Code-Splitting
- Optimierte Font-Loading-Strategie
- Native View Transitions (kein JavaScript-Overhead)

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

1. Dependencies installieren: `npm install` und `composer install`
2. Build erstellen: `npm run build`
3. `dist/` Ordner auf Webserver deployen
4. Sicherstellen, dass `public/api/` PHP-Dateien ausführbar sind
5. `.env`-Datei mit Credentials konfigurieren (Zoom, E-Mail, etc.)

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
