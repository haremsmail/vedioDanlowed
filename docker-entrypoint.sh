#!/bin/bash

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

# 4. Create .env file with all required values
cat > /app/.env << 'EOF'
APP_NAME=SaveTube
APP_ENV=production
APP_DEBUG=false
APP_KEY=
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite
LOG_CHANNEL=stderr
LOG_LEVEL=warning
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
EOF

# 5. Create SQLite database file
touch /app/database/database.sqlite
chmod 777 /app/database/database.sqlite

# 6. Generate APP_KEY
echo "🔑 Generating APP_KEY..."
php artisan key:generate --force --no-interaction

# 7. Run migrations
echo "🗃️ Running migrations..."
php artisan migrate --force --no-interaction

# 8. Cache routes and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Starting on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
