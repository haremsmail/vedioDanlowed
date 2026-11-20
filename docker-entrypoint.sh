#!/bin/bash
set -e
cd /app

echo "Starting video downloader..."

rm -f bootstrap/cache/*.php 2>/dev/null || true
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs storage/downloads storage/app/jobs storage/app/yt-dlp-cache database

# ── 1. Public URL (Render / Railway / Koyeb set these automatically) ──────────
if   [ -n "$RENDER_EXTERNAL_URL" ];      then DETECTED_URL="$RENDER_EXTERNAL_URL"
elif [ -n "$RAILWAY_PUBLIC_DOMAIN" ];    then DETECTED_URL="https://${RAILWAY_PUBLIC_DOMAIN}"
elif [ -n "$KOYEB_PUBLIC_DOMAIN" ];      then DETECTED_URL="https://${KOYEB_PUBLIC_DOMAIN}"
elif [ -n "$APP_URL" ];                  then DETECTED_URL="$APP_URL"
else DETECTED_URL="http://localhost:${PORT:-8000}"
fi
export APP_URL="$DETECTED_URL"
echo "APP_URL: $APP_URL"

# ── 2. Defaults (real environment variables always win over .env) ─────────────
cat > .env <<EOF
APP_NAME="Video Downloader"
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_LOCALE=ku
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=Asia/Baghdad
LOG_CHANNEL=stderr
LOG_LEVEL=warning
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
TRUSTED_PROXIES=*
EOF

touch database/database.sqlite

# APP_KEY: use the one from the hosting dashboard, otherwise generate one
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force --no-interaction >/dev/null
    echo "WARNING: APP_KEY not set - generated a temporary one (set APP_KEY in your host for stable sessions)."
fi

# ── 3. Cookies for YouTube / Instagram (optional but strongly recommended) ────
#   Option A: Render "Secret File" named cookies.txt  -> /etc/secrets/cookies.txt
#   Option B: env var YTDLP_COOKIES_B64 = base64 of your cookies.txt
if [ -f /etc/secrets/cookies.txt ]; then
    cp /etc/secrets/cookies.txt storage/app/cookies.txt
    echo "cookies.txt loaded from secret file"
elif [ -n "$YTDLP_COOKIES_B64" ]; then
    echo "$YTDLP_COOKIES_B64" | base64 -d > storage/app/cookies.txt 2>/dev/null && echo "cookies.txt loaded from env"
fi
[ -f storage/app/cookies.txt ] && chmod 600 storage/app/cookies.txt

# ── 4. Keep yt-dlp fresh (sites change often; old versions stop working) ──────
timeout 60 /opt/ytdlp/bin/pip install -q --no-cache-dir --upgrade "yt-dlp[default,curl-cffi]" 2>/dev/null \
    && echo "yt-dlp: $(yt-dlp --version)" || echo "yt-dlp update skipped"

php artisan migrate --force --no-interaction >/dev/null 2>&1 || true
php artisan config:cache >/dev/null
php artisan route:cache  >/dev/null
php artisan view:cache   >/dev/null

chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null || true

# ── 5. Background services ────────────────────────────────────────────────────
# download worker (auto-restarts if it ever crashes)
( while true; do php artisan video:worker; echo "worker restarted"; sleep 2; done ) &
# cleanup of old files every 5 minutes
php artisan schedule:work >/dev/null 2>&1 &

# ── 6. Web server: several workers so one slow request never blocks the site ──
export PHP_CLI_SERVER_WORKERS="${WEB_WORKERS:-8}"
echo "Listening on port ${PORT:-8000} with $PHP_CLI_SERVER_WORKERS workers"
cd /app/public
exec php -S 0.0.0.0:"${PORT:-8000}" /app/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
