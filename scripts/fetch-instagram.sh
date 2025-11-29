#!/bin/bash
# Fetch Instagram feed and generate JSON
# Usage: ./scripts/fetch-instagram.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PYTHON_SCRIPT="$PROJECT_ROOT/api/instagram_feed.py"
OUTPUT_FILE="$PROJECT_ROOT/public/data/instagram-feed.json"

echo "🔍 Fetching Instagram feed..."

# Check if venv exists and activate it
VENV_ACTIVATED=0
if [ -d "$PROJECT_ROOT/venv" ]; then
    echo "📦 Using virtual environment"
    source "$PROJECT_ROOT/venv/bin/activate"
    VENV_ACTIVATED=1
fi

# Check if Python is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Error: Python 3 is not installed"
    exit 1
fi

# Check if reelscraper is installed
if ! python3 -c "import reelscraper" 2>/dev/null; then
    echo "📦 Installing reelscraper..."
    if [ $VENV_ACTIVATED -eq 1 ]; then
        pip install reelscraper
    else
        pip3 install reelscraper
    fi
fi

# Run the Python script
python3 "$PYTHON_SCRIPT" \
    wohl_fuehl_gesundheit \
    --max-posts 12 \
    --output "$OUTPUT_FILE"

# Deactivate venv if it was activated
if [ $VENV_ACTIVATED -eq 1 ]; then
    deactivate 2>/dev/null || true
fi

echo "✅ Instagram feed generated successfully!"
echo "📍 File: $OUTPUT_FILE"
