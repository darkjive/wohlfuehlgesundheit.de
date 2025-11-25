# Stefanie Leidel - Holistische Darmtherapie

Website für Holistische Darmtherapie, entwickelt mit Astro und Tailwind CSS.

## Projektstruktur

```
/
├── integrations/          # Custom Astro Integrations
├── public/
│   ├── api/              # Backend APIs
│   └── vendor/           # Dependencies
├── src/
│   ├── assets/
│   ├── components/
│   │   ├── common/       # Gemeinsame Komponenten
│   │   ├── ui/           # UI Komponenten
│   │   └── widgets/      # Widget Komponenten
│   ├── config/           # Konfigurationsdateien
│   ├── layouts/
│   ├── pages/
│   └── utils/
├── composer.json
├── package.json
├── astro.config.ts
└── tailwind.config.js
```

## Installation und Entwicklung

Alle Befehle werden im Hauptverzeichnis des Projekts ausgeführt:

| Befehl              | Aktion                                            |
| ------------------- | ------------------------------------------------- |
| `npm install`       | Installiert Abhängigkeiten                        |
| `npm run dev`       | Startet den Entwicklungsserver auf localhost:4321 |
| `npm run build`     | Erstellt die produktionsreife Website in ./dist/  |
| `npm run preview`   | Vorschau der gebauten Website vor dem Deployment  |
| `npm run check`     | Überprüft das Projekt auf Fehler                  |
| `npm run fix`       | Führt ESLint aus und formatiert Code mit Prettier |
| `npm run astro ...` | Führt Astro CLI-Befehle aus                       |

## Konfiguration

Die Hauptkonfigurationsdatei befindet sich unter `./src/config/site.yaml` und enthält Site-Metadaten, SEO-Einstellungen und UI-Konfiguration.

## Anpassungen

### Styling

Für Anpassungen der Schriftarten, Farben oder anderen Design-Elementen:

- `src/components/CustomStyles.astro`
- `src/assets/styles/tailwind.css`

### Inhalte

- **Seiten**: `src/pages/` - Alle Astro-Seiten
- **Komponenten**: `src/components/` - Wiederverwendbare Komponenten
- **Konfiguration**: `src/config/` - Site- und Navigation-Konfiguration

## Technische Details

### Frontend

- **Framework**: Astro (Static Site Generator)
- **Styling**: Tailwind CSS mit Typography Plugin
- **Programmiersprache**: TypeScript
- **Schriftarten**: Noto Serif Display, Quicksand (Variable Fonts)

### Backend

- PHP Backend für Formular-Verarbeitung
- E-Mail-Benachrichtigungen
- DSGVO-konforme Datenschutzfeatures

### Besondere Features

- Online-Terminbuchungssystem
- Umfangreicher Anamnesebogen
- Dark Mode Support
- DSGVO-konforme Cookie-Verwaltung
- Performance-Optimierungen

## Deployment

Das Projekt wird automatisch über GitHub Actions zu IONOS deployed, sobald Änderungen in den master-Branch gepusht werden.

**Wichtig**: IONOS erlaubt nur eine SFTP-Verbindung gleichzeitig. Stellen Sie sicher, dass keine lokalen SFTP-Verbindungen aktiv sind, wenn das automatische Deployment läuft.

## Lizenz

Dieses Projekt basiert auf dem AstroWind Template und steht unter der MIT-Lizenz.
