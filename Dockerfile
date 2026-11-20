FROM php:8.3-cli

WORKDIR /app

# System packages: ffmpeg (merge video+audio, MP3), python (yt-dlp)
RUN apt-get update && apt-get install -y --no-install-recommends \
        ffmpeg python3 python3-venv ca-certificates curl unzip git \
        libzip-dev libsqlite3-dev libicu-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_sqlite zip intl opcache pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Deno = JavaScript runtime that yt-dlp needs for YouTube (without it YouTube downloads fail)
COPY --from=denoland/deno:bin /deno /usr/local/bin/deno

# yt-dlp (+ curl-cffi browser impersonation, helps TikTok / Instagram / Facebook)
RUN python3 -m venv /opt/ytdlp \
    && /opt/ytdlp/bin/pip install --no-cache-dir --upgrade "yt-dlp[default,curl-cffi]" \
    && ln -s /opt/ytdlp/bin/yt-dlp /usr/local/bin/yt-dlp

# PHP tuning (opcache = much faster page loads)
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
                storage/logs storage/downloads storage/app/jobs bootstrap/cache database \
    && sed -i 's/\r$//' docker-entrypoint.sh && chmod +x docker-entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 8000
CMD ["./docker-entrypoint.sh"]
