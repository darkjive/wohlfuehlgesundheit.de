#!/bin/bash

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo "❌ .env file not found!"
    echo "Please create a .env file with your SFTP credentials."
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

echo "🚀 Starting deployment..."
echo "📦 Uploading files to $SFTP_HOST..."

# Use lftp for SFTP deployment with mirror command
lftp -u "$SFTP_USER","$SFTP_PASSWORD" sftp://"$SFTP_HOST":"${SFTP_PORT:-22}" <<EOF
set sftp:auto-confirm yes
set ssl:verify-certificate no
cd $SFTP_REMOTE_PATH
mirror --reverse --delete --verbose --exclude .git/ --exclude .gitignore dist/ ./
bye
EOF

if [ $? -eq 0 ]; then
    echo "✅ Deployment successful!"
else
    echo "❌ Deployment failed!"
    exit 1
fi
