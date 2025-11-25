# SFTP Deployment Setup

## Übersicht

Dieses Projekt verwendet GitHub Actions für automatisches Deployment via SFTP:

- **Production**: Deployment auf den Haupt-Webspace bei Push auf `main` Branch
- **Test/Preview**: Deployment auf die test.subdomain bei Push auf andere Branches (z.B. `claude/*`)

## GitHub Secrets Konfiguration

### Schritt 1: Zu GitHub Repository Settings navigieren

1. Gehe zu deinem GitHub Repository
2. Klicke auf **Settings** → **Secrets and variables** → **Actions**
3. Klicke auf **New repository secret**

### Schritt 2: Secrets für Test/Preview-Umgebung hinzufügen

Füge folgende Secrets hinzu (für die test.subdomain):

| Secret Name | Wert |
|------------|------|
| `FTP_SERVER_PREVIEW` | `access-5016697314.webspace-host.com` |
| `FTP_USERNAME_PREVIEW` | `a1798707` |
| `FTP_PASSWORD_PREVIEW` | `AxjeY*LwURy8q!PH1$Mgt?d` |

### Schritt 3: Secrets für Production-Umgebung hinzufügen (optional)

Falls du separate Credentials für Production hast:

| Secret Name | Wert |
|------------|------|
| `FTP_SERVER` | [Dein Production Server] |
| `FTP_USERNAME` | [Dein Production Username] |
| `FTP_PASSWORD` | [Dein Production Passwort] |

**Hinweis:** Wenn Production und Test dieselben Credentials nutzen, kannst du auch die gleichen Werte verwenden.

## Wie funktioniert das Deployment?

### Automatisches Deployment

1. **Bei Push auf `main` Branch:**
   - GitHub Actions baut das Projekt (`npm run build`)
   - Deployt zu Production via SFTP

2. **Bei Push auf andere Branches (z.B. `claude/*`):**
   - GitHub Actions baut das Projekt
   - Deployt zur Test-Subdomain via SFTP

### Manuelles Deployment

Du kannst auch manuell deployen:
```bash
# Lokales Build erstellen
npm run build

# Dann mit SFTP-Client (z.B. FileZilla) hochladen
# oder mit CLI-Tool wie lftp/rsync
```

## Deployment-Konfiguration

Die Deployment-Konfiguration findest du in `.github/workflows/actions.yaml`.

### Wichtige Einstellungen:

- **Protokoll:** SFTP (Port 22)
- **Local Directory:** `./dist/` (Astro Build-Output)
- **Server Directory (Test):** `./` (Root des Test-Webspace)
- **Server Directory (Production):** `./` (Root des Production-Webspace)

### Anpassen des Server-Pfads

Falls deine test.subdomain einen anderen Ziel-Pfad benötigt (z.B. `/test/` oder `/public_html/`), passe in `.github/workflows/actions.yaml` den Wert `server-dir` an:

```yaml
server-dir: ./public_html/  # Beispiel
```

## Sicherheit

✅ **DO:**
- Verwende GitHub Secrets für alle Credentials
- Setze Secrets niemals in Code-Dateien
- Committe keine `.env` Dateien mit Passwörtern

❌ **DON'T:**
- Credentials in `.env` Dateien committen
- Passwörter im Code hardcoden
- Secrets in Log-Ausgaben drucken

## Troubleshooting

### Deployment schlägt fehl

1. **Überprüfe die GitHub Secrets:**
   - Sind alle Secrets korrekt gesetzt?
   - Gibt es Tippfehler in Benutzername/Passwort?

2. **Überprüfe den Server-Pfad:**
   - Ist `server-dir` korrekt?
   - Hat der SFTP-User Schreibrechte?

3. **GitHub Actions Log prüfen:**
   - Gehe zu **Actions** Tab in GitHub
   - Klicke auf den fehlgeschlagenen Workflow
   - Überprüfe die Fehlerausgabe

### Build schlägt fehl

```bash
# Lokaler Test
npm ci
npm run build
```

Falls der Build lokal funktioniert, aber in GitHub Actions fehlschlägt, überprüfe:
- Node.js Version in Workflow-Datei
- Environment-spezifische Variablen

## Test-Deployment testen

Um das Test-Deployment zu testen:

1. Erstelle einen neuen Branch: `git checkout -b test-deployment`
2. Mache eine kleine Änderung
3. Committe und pushe: `git push -u origin test-deployment`
4. Gehe zu GitHub Actions und beobachte den Deployment-Prozess
5. Überprüfe deine test.subdomain

## Nützliche Links

- [FTP-Deploy-Action Dokumentation](https://github.com/SamKirkland/FTP-Deploy-Action)
- [GitHub Actions Dokumentation](https://docs.github.com/en/actions)
- [GitHub Secrets Management](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
