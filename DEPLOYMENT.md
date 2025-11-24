# Automatisches Deployment Setup für IONOS

Dieses Projekt nutzt GitHub Actions für automatisches Push-to-Deploy auf IONOS.

## Deployment-Ziele

### 1. Production Deployment (Hauptdomain)
- **Trigger**: Push auf `main` Branch
- **Ziel**: wohlfuehlgesundheit.de
- **Workflow**: Build + Tests → Automatischer FTP-Upload

### 2. Preview Deployment (Preview-Subdomain)
- **Trigger**: Push auf alle anderen Branches (z.B. `claude/**`)
- **Ziel**: preview.wohlfuehlgesundheit.de
- **Workflow**: Build + Tests → Automatischer FTP-Upload zur Preview-Subdomain

## GitHub Secrets einrichten

Du musst folgende Secrets in deinem GitHub Repository hinterlegen:

### Für Production (Hauptdomain):

1. `FTP_SERVER` - SFTP-Server von IONOS
   - Wert: `access-5016697314.webspace-host.com`

2. `FTP_USERNAME` - Dein SFTP-Benutzername
   - Wert: `a1798707`

3. `FTP_PASSWORD` - Dein SFTP-Passwort
   - Das Passwort für deinen IONOS SFTP-Zugang

### Für Preview (Subdomain):

**Option A: Gleicher SFTP-Zugang mit Unterordner (empfohlen)**
4. `FTP_SERVER_PREVIEW` - Gleicher SFTP-Server
   - Wert: `access-5016697314.webspace-host.com`

5. `FTP_USERNAME_PREVIEW` - Gleicher Benutzername
   - Wert: `a1798707`

6. `FTP_PASSWORD_PREVIEW` - Gleiches Passwort
   - Wert: Dein IONOS SFTP-Passwort

Die Preview-Dateien werden dann in `/preview/` hochgeladen (siehe Workflow-Konfiguration).

**Option B: Separate Subdomain mit eigenem SFTP-Zugang**
Falls du eine separate Subdomain `preview.wohlfuehlgesundheit.de` mit eigenem SFTP-Zugang eingerichtet hast, trage hier die separaten Zugangsdaten ein.

## Schritt-für-Schritt Anleitung

### 1. IONOS vorbereiten

Du nutzt bereits SFTP (Port 22) mit folgenden Zugangsdaten:
- **Server**: `access-5016697314.webspace-host.com`
- **Port**: 22
- **Protokoll**: SFTP
- **Benutzername**: `a1798707`

#### Option A: Ein SFTP-Zugang mit Unterordnern (Standard)
Die aktuelle Konfiguration nutzt deinen bestehenden SFTP-Zugang:
- **Production**: Hochladen ins Root-Verzeichnis (`./`)
- **Preview**: Hochladen in `/preview/` Unterordner

Du musst in IONOS:
1. Einen Ordner `/preview/` anlegen (per SFTP oder FileZilla)
2. Eine Subdomain `preview.wohlfuehlgesundheit.de` erstellen, die auf `/preview/` zeigt

#### Option B: Separate Subdomain mit eigenem SFTP-Zugang (Optional)
Falls du eine separate Subdomain mit eigenem SFTP-Zugang einrichten möchtest:
1. Logge dich in dein IONOS Control Panel ein
2. Richte eine Subdomain `preview.wohlfuehlgesundheit.de` mit separatem SFTP-Zugang ein
3. Trage die separaten Zugangsdaten als GitHub Secrets ein

### 2. GitHub Secrets hinzufügen

1. Gehe zu deinem GitHub Repository
2. Klicke auf **Settings** → **Secrets and variables** → **Actions**
3. Klicke auf **New repository secret**
4. Füge nacheinander alle 6 Secrets hinzu:

**Für Production:**
```
Name: FTP_SERVER
Value: access-5016697314.webspace-host.com
```

```
Name: FTP_USERNAME
Value: a1798707
```

```
Name: FTP_PASSWORD
Value: [dein-ionos-sftp-passwort]
```

**Für Preview (gleicher SFTP-Zugang):**
```
Name: FTP_SERVER_PREVIEW
Value: access-5016697314.webspace-host.com
```

```
Name: FTP_USERNAME_PREVIEW
Value: a1798707
```

```
Name: FTP_PASSWORD_PREVIEW
Value: [dein-ionos-sftp-passwort] (gleiches Passwort)
```

💡 **Tipp**: Falls du den gleichen SFTP-Zugang für Production und Preview nutzt (empfohlen), sind die Werte für die Preview-Secrets identisch mit den Production-Secrets.

### 3. Workflow testen

1. **Production-Deployment testen:**
   ```bash
   git checkout main
   git commit --allow-empty -m "Test production deployment"
   git push origin main
   ```

2. **Preview-Deployment testen:**
   ```bash
   git checkout -b test-preview
   git commit --allow-empty -m "Test preview deployment"
   git push origin test-preview
   ```

3. Überprüfe den Status in GitHub:
   - Gehe zu **Actions** Tab in deinem Repository
   - Sieh dir die Workflow-Ausführungen an
   - Bei Fehlern: Logs überprüfen

## Workflow-Details

### Was passiert beim Deployment?

1. **Build & Check Phase:**
   - Code wird ausgecheckt
   - Node.js Dependencies werden installiert
   - Projekt wird gebaut (`npm run build`)
   - Code-Quality-Checks werden ausgeführt (`npm run check`)

2. **Deploy Phase (nur bei erfolgreichem Build):**
   - Der `dist/` Ordner wird per FTP/SFTP hochgeladen
   - Alte Dateien werden **nicht** gelöscht (inkrementelles Update)
   - `.git` und `node_modules` werden ausgeschlossen

### Deployment-Trigger

- **Production**: Nur bei Push auf `main` Branch
- **Preview**: Bei Push auf alle anderen Branches (außer `main`)
- **Pull Requests**: Nur Build & Check, kein Deployment

## Wichtige Hinweise

### PHP-Backend und Dependencies

Das Deployment überträgt nur den `dist/` Ordner (Astro-Build). Für das PHP-Backend musst du sicherstellen:

1. **PHP-Dateien**: Die Dateien in `public/api/` sind bereits im `dist/` Ordner enthalten
2. **Composer Dependencies**: Der `vendor/` Ordner muss separat auf den Server:
   - Entweder manuell per FTP hochladen
   - Oder auf dem Server `composer install` ausführen (falls SSH-Zugang vorhanden)

### .env Datei

Die `.env` Datei mit sensiblen Daten (Zoom-Credentials, etc.) wird **nicht** automatisch deployed:
- Muss manuell auf dem Server angelegt werden
- Pfad: `/dist/.env` (oder wo dein Webroot ist)

### SSL/HTTPS

- Stelle sicher, dass beide Domains SSL-Zertifikate haben
- IONOS bietet kostenlose Let's Encrypt Zertifikate

### Server-Verzeichnisse

Falls deine IONOS-Struktur anders ist, passe `server-dir` in `.github/workflows/actions.yaml` an:

```yaml
# Beispiele für verschiedene IONOS-Strukturen:
server-dir: ./                    # Root-Verzeichnis
server-dir: /www/                 # Häufig bei IONOS
server-dir: /htdocs/              # Alternative
server-dir: /public_html/         # Alternative
```

## SFTP-Konfiguration

Dieses Projekt nutzt bereits **SFTP (Port 22)** für sichere Übertragungen. Die Konfiguration ist bereits in `.github/workflows/actions.yaml` eingerichtet:

```yaml
protocol: ftps
port: 22
```

Falls Verbindungsprobleme auftreten, kannst du alternativ testen:
- `protocol: sftp` (native SFTP)
- `protocol: ftps-legacy` (ältere FTPS-Implementierung)

## Troubleshooting

### FTP-Verbindung schlägt fehl
- Überprüfe Server, Username und Passwort
- Prüfe ob FTP-Zugang bei IONOS aktiviert ist
- Teste FTP-Verbindung lokal mit FileZilla

### Build schlägt fehl
- Überprüfe Logs in GitHub Actions
- Teste Build lokal: `npm run build`
- Stelle sicher, dass alle Dependencies installiert sind

### Preview-Deployment funktioniert nicht
- Prüfe ob Subdomain `preview.*` bei IONOS eingerichtet ist
- Verifiziere FTP-Zugangsdaten für Preview
- Überprüfe `server-dir` Pfad

## Support

Bei Fragen oder Problemen:
1. GitHub Actions Logs überprüfen
2. IONOS Support kontaktieren (FTP-Zugang)
3. Issue im Repository erstellen
