#!/bin/bash
set -e

echo "🚀 Starting Video Downloader..."

# Create required storage directories
mkdir -p /app/storage/framework/{cache/data,sessions,views,testing}
mkdir -p /app/storage/logs
mkdir -p /app/storage/downloads
mkdir -p /app/bootstrap/cache

# Set permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database
chmod -R 775 /app/storage /app/bootstrap/cache

# Generate APP_KEY if not set or not in Laravel base64 format
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]]; then
    echo "🔑 Generating Laravel APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Create SQLite database if using sqlite driver
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f /app/database/database.sqlite ]; then
        touch /app/database/database.sqlite
        chown www-data:www-data /app/database/database.sqlite
    fi
fi

# Cache config for performance
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Run migrations
echo "🗃️ Running migrations..."
php artisan migrate --force --no-interaction

echo "✅ Ready! Starting server on port ${PORT:-8000}..."

# Start the application (Render sets PORT env variable)
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
