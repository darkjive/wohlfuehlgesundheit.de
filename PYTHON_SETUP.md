# Python Virtual Environment Setup

## Warum venv?

✅ **Empfohlen für saubere Entwicklung**

- Isoliert Python-Dependencies vom System
- Vermeidet Konflikte mit anderen Projekten
- Einfach zu löschen und neu zu erstellen
- Reproduzierbar auf allen Systemen

## Setup mit venv

### 1. venv erstellen

```bash
# Im Projektverzeichnis
cd /home/user/wohlfuehlgesundheit.de

# venv erstellen
python3 -m venv venv
```

### 2. venv aktivieren

**Garuda Linux / macOS:**
```bash
source venv/bin/activate
```

**Windows:**
```bash
venv\Scripts\activate
```

Nach Aktivierung siehst du `(venv)` im Prompt:
```
(venv) user@garuda:~/wohlfuehlgesundheit.de$
```

### 3. Dependencies installieren

```bash
# Mit aktiviertem venv
pip install -r api/requirements.txt

# Oder direkt:
pip install reelscraper
```

### 4. Instagram-Feed abrufen

```bash
# venv muss aktiviert sein!
python api/instagram_feed.py wohl_fuehl_gesundheit

# Oder mit npm (aktiviert venv automatisch nicht):
npm run instagram:fetch
```

### 5. venv deaktivieren

```bash
deactivate
```

---

## npm-Integration mit venv

### Option A: Scripts anpassen (empfohlen)

Update `scripts/fetch-instagram.sh`:

```bash
#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PYTHON_SCRIPT="$PROJECT_ROOT/api/instagram_feed.py"
OUTPUT_FILE="$PROJECT_ROOT/public/data/instagram-feed.json"

echo "🔍 Fetching Instagram feed..."

# Check if venv exists and activate it
if [ -d "$PROJECT_ROOT/venv" ]; then
    echo "📦 Using virtual environment"
    source "$PROJECT_ROOT/venv/bin/activate"
fi

# Check if Python is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Error: Python 3 is not installed"
    exit 1
fi

# Check if reelscraper is installed
if ! python3 -c "import reelscraper" 2>/dev/null; then
    echo "📦 Installing reelscraper..."
    pip install reelscraper
fi

# Run the Python script
python3 "$PYTHON_SCRIPT" \
    wohl_fuehl_gesundheit \
    --max-posts 12 \
    --output "$OUTPUT_FILE"

# Deactivate venv if it was activated
if [ -d "$PROJECT_ROOT/venv" ]; then
    deactivate
fi

echo "✅ Instagram feed generated successfully!"
echo "📍 File: $OUTPUT_FILE"
```

### Option B: Ohne venv

Falls du kein venv nutzen willst:
```bash
# System-weit installieren
pip install reelscraper

# Oder nur für User
pip install --user reelscraper
```

---

## .gitignore anpassen

Füge venv zum `.gitignore` hinzu:

```bash
echo "venv/" >> .gitignore
```

---

## Best Practices

### Für lokale Entwicklung (Garuda Linux):

**Mit venv:**
```bash
# Einmalig
python3 -m venv venv
source venv/bin/activate
pip install -r api/requirements.txt

# Danach immer:
source venv/bin/activate  # Bei Session-Start
npm run instagram:fetch   # Funktioniert nur wenn venv aktiviert
deactivate               # Bei Session-Ende
```

**Ohne venv:**
```bash
# Einmalig
pip install --user reelscraper

# Danach:
npm run instagram:fetch  # Funktioniert direkt
```

### Für Production (IONOS):

**IONOS nutzt System-Python**, daher:
```bash
# Auf IONOS (kein venv nötig)
pip3 install reelscraper

# Oder in Benutzer-Verzeichnis
pip3 install --user reelscraper
```

---

## Empfehlung für dein Projekt

### 🎯 Lokale Entwicklung: **venv verwenden**
- Sauber und isoliert
- Best Practice
- Kein Aufräumen nach Projekt-Ende nötig

### 🎯 IONOS Production: **System-Python**
- Kein venv auf Shared Hosting üblich
- `pip3 install --user` für User-Installation
- Cron-Jobs greifen auf User-Packages zu

---

## venv vs. System-Install - Vergleich

| Aspekt | venv | System-Install |
|--------|------|----------------|
| **Isolation** | ✅ Ja | ❌ Nein |
| **Konflikte** | ✅ Keine | ⚠️ Möglich |
| **Setup** | ⏱️ 30 Sekunden | ⏱️ 10 Sekunden |
| **Cleanup** | ✅ Einfach (venv löschen) | ❌ Manuell deinstallieren |
| **Best Practice** | ✅ Ja | ❌ Nicht empfohlen |
| **Für dieses Projekt** | ✅ Empfohlen | ⚠️ Funktioniert, aber... |

---

## FAQ

### Muss ich venv für jede Session aktivieren?

Ja, nach jedem Terminal-Neustart:
```bash
source venv/bin/activate
```

### Kann ich npm ohne venv nutzen?

Ja, aber dann muss `reelscraper` system-weit installiert sein:
```bash
pip install --user reelscraper
```

### Was ist der Unterschied zu pipenv?

- **venv**: Standard Python-Tool (eingebaut)
- **pipenv**: Externe Alternative mit mehr Features
- Für dieses Projekt: **venv ist ausreichend**

### Soll ich venv committen?

**Nein!** venv gehört in `.gitignore`:
```bash
venv/
__pycache__/
*.pyc
```

---

**Fazit:** Nutze venv für saubere lokale Entwicklung, auf IONOS System-Python.
