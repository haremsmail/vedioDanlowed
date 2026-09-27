#!/bin/bash

echo "🚀 Starting SaveTube..."

# Create all required directories
mkdir -p /app/storage/framework/cache/data
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/framework/testing
mkdir -p /app/storage/logs
mkdir -p /app/storage/downloads
mkdir -p /app/bootstrap/cache
mkdir -p /app/database

# Set open permissions (Railway runs as root, no www-data needed)
chmod -R 777 /app/storage
chmod -R 777 /app/bootstrap/cache
chmod -R 777 /app/database

# Create .env file with all required values
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

# Create SQLite database file
touch /app/database/database.sqlite
chmod 777 /app/database/database.sqlite

# Generate APP_KEY
echo "🔑 Generating APP_KEY..."
php artisan key:generate --force --no-interaction

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "🗃️ Running migrations..."
php artisan migrate --force --no-interaction

echo "✅ Starting on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
