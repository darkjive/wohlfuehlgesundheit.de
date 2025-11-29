#!/bin/bash
# Healthcheck for Instagram Feed
# Checks if feed is up-to-date and contains posts

set -e

FEED_FILE="public/data/instagram-feed.json"
MAX_AGE_HOURS=48

# Check if feed file exists
if [ ! -f "$FEED_FILE" ]; then
    echo "❌ ERROR: Feed file not found: $FEED_FILE"
    exit 1
fi

# Check file age
AGE_SECONDS=$(( $(date +%s) - $(stat -c %Y "$FEED_FILE") ))
AGE_HOURS=$(( AGE_SECONDS / 3600 ))

if [ $AGE_HOURS -gt $MAX_AGE_HOURS ]; then
    echo "⚠️  WARNING: Feed is $AGE_HOURS hours old (max: $MAX_AGE_HOURS hours)"
    echo "   Run: npm run instagram:fetch"
    exit 1
fi

# Check if jq is available
if ! command -v jq &> /dev/null; then
    echo "⚠️  WARNING: jq not installed, skipping content validation"
    echo "   Install: sudo pacman -S jq"
    echo "✅ Feed file exists and is recent ($AGE_HOURS hours old)"
    exit 0
fi

# Check if feed contains posts
POST_COUNT=$(cat "$FEED_FILE" | jq -r '.postsCount // 0')
if [ $POST_COUNT -eq 0 ]; then
    echo "❌ ERROR: Feed contains no posts"
    echo "   Run: npm run instagram:fetch"
    exit 1
fi

# Get feed metadata
USERNAME=$(cat "$FEED_FILE" | jq -r '.username // "unknown"')
FETCHED_AT=$(cat "$FEED_FILE" | jq -r '.fetchedAt // "unknown"')

# Success
echo "✅ Feed is healthy!"
echo "   Username: @$USERNAME"
echo "   Posts: $POST_COUNT"
echo "   Age: $AGE_HOURS hours"
echo "   Last fetched: $FETCHED_AT"
exit 0
