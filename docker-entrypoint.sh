#!/bin/bash
set -e

echo "🚀 Starting Video Downloader..."

# Create required storage directories
mkdir -p /app/storage/framework/{cache/data,sessions,views,testing}
mkdir -p /app/storage/logs
mkdir -p /app/storage/downloads
mkdir -p /app/bootstrap/cache

# Set permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Note: PostgreSQL database will be managed by the db container or Render managed DB

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
