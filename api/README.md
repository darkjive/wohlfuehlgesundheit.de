# API Directory

Dieses Verzeichnis enthält Backend-APIs und Python-Scripts für die Website.

## 📁 Struktur

```
api/
├── instagram_feed.py      # Instagram-Scraper (Python 3.7+)
├── requirements.txt       # Python-Dependencies
├── IONOS_SETUP.md        # Vollständige Setup-Anleitung
└── README.md             # Diese Datei
```

## 🚀 Quick Start

### Instagram Feed abrufen

```bash
# Via npm (empfohlen)
npm run instagram:fetch

# Direkt mit Python
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12

# Feed-Gesundheit prüfen
npm run instagram:check
```

## 📖 Dokumentation

Die vollständige Anleitung zur Instagram-Feed-Integration findest du hier:

👉 **[IONOS_SETUP.md](./IONOS_SETUP.md)**

Diese Anleitung enthält:
- ✅ Garuda Linux Setup (lokale Entwicklung)
- ✅ IONOS Server Setup (Production)
- ✅ Troubleshooting & Fehlersuche
- ✅ Monitoring & Logging
- ✅ Best Practices & DSGVO

## 🔧 Dependencies

```bash
# Installation
pip install -r api/requirements.txt
```

Benötigt:
- Python 3.7+
- reelscraper >= 1.0.0

## 📝 Scripts

| Script | Beschreibung |
|--------|-------------|
| `instagram_feed.py` | Scraped Instagram-Posts und generiert JSON-Feed |

## 🌐 Output

Generiertes JSON-Feed:
```
public/data/instagram-feed.json
```

Format:
```json
{
  "username": "wohl_fuehl_gesundheit",
  "posts": [
    {
      "id": "...",
      "caption": "...",
      "mediaUrl": "...",
      "permalink": "...",
      "timestamp": "..."
    }
  ],
  "fetchedAt": "2025-11-29T...",
  "postsCount": 12
}
```

## 🔒 Sicherheit

- ✅ Keine API-Keys erforderlich
- ✅ Nur öffentliche Instagram-Daten
- ✅ Read-only Zugriff
- ✅ DSGVO-konform

## 📞 Support

Bei Fragen oder Problemen:
1. [IONOS_SETUP.md](./IONOS_SETUP.md) → Fehlersuche-Sektion
2. GitHub Issues: https://github.com/darkjive/wohlfuehlgesundheit.de/issues
