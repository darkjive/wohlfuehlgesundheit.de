#!/bin/bash

# Parse command line arguments
TEST_MODE=false
if [ "$1" == "--test" ]; then
    TEST_MODE=true
fi

# Load environment variables
if [ -f public/api/.env ]; then
    # Source the .env file properly, handling special characters
    set -a
    source <(grep -E '^SFTP_' public/api/.env | grep -v '^#')
    set +a
else
    echo "❌ .env file not found at public/api/.env!"
    echo "Please create a public/api/.env file with your SFTP credentials."
    echo "See .env.example for reference."
    exit 1
fi

# Check if required variables are set
if [ -z "$SFTP_HOST" ] || [ -z "$SFTP_USER" ] || [ -z "$SFTP_PASSWORD" ] || [ -z "$SFTP_REMOTE_PATH" ]; then
    echo "❌ Missing required environment variables in .env file!"
    echo "Required: SFTP_HOST, SFTP_USER, SFTP_PASSWORD, SFTP_REMOTE_PATH"
    exit 1
fi

# Check if dist folder exists
if [ ! -d "dist" ]; then
    echo "❌ dist folder not found!"
    echo "Please run 'npm run build' first."
    exit 1
fi

# Set deployment path based on mode
if [ "$TEST_MODE" == "true" ]; then
    DEPLOY_PATH="$SFTP_REMOTE_PATH/test"
    echo "🧪 Test deployment mode enabled"
    echo "🚀 Starting test deployment..."
    echo "📦 Uploading files to $SFTP_HOST$DEPLOY_PATH..."
else
    DEPLOY_PATH="$SFTP_REMOTE_PATH"
    echo "🚀 Starting production deployment..."
    echo "📦 Uploading files to $SFTP_HOST..."
fi

# Use lftp for SFTP deployment with mirror command
lftp -u "$SFTP_USER","$SFTP_PASSWORD" sftp://"$SFTP_HOST":"${SFTP_PORT:-22}" <<EOF
set sftp:auto-confirm yes
set ssl:verify-certificate no
cd $DEPLOY_PATH
mirror --reverse --delete --verbose --exclude .git/ --exclude .gitignore --exclude-glob logs/ --exclude-glob logs/* dist/ ./
bye
EOF

if [ $? -eq 0 ]; then
    echo "✅ Deployment successful!"
else
    echo "❌ Deployment failed!"
    exit 1
fi
