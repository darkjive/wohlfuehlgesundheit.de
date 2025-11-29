# Instagram Feed Setup

Komplette Anleitung zur Instagram-Feed-Integration für lokale Entwicklung (Garuda Linux) und IONOS Server-Deployment.

---

## 📋 Inhaltsverzeichnis

1. [Quick Start](#quick-start)
2. [Lokale Entwicklung (Garuda Linux)](#lokale-entwicklung-garuda-linux)
3. [IONOS Server Setup](#ionos-server-setup)
4. [Verwendung](#verwendung)
5. [Fehlersuche](#fehlersuche)
6. [Monitoring & Logs](#monitoring--logs)
7. [Best Practices](#best-practices)

---

## Quick Start

### Garuda Linux (Lokale Entwicklung)

```bash
# 1. Python-Dependencies installieren
pip install instaloader

# 2. Feed abrufen
npm run instagram:fetch

# 3. Dev-Server starten
npm start

# Website öffnen: http://localhost:4321
```

### IONOS Server (Production)

```bash
# 1. SSH verbinden
ssh benutzer@wohlfuehlgesundheit.de

# 2. Ins Projektverzeichnis
cd /pfad/zu/projekt

# 3. Python-Dependencies installieren
pip3 install -r api/requirements.txt

# 4. Feed initial abrufen
python3 api/instagram_feed.py wohl_fuehl_gesundheit

# 5. Cron-Job einrichten (täglich um 6:00 Uhr)
crontab -e
# Zeile hinzufügen:
# 0 6 * * * cd /pfad/zu/projekt && python3 api/instagram_feed.py wohl_fuehl_gesundheit >> /var/log/instagram-feed.log 2>&1
```

---

## Lokale Entwicklung (Garuda Linux)

### 1. Systemvoraussetzungen prüfen

```bash
# Python-Version prüfen (mindestens 3.7+)
python3 --version

# Falls Python nicht installiert ist (Garuda Linux):
sudo pacman -S python python-pip
```

### 2. Python-Dependencies installieren

**Option A: Mit pip (empfohlen)**
```bash
# Ins Projektverzeichnis wechseln
cd /home/user/wohlfuehlgesundheit.de

# instaloader installieren
pip install instaloader

# Oder mit requirements.txt:
pip install -r api/requirements.txt
```

**Option B: Mit pipenv (isolierte Umgebung)**
```bash
# pipenv installieren (falls nicht vorhanden)
pip install pipenv

# Virtuelle Umgebung erstellen und Dependencies installieren
pipenv install instaloader

# Shell mit virtueller Umgebung starten
pipenv shell
```

**Option C: Mit venv (Standard Python)**
```bash
# Virtuelle Umgebung erstellen
python3 -m venv venv

# Aktivieren
source venv/bin/activate

# Dependencies installieren
pip install -r api/requirements.txt
```

### 3. Instagram-Feed lokal testen

```bash
# Script ausführbar machen
chmod +x api/instagram_feed.py
chmod +x scripts/fetch-instagram.sh

# Feed manuell abrufen (Python direkt)
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12

# Oder mit npm-Script:
npm run instagram:fetch
```

### 4. Feed-Daten prüfen

```bash
# JSON-Datei anschauen
cat public/data/instagram-feed.json

# Oder mit jq (für bessere Formatierung):
sudo pacman -S jq
cat public/data/instagram-feed.json | jq
```

### 5. Build-Prozess testen

```bash
# Build mit automatischem Instagram-Fetch
npm run build

# Vorschau der gebauten Seite
npm run preview
```

### 6. Lokale Entwicklung starten

```bash
# Dev-Server mit PHP + Astro
npm start

# Website öffnen: http://localhost:4321
# Instagram-Feed sollte auf der Startseite sichtbar sein
```

### 7. Automatisierung für lokale Entwicklung (optional)

**Cronjob für automatische Updates (Garuda Linux):**

```bash
# Crontab öffnen
crontab -e

# Feed täglich um 9:00 Uhr abrufen (lokale Entwicklung)
0 9 * * * cd /home/user/wohlfuehlgesundheit.de && /usr/bin/python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12 --output public/data/instagram-feed.json >> /tmp/instagram-feed.log 2>&1
```

**Systemd Timer (moderne Alternative zu Cron):**

```bash
# Service-Datei erstellen
sudo nano /etc/systemd/system/instagram-feed.service
```

Inhalt:
```ini
[Unit]
Description=Instagram Feed Scraper
After=network.target

[Service]
Type=oneshot
User=user
WorkingDirectory=/home/user/wohlfuehlgesundheit.de
ExecStart=/usr/bin/python3 /home/user/wohlfuehlgesundheit.de/api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12 --output /home/user/wohlfuehlgesundheit.de/public/data/instagram-feed.json
StandardOutput=append:/tmp/instagram-feed.log
StandardError=append:/tmp/instagram-feed.log
```

Timer-Datei erstellen:
```bash
sudo nano /etc/systemd/system/instagram-feed.timer
```

Inhalt:
```ini
[Unit]
Description=Instagram Feed Scraper Timer
Requires=instagram-feed.service

[Timer]
OnCalendar=daily
OnCalendar=*-*-* 09:00:00
Persistent=true

[Install]
WantedBy=timers.target
```

Aktivieren:
```bash
# Timer aktivieren und starten
sudo systemctl enable instagram-feed.timer
sudo systemctl start instagram-feed.timer

# Status prüfen
sudo systemctl status instagram-feed.timer
sudo systemctl list-timers
```

---

## IONOS Server Setup

### 1. Python-Umgebung einrichten

```bash
# SSH in deinen IONOS Server
ssh benutzer@deine-domain.de

# Navigiere zum Projektverzeichnis
cd /pfad/zu/wohlfuehlgesundheit.de

# Installiere instaloader
pip3 install instaloader
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

---

## Verwendung

### CLI-Befehle

**npm-Scripts (empfohlen):**
```bash
# Feed abrufen
npm run instagram:fetch

# Build mit automatischem Feed-Fetch
npm run build

# Preview der gebauten Seite
npm run preview
```

**Python direkt:**
```bash
# Standard (12 Posts)
python3 api/instagram_feed.py wohl_fuehl_gesundheit

# Mit benutzerdefinierten Optionen
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 20 --output public/data/instagram-feed.json

# Hilfe anzeigen
python3 api/instagram_feed.py --help
```

**Bash-Script:**
```bash
# Direkter Aufruf
./scripts/fetch-instagram.sh

# Mit sudo (falls nötig für IONOS)
sudo ./scripts/fetch-instagram.sh
```

### Python-Script Parameter

```bash
python3 api/instagram_feed.py [USERNAME] [OPTIONS]

Positionale Argumente:
  USERNAME              Instagram-Username (default: wohl_fuehl_gesundheit)

Optionale Argumente:
  -h, --help            Hilfe anzeigen
  --max-posts N         Maximale Anzahl Posts (default: 12)
  -o, --output PATH     Output-Pfad (default: public/data/instagram-feed.json)
```

**Beispiele:**
```bash
# Anderen Account scrapen
python3 api/instagram_feed.py other_account --max-posts 6

# Andere Output-Datei
python3 api/instagram_feed.py wohl_fuehl_gesundheit -o dist/data/feed.json

# Mehr Posts abrufen
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 20
```

---

## Fehlersuche

### Problem: `ModuleNotFoundError: No module named 'instaloader'`

**Symptom:**
```
Error: instaloader not installed. Install with: pip install instaloader
```

**Lösung (Garuda Linux):**
```bash
pip install instaloader

# Falls Permission-Fehler:
pip install --user instaloader

# Oder system-weit:
sudo pip install instaloader
```

**Lösung (IONOS):**
```bash
pip3 install instaloader

# Falls pip3 fehlt:
python3 -m pip install instaloader
```

---

### Problem: `Permission denied` beim Ausführen

**Symptom:**
```
bash: ./api/instagram_feed.py: Permission denied
```

**Lösung:**
```bash
# Script ausführbar machen
chmod +x api/instagram_feed.py
chmod +x scripts/fetch-instagram.sh

# Oder direkt mit Python ausführen
python3 api/instagram_feed.py
```

---

### Problem: `403 Forbidden` Warnung (normal!)

**Symptom:**
```
JSON Query to graphql/query: 403 Forbidden when accessing https://www.instagram.com/graphql/query [retrying; skip with ^C]
```

**Erklärung:**
✅ **Dies ist NORMAL und KEIN Fehler!**

Instagram hat Anti-Bot-Schutz, der manchmal den ersten Request blockiert. `instaloader` erkennt das automatisch und macht einen Retry mit angepassten Request-Headern.

**Was passiert:**
1. Erster Request → 403 Forbidden (Instagram blockt)
2. instaloader wartet kurz
3. Zweiter Request → Erfolgreich! ✅

**Lösung:**
- ✅ Nichts tun - das Script funktioniert automatisch
- ⚠️ Wenn es mehrmals fehlschlägt: 5-10 Minuten warten
- ℹ️ Die Warnung verschwindet nicht, aber das Feed wird trotzdem generiert

**Bestätigung dass es funktioniert hat:**
```
✓ Generated Instagram feed: public/data/instagram-feed.json (12 posts)
```

---

### Problem: Keine Posts werden abgerufen

**Symptom:**
```
Warning: No posts found for @wohl_fuehl_gesundheit
```

**Mögliche Ursachen & Lösungen:**

1. **Instagram-Account ist privat**
   - Der Account muss öffentlich sein
   - Überprüfen: https://www.instagram.com/wohl_fuehl_gesundheit/
   - Lösung: Account-Privatsphäre-Einstellungen ändern

2. **Falscher Username**
   ```bash
   # Korrekter Username (ohne @):
   python3 api/instagram_feed.py wohl_fuehl_gesundheit

   # FALSCH (mit @):
   python3 api/instagram_feed.py @wohl_fuehl_gesundheit
   ```

3. **Instagram-Rate-Limiting**
   - Zu viele Anfragen in kurzer Zeit
   - Lösung: 5-10 Minuten warten und erneut versuchen

4. **Netzwerkprobleme**
   ```bash
   # Internetverbindung testen
   ping instagram.com

   # DNS prüfen
   nslookup instagram.com
   ```

---

### Problem: JSON-Datei wird nicht generiert

**Symptom:**
```
FileNotFoundError: [Errno 2] No such file or directory: 'public/data/instagram-feed.json'
```

**Lösung:**
```bash
# Verzeichnis existiert nicht - erstellen
mkdir -p public/data

# Schreibrechte prüfen
ls -la public/data

# Rechte anpassen (falls nötig)
chmod 755 public/data

# Erneut ausführen
npm run instagram:fetch
```

---

### Problem: `npm run instagram:fetch` schlägt fehl

**Symptom:**
```
Error: bash: scripts/fetch-instagram.sh: No such file or directory
```

**Lösung:**
```bash
# Prüfen ob Script existiert
ls -la scripts/fetch-instagram.sh

# Script ausführbar machen
chmod +x scripts/fetch-instagram.sh

# Direkt Python-Script aufrufen
python3 api/instagram_feed.py wohl_fuehl_gesundheit
```

---

### Problem: Alte Posts werden angezeigt

**Symptom:**
Das Feed zeigt veraltete Posts

**Lösung:**
```bash
# Feed manuell aktualisieren
npm run instagram:fetch

# Cache löschen (Browser)
# Ctrl + Shift + R (Hard Reload)

# Oder Astro Cache löschen
rm -rf .astro
npm run build
```

---

### Problem: Cron-Job läuft nicht

**Garuda Linux - Cron prüfen:**
```bash
# Cron-Dienst-Status
sudo systemctl status cronie

# Falls nicht aktiv:
sudo systemctl enable --now cronie

# Crontab-Log prüfen
grep CRON /var/log/syslog
```

**Garuda Linux - Systemd Timer prüfen:**
```bash
# Timer-Status
sudo systemctl status instagram-feed.timer

# Timer-Liste
systemctl list-timers --all

# Service manuell ausführen (Test)
sudo systemctl start instagram-feed.service

# Logs anschauen
sudo journalctl -u instagram-feed.service -f
```

**IONOS - Cron prüfen:**
```bash
# Aktive Cron-Jobs anzeigen
crontab -l

# Cron-Logs (IONOS-spezifisch)
tail -f /var/log/cron
```

---

## Monitoring & Logs

### Logs prüfen (Garuda Linux)

**Systemd-Service-Logs:**
```bash
# Echtzeit-Logs
sudo journalctl -u instagram-feed.service -f

# Letzte 50 Zeilen
sudo journalctl -u instagram-feed.service -n 50

# Logs seit gestern
sudo journalctl -u instagram-feed.service --since yesterday
```

**Datei-Logs:**
```bash
# Log-Datei anschauen
tail -f /tmp/instagram-feed.log

# Letzte Fehler finden
grep -i error /tmp/instagram-feed.log
```

### Logs prüfen (IONOS)

```bash
# Cron-Log
tail -f /var/log/instagram-feed.log

# Fehler finden
grep -i error /var/log/instagram-feed.log

# Anzahl erfolgreicher Runs
grep "Generated Instagram feed" /var/log/instagram-feed.log | wc -l
```

### JSON-Feed validieren

```bash
# Feed anzeigen
cat public/data/instagram-feed.json

# Mit jq (formatiert):
cat public/data/instagram-feed.json | jq

# Nur Anzahl Posts
cat public/data/instagram-feed.json | jq '.postsCount'

# Letzte Aktualisierung
cat public/data/instagram-feed.json | jq '.fetchedAt'

# Post-Titles anzeigen
cat public/data/instagram-feed.json | jq '.posts[].caption' | head -n 5
```

### Performance-Monitoring

```bash
# Dateigröße prüfen
du -h public/data/instagram-feed.json

# Anzahl Posts zählen
cat public/data/instagram-feed.json | jq '.posts | length'

# Script-Laufzeit messen
time python3 api/instagram_feed.py wohl_fuehl_gesundheit
```

---

## Best Practices

### Aktualisierungsintervalle

**Empfohlene Intervalle:**
- **Produktion (IONOS)**: 1-2x täglich (z.B. 6:00 und 18:00 Uhr)
- **Development (Garuda)**: Manuell oder 1x täglich
- **Vermeiden**: Aktualisierung häufiger als alle 2 Stunden (Instagram Rate-Limiting)

**Warum?**
- Instagram-Posts ändern sich nicht minütlich
- Zu viele Requests können zu temporären Sperren führen
- Reduziert Server-Last und Bandbreite

### Anzahl Posts

```bash
# Standard: 12 Posts (optimale Balance)
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 12

# Mehr Posts (bei viel Content): 20-24 Posts
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 20

# Weniger Posts (Performance): 6-9 Posts
python3 api/instagram_feed.py wohl_fuehl_gesundheit --max-posts 6
```

**Empfehlung**: 6-12 Posts für optimale Ladezeiten

### Fehlerbehandlung & Logging

**Produktions-Cron mit Fehlerbehandlung:**
```bash
0 6 * * * cd /pfad/zu/projekt && { python3 api/instagram_feed.py wohl_fuehl_gesundheit 2>&1 || echo "Instagram feed failed at $(date)" >> /var/log/instagram-feed-errors.log; } >> /var/log/instagram-feed.log
```

**Log-Rotation einrichten:**
```bash
# /etc/logrotate.d/instagram-feed erstellen
sudo nano /etc/logrotate.d/instagram-feed
```

Inhalt:
```
/var/log/instagram-feed.log {
    weekly
    rotate 4
    compress
    missingok
    notifempty
}
```

### Backup & Fallback

**Automatisches Backup (vor Update):**
```bash
# In scripts/fetch-instagram.sh hinzufügen:
cp public/data/instagram-feed.json public/data/instagram-feed.backup.json 2>/dev/null || true
```

**Fallback bei Fehler:**
```bash
# Backup wiederherstellen
cp public/data/instagram-feed.backup.json public/data/instagram-feed.json
```

### Performance-Optimierung

1. **CDN für Bilder verwenden** (optional)
   - Instagram-Bilder werden direkt von Instagram geladen
   - Kein zusätzlicher Speicherplatz erforderlich

2. **Lazy Loading aktiviert**
   - Komponente nutzt `loading="lazy"` für Bilder
   - Optimiert Page-Load-Performance

3. **JSON-Komprimierung**
   ```bash
   # JSON minifizieren (optional)
   cat public/data/instagram-feed.json | jq -c > public/data/instagram-feed.min.json
   ```

### Datenschutz (DSGVO)

✅ **Erlaubt:**
- Öffentliche Instagram-Posts anzeigen
- Öffentliche Profilinformationen abrufen
- Links zu Instagram-Profil/-Posts setzen

⚠️ **Beachten:**
- Keine Cookies von Instagram laden (nur wenn unbedingt nötig)
- Cookie-Consent für Instagram-Embeds einholen (falls verwendet)
- In Datenschutzerklärung erwähnen

**Datenschutzerklärung - Beispieltext:**
```markdown
Wir zeigen auf unserer Website öffentliche Beiträge unseres Instagram-Profils an.
Die Bilder und Inhalte werden direkt von Instagram geladen. Dabei kann Instagram
Ihre IP-Adresse und weitere Metadaten erfassen. Weitere Informationen finden Sie
in der Datenschutzerklärung von Instagram/Meta.
```

### Git & Deployment

**In .gitignore aufnehmen (optional):**
```bash
# Falls du das Feed nicht committen möchtest:
public/data/instagram-feed.json
public/data/instagram-feed.backup.json
```

**Pre-Build Hook (automatischer Fetch vor Build):**
```json
// package.json
{
  "scripts": {
    "prebuild": "npm run instagram:fetch",
    "build": "astro build"
  }
}
```

### Monitoring & Alerts

**E-Mail-Benachrichtigung bei Fehler (Garuda Linux):**
```bash
# Installiere mailutils
sudo pacman -S mailutils

# Cron mit E-Mail
0 6 * * * cd /home/user/projekt && python3 api/instagram_feed.py || echo "Instagram Feed failed" | mail -s "Instagram Scraper Error" deine@email.de
```

**Healthcheck-Script erstellen:**
```bash
#!/bin/bash
# scripts/check-instagram-feed.sh

FEED_FILE="public/data/instagram-feed.json"
MAX_AGE_HOURS=48

if [ ! -f "$FEED_FILE" ]; then
    echo "ERROR: Feed file not found"
    exit 1
fi

# Prüfe Alter der Datei
AGE_SECONDS=$(( $(date +%s) - $(stat -c %Y "$FEED_FILE") ))
AGE_HOURS=$(( AGE_SECONDS / 3600 ))

if [ $AGE_HOURS -gt $MAX_AGE_HOURS ]; then
    echo "WARNING: Feed is $AGE_HOURS hours old (max: $MAX_AGE_HOURS)"
    exit 1
fi

# Prüfe ob Feed Posts enthält
POST_COUNT=$(cat "$FEED_FILE" | jq -r '.postsCount // 0')
if [ $POST_COUNT -eq 0 ]; then
    echo "ERROR: Feed contains no posts"
    exit 1
fi

echo "OK: Feed is healthy ($POST_COUNT posts, $AGE_HOURS hours old)"
exit 0
```

---

## Sicherheit

- ⚠️ Das JSON-Feed enthält öffentliche Instagram-Daten
- ✅ Keine sensiblen Daten oder API-Keys erforderlich
- ✅ Read-only Zugriff auf öffentliche Instagram-Profile
- ✅ DSGVO-konform (öffentliche Daten)
- ✅ Keine Instagram-API-Credentials notwendig
- ✅ Keine Login-Daten gespeichert

---

## System-Anforderungen

### Garuda Linux
- **Python**: 3.7+ (empfohlen: 3.10+)
- **pip**: Python Package Manager
- **Node.js**: 18+ (für npm-Scripts)
- **Bash**: Shell für Scripts

### IONOS Server
- **Python**: 3.13.7 ✅ (bereits installiert)
- **pip3**: Python Package Manager
- **Cron**: Für automatische Updates
- **Apache/Nginx**: Webserver (bereits vorhanden)

---

## Nützliche Links

- **instaloader Dokumentation**: https://instaloader.github.io/
- **Instagram Profil**: https://www.instagram.com/wohl_fuehl_gesundheit/
- **Astro Dokumentation**: https://docs.astro.build/
- **Python 3 Dokumentation**: https://docs.python.org/3/

---

## Support & Troubleshooting

Bei Problemen oder Fragen:

1. **Logs prüfen** (siehe [Monitoring & Logs](#monitoring--logs))
2. **Fehlersuche durchgehen** (siehe [Fehlersuche](#fehlersuche))
3. **GitHub Issues**: https://github.com/darkjive/wohlfuehlgesundheit.de/issues
4. **instaloader Issues**: https://github.com/instaloader/instaloader/issues

---

**Version**: 1.0.0
**Letztes Update**: 2025-11-29
**Autor**: Claude Code
**Lizenz**: MIT
