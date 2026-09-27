FROM php:8.2-cli

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    libsqlite3-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    ffmpeg \
    python3 \
    python3-pip \
    python3-venv \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install yt-dlp (latest version via pip)
RUN python3 -m venv /opt/ytdlp-venv \
    && /opt/ytdlp-venv/bin/pip install --no-cache-dir yt-dlp \
    && ln -s /opt/ytdlp-venv/bin/yt-dlp /usr/local/bin/yt-dlp

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_sqlite \
        pdo_pgsql \
        pdo_mysql \
        bcmath \
        zip \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Force cache bust for fresh build without old cached layers
ARG CACHE_BUST=20260927-v1

# Copy composer files first to guarantee clean dependency resolution
COPY composer.json composer.lock ./

# Install production dependencies directly from lock file (clean build)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Copy project files
COPY . .

# Complete composer dump-autoload cleanly without any dev packages
RUN composer dump-autoload --optimize --no-dev

# Create required storage directories
RUN mkdir -p storage/framework/{cache/data,sessions,views,testing} \
    && mkdir -p storage/logs \
    && mkdir -p storage/downloads \
    && mkdir -p bootstrap/cache \
    && mkdir -p database

# Fix line endings if written on Windows and ensure executable permissions
RUN sed -i 's/\r$//' docker-entrypoint.sh \
    && chmod +x docker-entrypoint.sh

# Set permissions
RUN chmod -R 777 /app/storage /app/bootstrap/cache /app/database

# Expose port (Railway sets PORT env variable)
EXPOSE 8000

# Run entrypoint script
CMD ["./docker-entrypoint.sh"]
