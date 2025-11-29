#!/bin/bash
# Fetch Instagram feed and generate JSON
# Usage: ./scripts/fetch-instagram.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PYTHON_SCRIPT="$PROJECT_ROOT/api/instagram_feed.py"
OUTPUT_FILE="$PROJECT_ROOT/public/data/instagram-feed.json"

echo "🔍 Fetching Instagram feed..."

# Check if Python is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Error: Python 3 is not installed"
    exit 1
fi

# Check if reelscraper is installed
if ! python3 -c "import reelscraper" 2>/dev/null; then
    echo "📦 Installing reelscraper..."
    pip3 install reelscraper
fi

# Run the Python script
python3 "$PYTHON_SCRIPT" \
    wohl_fuehl_gesundheit \
    --max-posts 12 \
    --output "$OUTPUT_FILE"

echo "✅ Instagram feed generated successfully!"
echo "📍 File: $OUTPUT_FILE"
