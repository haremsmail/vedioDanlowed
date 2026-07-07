# Laravel Video Downloader Project

## Project Overview
A full-featured Laravel web application for downloading videos from YouTube, TikTok, Instagram, and Facebook with:
- REST API endpoints for video analysis and downloading
- Queue system for async downloads
- Beautiful Blade UI with Kurdish language support
- Rate limiting and security features
- yt-dlp integration for video processing

## Project Structure
```
danlwoedvideio/
├── app/Http/Controllers/VideoController.php - API endpoints
├── app/Services/YtDlpService.php - yt-dlp integration
├── app/Jobs/ProcessVideoDownload.php - Queue job
├── app/Models/Download.php - Download model
├── app/Http/Middleware/ - Rate limiting & security
├── resources/views/index.blade.php - UI template
├── resources/lang/ku/ - Kurdish translations
├── routes/ - API and web routes
├── config/ - Configuration files
└── storage/downloads/ - Downloaded files
```

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Install yt-dlp
- **Linux**: `sudo apt-get install yt-dlp`
- **macOS**: `brew install yt-dlp`
- **Windows**: `pip install yt-dlp`

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:
```
YTDLP_PATH=/usr/local/bin/yt-dlp  # Adjust for your system
QUEUE_CONNECTION=database
APP_LOCALE=ku
```

### 4. Setup Database
```bash
php artisan migrate
```

### 5. Start Services
```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work
```

### 6. Access Application
Visit: http://localhost:8000

## Key Features Implemented

✅ REST API (/api/analyze, /api/download, /api/download/{id})
✅ Video metadata extraction (title, thumbnail, duration)
✅ Multiple quality formats (360p, 720p, 1080p, audio)
✅ Queue-based async downloads
✅ Input validation & security
✅ Rate limiting (30 requests/minute)
✅ Kurdish & English translations
✅ Responsive Blade UI with RTL support
✅ Error handling & logging
✅ Download tracking

## API Endpoints

### POST /api/analyze
Analyze video and get metadata
```json
{"url": "https://youtube.com/watch?v=..."}
```

### POST /api/download
Queue video download
```json
{"url": "https://...", "format_id": "18", "quality": "360p"}
```

### GET /api/download/{id}
Check download status

### GET /download/{file}
Download completed file

## Configuration Files

- `.env.example` - Environment variables template
- `config/ytdlp.php` - yt-dlp settings
- `config/ratelimit.php` - Rate limiting settings
- `app/Services/YtDlpService.php` - Command execution logic

## Middleware

- `RateLimitMiddleware` - Rate limiting (30/min)
- `EncryptCookies` - Cookie encryption
- `VerifyCsrfToken` - CSRF protection
- `Authenticate` - Authentication

## Database

SQLite by default (database.sqlite). Update `DB_CONNECTION` in `.env` for MySQL.

## Language Support

- **Kurdish** (كوردی): `resources/lang/ku/messages.php`
- **English**: `resources/lang/en/messages.php`

Set default language in `.env`: `APP_LOCALE=ku`

## Troubleshooting

**yt-dlp not found**: Update `YTDLP_PATH` in `.env`
**Queue not working**: Run `php artisan queue:work`
**Permission denied**: `chmod +x storage/downloads`

## Next Steps

1. Install PHP dependencies: `composer install`
2. Install yt-dlp
3. Copy and configure `.env` file
4. Run migrations: `php artisan migrate`
5. Start queue worker: `php artisan queue:work`
6. Start dev server: `php artisan serve`
7. Open http://localhost:8000

---
Built with Laravel 11, yt-dlp, and modern web technologies.
