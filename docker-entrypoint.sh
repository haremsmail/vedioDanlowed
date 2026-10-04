#!/bin/bash
set -e

echo "🚀 Starting SaveTube..."

# 1. Immediately delete any stale bootstrap cache files BEFORE Laravel boots
rm -f /app/bootstrap/cache/*.php /app/bootstrap/cache/*.tmp 2>/dev/null || true

# 2. Create all required directories
mkdir -p /app/storage/framework/cache/data
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/framework/testing
mkdir -p /app/storage/logs
mkdir -p /app/storage/downloads
mkdir -p /app/bootstrap/cache
mkdir -p /app/database

# 3. Set open permissions
chmod -R 777 /app/storage
chmod -R 777 /app/bootstrap/cache
chmod -R 777 /app/database

# 4. Update yt-dlp to latest version (fixes broken downloads on TikTok, YouTube, etc.)
echo "⬆️  Updating yt-dlp to latest version..."
/opt/ytdlp-venv/bin/pip install --no-cache-dir --upgrade yt-dlp 2>/dev/null && echo "✅ yt-dlp updated" || echo "⚠️  yt-dlp update skipped (using existing)"

# 5. Determine the public APP_URL
# Railway provides RAILWAY_PUBLIC_DOMAIN or RAILWAY_STATIC_URL automatically
if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    DETECTED_URL="https://${RAILWAY_PUBLIC_DOMAIN}"
elif [ -n "$RAILWAY_STATIC_URL" ]; then
    DETECTED_URL="https://${RAILWAY_STATIC_URL}"
elif [ -n "$APP_URL" ] && [ "$APP_URL" != "http://localhost" ]; then
    # Already set by user — ensure https://
    DETECTED_URL=$(echo "$APP_URL" | sed 's|^http://|https://|')
else
    DETECTED_URL="http://localhost"
fi

echo "🌐 APP_URL detected: $DETECTED_URL"

# 6. Create .env file with all required values
cat > /app/.env << EOF
APP_NAME=SaveTube
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_URL=${DETECTED_URL}
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite
LOG_CHANNEL=stderr
LOG_LEVEL=warning
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
TRUSTED_PROXIES=*
EOF

# 7. Create SQLite database file
touch /app/database/database.sqlite
chmod 777 /app/database/database.sqlite

# 8. Generate APP_KEY
echo "🔑 Generating APP_KEY..."
php artisan key:generate --force --no-interaction

# 9. Run migrations
echo "🗃️ Running migrations..."
php artisan migrate --force --no-interaction

# 10. Cache routes and views (non-blocking)
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

echo "✅ Starting on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
