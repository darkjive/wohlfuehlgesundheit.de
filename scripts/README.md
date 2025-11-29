# Scripts Directory

Automatisierungs-Scripts für Maintenance und Deployment.

## 📁 Verfügbare Scripts

### `fetch-instagram.sh`

Holt aktuelle Instagram-Posts und generiert JSON-Feed.

**Verwendung:**
```bash
./scripts/fetch-instagram.sh
# oder
npm run instagram:fetch
```

**Was es macht:**
1. Prüft Python-Installation
2. Installiert reelscraper (falls nötig)
3. Führt `instagram_feed.py` aus
4. Generiert `public/data/instagram-feed.json`

---

### `check-instagram-feed.sh`

Healthcheck für Instagram-Feed (Validierung).

**Verwendung:**
```bash
./scripts/check-instagram-feed.sh
# oder
npm run instagram:check
```

**Was es prüft:**
- ✅ Feed-Datei existiert
- ✅ Feed ist nicht älter als 48 Stunden
- ✅ Feed enthält Posts
- ✅ Metadaten sind korrekt

**Beispiel-Output:**
```
✅ Feed is healthy!
   Username: @wohl_fuehl_gesundheit
   Posts: 12
   Age: 3 hours
   Last fetched: 2025-11-29T09:00:00.000Z
```

---

## 🔧 Berechtigungen

Alle Scripts sind bereits ausführbar. Falls nötig:

```bash
chmod +x scripts/*.sh
```

---

## 📝 npm-Integration

Diese Scripts sind in `package.json` integriert:

```json
{
  "scripts": {
    "instagram:fetch": "bash scripts/fetch-instagram.sh",
    "instagram:check": "bash scripts/check-instagram-feed.sh"
  }
}
```

---

## 🤖 Automatisierung

### Cron (Garuda Linux)

```bash
# Täglich um 9:00 Uhr Feed abrufen
crontab -e

# Zeile hinzufügen:
0 9 * * * cd /home/user/wohlfuehlgesundheit.de && ./scripts/fetch-instagram.sh >> /tmp/instagram-feed.log 2>&1
```

### Systemd Timer (empfohlen)

Siehe `api/IONOS_SETUP.md` für Systemd-Timer-Konfiguration.

---

## 📖 Weitere Dokumentation

Vollständige Anleitung: [api/IONOS_SETUP.md](../api/IONOS_SETUP.md)
