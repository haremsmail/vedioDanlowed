#!/bin/bash
set -e

echo "🚀 Starting Video Downloader..."

# Create required directories
mkdir -p /app/storage/framework/{cache/data,sessions,views,testing}
mkdir -p /app/storage/logs
mkdir -p /app/storage/downloads
mkdir -p /app/bootstrap/cache
mkdir -p /app/database

# Auto-create a safe production .env file if it doesn't exist
if [ ! -f /app/.env ]; then
    echo "Creating safe default .env file..."
    echo "APP_NAME=SaveTube" > /app/.env
    echo "APP_ENV=production" >> /app/.env
    echo "APP_DEBUG=false" >> /app/.env
    echo "APP_KEY=" >> /app/.env
    echo "DB_CONNECTION=sqlite" >> /app/.env
    echo "DB_DATABASE=/app/database/database.sqlite" >> /app/.env
    echo "LOG_CHANNEL=stderr" >> /app/.env
    echo "SESSION_DRIVER=file" >> /app/.env
    echo "CACHE_DRIVER=file" >> /app/.env
fi

# Generate APP_KEY if missing
if ! grep -q "APP_KEY=base64:" /app/.env; then
    echo "🔑 Generating Laravel APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Create SQLite database
if [ ! -f /app/database/database.sqlite ]; then
    touch /app/database/database.sqlite
fi

# Set proper permissions so web server can write
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database /app/.env
chmod -R 775 /app/storage /app/bootstrap/cache

# Cache config for performance
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Run migrations
echo "🗃️ Running migrations..."
php artisan migrate --force --no-interaction

echo "✅ Ready! Starting server on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
