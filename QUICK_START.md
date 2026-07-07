# 🚀 Quick Start Guide

## 5-Minute Setup

### 1. Prerequisites
- PHP 8.1+
- Composer
- yt-dlp

### 2. Installation

#### Linux/macOS:
```bash
cd danlwoedvideio

# Install yt-dlp
sudo apt-get install yt-dlp  # Debian/Ubuntu
# OR
brew install yt-dlp  # macOS

# Run install script
chmod +x install.sh
./install.sh
```

#### Windows:
```bash
cd danlwoedvideio

# Install yt-dlp
pip install yt-dlp

# Run install script
install.bat
```

### 3. Find yt-dlp Path

```bash
# Linux/macOS
which yt-dlp

# Windows
where yt-dlp
```

### 4. Configure .env

Edit `.env` and set:
```env
YTDLP_PATH=/path/to/yt-dlp
APP_LOCALE=ku
```

### 5. Run Application

**Terminal 1 - Web Server:**
```bash
php artisan serve
```

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:work
```

### 6. Access
Visit: `http://localhost:8000`

## Common Issues

### yt-dlp not found
```bash
# Update .env with correct path
YTDLP_PATH=/usr/local/bin/yt-dlp
```

### Permission denied
```bash
chmod +x storage/downloads
chmod +x /usr/local/bin/yt-dlp
```

### Queue not processing
```bash
# Restart queue worker
php artisan queue:work --tries=3 --timeout=3600
```

### Database error
```bash
php artisan migrate:refresh
```

## Using Docker (Optional)

```bash
docker-compose up -d
# Visit http://localhost:8000
```

## API Examples

### Analyze Video
```bash
curl -X POST http://localhost:8000/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"url":"https://youtube.com/watch?v=dQw4w9WgXcQ"}'
```

### Download Video
```bash
curl -X POST http://localhost:8000/api/download \
  -H "Content-Type: application/json" \
  -d '{
    "url":"https://youtube.com/watch?v=dQw4w9WgXcQ",
    "format_id":"18",
    "quality":"360p"
  }'
```

### Check Status
```bash
curl http://localhost:8000/api/download/1
```

## Frontend Usage

1. **Enter URL** - Paste video link
2. **Click Analyze** - Get video info
3. **Select Quality** - Choose format
4. **Download** - Queue the download
5. **Track** - Check download status

## Admin Commands

```bash
# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear

# Flush queue
php artisan queue:flush

# Debug with tinker
php artisan tinker
```

---

**Note**: Application is in Kurdish by default. Change `APP_LOCALE=en` in `.env` for English.
