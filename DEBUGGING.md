# 🔧 DEBUGGING-ANLEITUNG

## Problem: Server Error - Keine E-Mails kommen an

Wenn deine Formulare einen Server-Error werfen und keine E-Mails ankommen, folge dieser Anleitung:

---

## 🚨 SCHRITT 1: Debug-Script ausführen

### Auf dem Live-Server

1. **Lade das Debug-Script auf:**
   ```
   /public/api/debug.php
   ```

2. **Öffne im Browser:**
   ```
   https://deine-domain.de/api/debug.php?secret=debug2024
   ```

   ⚠️ **WICHTIG:** Ersetze `debug2024` durch ein eigenes Passwort!

3. **Prüfe die Ausgabe:**
   - ✅ Grüne Häkchen = alles OK
   - ❌ Rote Kreuze = Problem gefunden
   - ⚠️ Gelbe Warnungen = Achtung erforderlich

4. **Nach dem Debugging:**
   ```bash
   # LÖSCHE die debug.php SOFORT wieder!
   rm /public/api/debug.php
   ```

---

## 📁 SCHRITT 2: .env-Datei korrekt platzieren

### Wo kann die .env-Datei liegen?

Das System sucht **automatisch** an folgenden Orten:

1. `/public/api/.env` (im selben Ordner wie die PHP-Dateien)
2. `/public/.env` (ein Level höher)
3. `/.env` (Projekt-Root)
4. `[DOCUMENT_ROOT]/.env`
5. `[DOCUMENT_ROOT]/api/.env`

### .env-Datei erstellen

1. **Kopiere die Vorlage:**
   ```bash
   cp /public/api/.env.example /public/api/.env
   ```

2. **Bearbeite die .env-Datei** und fülle alle Werte aus:
   ```bash
   nano /public/api/.env
   ```

3. **Setze Berechtigungen:**
   ```bash
   chmod 600 /public/api/.env
   ```

---

## 🔑 SCHRITT 3: Zoom API Credentials

### Zoom Server-to-Server OAuth App erstellen

1. Gehe zu: https://marketplace.zoom.us/develop/create
2. Wähle **"Server-to-Server OAuth"**
3. Gib deiner App einen Namen (z.B. "Wohlfühlgesundheit Booking")
4. Notiere dir:
   - **Account ID**
   - **Client ID**
   - **Client Secret**

### Scopes hinzufügen

Füge folgende Scopes hinzu:
- `meeting:write:admin`
- `meeting:read:admin`
- `user:read:admin`

### In .env eintragen

```env
ZOOM_ACCOUNT_ID=deine_account_id
ZOOM_CLIENT_ID=dein_client_id
ZOOM_CLIENT_SECRET=dein_client_secret
```

---

## 📧 SCHRITT 4: E-Mail-Konfiguration

### PHP Mail testen

1. **Prüfe PHP Mail-Funktion:**
   - Im Debug-Script auf den Link "Test-E-Mail senden" klicken
   - E-Mail-Postfach prüfen (auch Spam!)

2. **Wenn keine E-Mail ankommt:**

   **Option A - PHP Mail konfigurieren (Linux/Unix):**
   ```bash
   # Postfix oder Sendmail installieren
   sudo apt-get install postfix

   # PHP mail() nutzt dann automatisch sendmail
   ```

   **Option B - SMTP verwenden (empfohlen für Shared Hosting):**
   - Frage deinen Hoster nach SMTP-Einstellungen
   - Nutze PHPMailer statt der mail()-Funktion

3. **E-Mail-Adressen in .env setzen:**
   ```env
   ADMIN_EMAIL=deine@email.de
   FROM_EMAIL=noreply@wohlfuehlgesundheit.de
   FROM_NAME=Wohlfühlgesundheit
   ```

---

## 🔒 SCHRITT 5: CORS & Security

### ALLOWED_ORIGINS setzen

```env
# Nur diese Domains dürfen die API aufrufen
# WICHTIG: Domain mit Umlaut (IDN) - beide Varianten erlauben!
# UTF-8 (Browser-Anzeige): wohlfühlgesundheit.de
# Punycode (technisch):    xn--wohlfhlgesundheit-62b.de
ALLOWED_ORIGINS=https://wohlfühlgesundheit.de,https://xn--wohlfhlgesundheit-62b.de,https://www.wohlfühlgesundheit.de,https://www.xn--wohlfhlgesundheit-62b.de
```

⚠️ **Wichtig:**
- Keine Leerzeichen nach dem Komma!
- Beide Domain-Varianten (UTF-8 + Punycode) angeben für maximale Kompatibilität
- Siehe IDN-DOMAIN.md für Details

### CSRF Secret generieren

```bash
# Generiere einen zufälligen String
openssl rand -base64 32
```

Trage den generierten String ein:
```env
CSRF_SECRET=dein_generierter_string_hier
```

---

## 📊 SCHRITT 6: PHP Logs prüfen

### Fehler-Logs finden

**Bei den meisten Hostern:**
```
/var/log/php_errors.log
/var/log/apache2/error.log
/home/username/logs/error.log
```

**Via cPanel:**
- cPanel → Logs → Error Logs

### Logs live anzeigen

```bash
tail -f /pfad/zum/error.log
```

---

## 🐛 HÄUFIGE FEHLER & LÖSUNGEN

### Fehler 1: ".env file not found"

**Ursache:** .env-Datei liegt am falschen Ort

**Lösung:**
```bash
# Prüfe, wo die Datei ist
find /home -name ".env" 2>/dev/null

# Verschiebe sie an den richtigen Ort
mv /alte/position/.env /public/api/.env
```

---

### Fehler 2: "Environment validation failed"

**Ursache:** Umgebungsvariablen nicht gesetzt oder leer

**Lösung:**
1. Öffne .env-Datei
2. Prüfe, ob ALLE erforderlichen Variablen ausgefüllt sind:
   - ZOOM_ACCOUNT_ID
   - ZOOM_CLIENT_ID
   - ZOOM_CLIENT_SECRET
   - ADMIN_EMAIL
   - FROM_EMAIL
   - ALLOWED_ORIGINS
   - CSRF_SECRET

---

### Fehler 3: "Zoom API Verbindungsfehler"

**Ursache:** Falsche Zoom-Credentials oder API nicht aktiviert

**Lösung:**
1. Prüfe Credentials in Zoom Marketplace
2. Stelle sicher, dass die App **aktiviert** ist
3. Prüfe, ob alle Scopes gesetzt sind

---

### Fehler 4: "Sicherheitsvalidierung fehlgeschlagen"

**Ursache:** CSRF-Token ungültig oder CSRF_SECRET nicht gesetzt

**Lösung:**
1. CSRF_SECRET in .env setzen
2. Browser-Cache leeren
3. Seite neu laden

---

### Fehler 5: "Zu viele Anfragen"

**Ursache:** Rate-Limiting greift (Standard: 5 pro Stunde)

**Lösung:**
```bash
# Rate-Limit-Dateien löschen
rm -rf /public/_rate_limit/*

# Oder Limit in .env erhöhen
RATE_LIMIT_MAX_REQUESTS=10
```

---

### Fehler 6: E-Mail wird nicht gesendet

**Ursache:** PHP mail() nicht korrekt konfiguriert

**Lösung:**

**Prüfe sendmail_path:**
```bash
php -i | grep sendmail_path
```

**Falls leer oder falsch:**
```ini
; In php.ini
sendmail_path = /usr/sbin/sendmail -t -i
```

**Oder nutze SMTP statt mail():**
- Implementiere PHPMailer
- Konfiguriere SMTP-Server des Hosters

---

## 🔍 ERWEITERTE DEBUGGING-TIPPS

### Debug-Modus aktivieren

```env
DEBUG_MODE=true
```

⚠️ **WICHTIG:** Nur temporär! Auf Production wieder auf `false` setzen!

### PHP Errors anzeigen (nur temporär!)

```php
<?php
// Am Anfang der PHP-Datei
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### cURL-Anfragen testen

```bash
# Test CSRF-Token holen
curl https://deine-domain.de/api/get-csrf-token.php

# Test Formular senden (mit echten Daten)
curl -X POST https://deine-domain.de/api/anamnese-booking.php \
  -d "vorname=Test" \
  -d "nachname=User" \
  -d "email=test@example.com" \
  # ... weitere Felder
```

---

## 📞 SUPPORT KONTAKT

Wenn alle Debugging-Schritte fehlschlagen:

1. **Sammle folgende Informationen:**
   - Debug-Script-Ausgabe (Screenshot)
   - PHP Error Logs
   - PHP Version (`php -v`)
   - Hosting-Anbieter

2. **Erstelle ein GitHub Issue:**
   - https://github.com/darkjive/wohlfuehlgesundheit.de/issues

---

## ✅ CHECKLISTE

Vor dem Live-Gang:

- [ ] .env-Datei erstellt und ausgefüllt
- [ ] .env-Berechtigungen auf 600 gesetzt
- [ ] Zoom API getestet (grüner Haken im Debug-Script)
- [ ] Test-E-Mail erfolgreich empfangen
- [ ] CORS ALLOWED_ORIGINS gesetzt
- [ ] CSRF_SECRET generiert
- [ ] Debug-Script gelöscht
- [ ] DEBUG_MODE=false in .env
- [ ] display_errors=Off in PHP
- [ ] Testbuchung durchgeführt
- [ ] Logs auf Fehler geprüft

---

**Viel Erfolg beim Debugging! 🚀**
