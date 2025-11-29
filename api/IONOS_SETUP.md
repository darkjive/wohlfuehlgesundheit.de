# Instagram Feed Setup für IONOS Server

## Installation auf IONOS

### 1. Python-Umgebung einrichten

```bash
# SSH in deinen IONOS Server
ssh benutzer@deine-domain.de

# Navigiere zum Projektverzeichnis
cd /pfad/zu/wohlfuehlgesundheit.de

# Installiere reelscraper
pip3 install reelscraper
# oder mit requirements.txt:
pip3 install -r api/requirements.txt
```

### 2. Instagram Feed manuell abrufen

```bash
# Generiere das Instagram-Feed JSON
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12 --output public/data/instagram-feed.json
```

### 3. Automatisierung mit Cron-Job

Erstelle einen Cron-Job, um das Feed automatisch zu aktualisieren:

```bash
# Cron-Editor öffnen
crontab -e

# Füge folgende Zeile hinzu (täglich um 6:00 Uhr):
0 6 * * * cd /pfad/zu/wohlfuehlgesundheit.de && /usr/bin/python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12 --output public/data/instagram-feed.json >> /var/log/instagram-feed.log 2>&1
```

Alternative Cron-Zeitpläne:
- Alle 12 Stunden: `0 */12 * * *`
- Alle 6 Stunden: `0 */6 * * *`
- Zweimal täglich (6:00 und 18:00): `0 6,18 * * *`

### 4. Berechtigungen prüfen

```bash
# Stelle sicher, dass das Script ausführbar ist
chmod +x api/instagram_feed.py

# Stelle sicher, dass das public/data Verzeichnis beschreibbar ist
chmod 755 public/data
```

### 5. htaccess-Konfiguration (Optional)

Wenn du das JSON-Feed über eine API-Route zugänglich machen möchtest:

```apache
# In .htaccess hinzufügen:
<FilesMatch "instagram-feed\.json$">
    Header set Access-Control-Allow-Origin "*"
    Header set Cache-Control "max-age=3600, public"
</FilesMatch>
```

## Verwendung

### Manueller Abruf
```bash
npm run instagram:fetch
```

### Build-Prozess (mit automatischem Fetch)
```bash
npm run build
```

### Nur Instagram-Feed abrufen
```bash
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12
```

## Fehlersuche

### Problem: `ModuleNotFoundError: No module named 'reelscraper'`
**Lösung:** Installiere reelscraper:
```bash
pip3 install reelscraper
```

### Problem: Keine Posts werden abgerufen
**Lösung:**
1. Überprüfe die Instagram-Privatsphäre-Einstellungen (Account muss öffentlich sein)
2. Überprüfe den Username (ohne @)
3. Schaue in die Logs für detaillierte Fehlermeldungen

### Problem: JSON-Datei wird nicht generiert
**Lösung:**
1. Überprüfe Schreibrechte: `chmod 755 public/data`
2. Erstelle das Verzeichnis manuell: `mkdir -p public/data`

## Monitoring

Logdatei prüfen:
```bash
tail -f /var/log/instagram-feed.log
```

JSON-Feed prüfen:
```bash
cat public/data/instagram-feed.json
```

## Sicherheit

- ⚠️ Das JSON-Feed enthält öffentliche Instagram-Daten
- ✅ Keine sensiblen Daten oder API-Keys erforderlich
- ✅ Read-only Zugriff auf öffentliche Instagram-Profile
- ✅ DSGVO-konform (öffentliche Daten)

## Python Version

- Mindestversion: Python 3.7+
- IONOS Server: Python 3.13.7 ✅
