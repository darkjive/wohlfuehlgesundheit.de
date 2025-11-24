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

1. `FTP_SERVER` - FTP/SFTP-Server von IONOS
   - Beispiel: `wohlfuehlgesundheit.de` oder `ftp.wohlfuehlgesundheit.de`
   - Findest du in deinem IONOS Control Panel unter "FTP-Zugänge"

2. `FTP_USERNAME` - Dein FTP-Benutzername
   - Beispiel: `u12345678` oder deine E-Mail-Adresse
   - Findest du in deinem IONOS Control Panel

3. `FTP_PASSWORD` - Dein FTP-Passwort
   - Das Passwort, das du für deinen FTP-Zugang vergeben hast

### Für Preview (Subdomain):

4. `FTP_SERVER_PREVIEW` - FTP/SFTP-Server für Preview-Subdomain
   - Beispiel: `preview.wohlfuehlgesundheit.de` oder `ftp.preview.wohlfuehlgesundheit.de`
   - Falls du eine separate Subdomain eingerichtet hast

5. `FTP_USERNAME_PREVIEW` - FTP-Benutzername für Preview
   - Kann der gleiche sein wie für Production oder ein separater

6. `FTP_PASSWORD_PREVIEW` - FTP-Passwort für Preview
   - Das zugehörige Passwort

## Schritt-für-Schritt Anleitung

### 1. IONOS vorbereiten

#### Option A: Separate FTP-Zugänge (empfohlen)
1. Logge dich in dein IONOS Control Panel ein
2. Richte eine Subdomain `preview.wohlfuehlgesundheit.de` ein
3. Erstelle separate FTP-Zugänge für:
   - Hauptdomain (wohlfuehlgesundheit.de)
   - Preview-Subdomain (preview.wohlfuehlgesundheit.de)

#### Option B: Ein FTP-Zugang mit Unterordnern
1. Verwende einen FTP-Zugang
2. Passe die `server-dir` in `.github/workflows/actions.yaml` an:
   - Production: `server-dir: /www/` (oder dein Hauptverzeichnis)
   - Preview: `server-dir: /preview/` (oder Subdomain-Verzeichnis)

### 2. GitHub Secrets hinzufügen

1. Gehe zu deinem GitHub Repository
2. Klicke auf **Settings** → **Secrets and variables** → **Actions**
3. Klicke auf **New repository secret**
4. Füge nacheinander alle 6 Secrets hinzu:

```
Name: FTP_SERVER
Value: wohlfuehlgesundheit.de
```

```
Name: FTP_USERNAME
Value: u12345678
```

```
Name: FTP_PASSWORD
Value: dein-ftp-passwort
```

```
Name: FTP_SERVER_PREVIEW
Value: preview.wohlfuehlgesundheit.de
```

```
Name: FTP_USERNAME_PREVIEW
Value: u12345678-preview
```

```
Name: FTP_PASSWORD_PREVIEW
Value: dein-preview-ftp-passwort
```

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

## Alternative: SFTP statt FTP

Falls du SFTP nutzen möchtest (sicherer), ändere in `.github/workflows/actions.yaml`:

```yaml
- name: Deploy via SFTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    protocol: ftps  # oder ftps-legacy
    local-dir: ./dist/
    server-dir: ./
```

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
