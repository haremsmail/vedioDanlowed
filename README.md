# 🎬 Video Downloader - Laravel Web Application

A full-featured Laravel video downloader web application that supports downloading videos from YouTube, TikTok, Instagram, Snapchat, and many other platforms with multiple quality options.

## 📋 Features

- ✅ **Multi-Platform Support**: YouTube, TikTok, Instagram, Snapchat, Facebook, Twitch, and 1000+ platforms
- ✅ **Multiple Quality Options**: 360p, 480p, 720p, 1080p, 1440p, 2160p (4K), Audio-only
- ✅ **REST API Endpoints**: Complete API for video analysis and downloading
- ✅ **Beautiful Blade UI**: Clean, responsive, dark-mode interface
- ✅ **Kurdish Language Support**: Full Kurdish (Sorani) translations with natural phrasing
- ✅ **English Support**: Complete English localization
- ✅ **Rate Limiting**: Protect against abuse (30 requests/minute)
- ✅ **Input Validation**: Secure URL validation
- ✅ **Command Injection Protection**: Safe shell command execution
- ✅ **Error Handling**: Graceful error messages in multiple languages

## 📦 System Requirements

- PHP 8.1 or higher
- Laravel 11.x
- SQLite or MySQL
- yt-dlp (command-line tool)
- Composer

## 🔧 Installation & Setup

### 1. Clone or Extract the Project

```bash
cd danlwoedvideio
```

### 2. Install yt-dlp

**On Linux/macOS:**
```bash
# Using pip
pip install yt-dlp

# Or using apt (Debian/Ubuntu)
sudo apt-get install yt-dlp

# Or using brew (macOS)
brew install yt-dlp
```

**On Windows:**
```bash
# Using pip
pip install yt-dlp

# Or download binary from:
# https://github.com/yt-dlp/yt-dlp/releases
```

**Verify installation:**
```bash
yt-dlp --version
```

### 3. Install PHP Dependencies

```bash
composer install
```

### 4. Setup Environment

```bash
# Copy example env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Environment

Edit `.env` file and set:

```env
# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ku
APP_FALLBACK_LOCALE=en

# Database
DB_CONNECTION=sqlite

# Queue
QUEUE_CONNECTION=database

# yt-dlp Path (adjust according to your system)
YTDLP_PATH=/usr/local/bin/yt-dlp

# For Windows:
YTDLP_PATH=C:\\Python39\\Scripts\\yt-dlp.exe

# Downloads
DOWNLOADS_PATH=/storage/downloads
MAX_DOWNLOAD_SIZE=10737418240  # 10GB

# Rate Limiting
RATE_LIMIT=30
RATE_LIMIT_PERIOD=60
```

### 6. Create Database

```bash
# For SQLite (default)
php artisan migrate

# For MySQL, update .env and run
php artisan migrate
```

### 7. Start Queue Worker (for async downloads)

```bash
# In a separate terminal
php artisan queue:work
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📡 API Endpoints

### 1. Analyze Video
**POST** `/api/analyze`

Request:
```json
{
  "url": "https://www.youtube.com/watch?v=..."
}
```

Response:
```json
{
  "success": true,
  "data": {
    "title": "Video Title",
    "thumbnail": "https://...",
    "duration": 300,
    "formats": [
      {
        "id": "18",
        "format": "video",
        "quality": "360p",
        "height": 360,
        "ext": "mp4"
      }
    ]
  }
}
```

### 2. Download Video
**POST** `/api/download`

Request:
```json
{
  "url": "https://www.youtube.com/watch?v=...",
  "format_id": "18",
  "quality": "360p"
}
```

Response:
```json
{
  "success": true,
  "message": "Your video is ready! Download will start...",
  "download_url": "http://localhost:8000/download/v_abc123.mp4",
  "filename": "v_abc123.mp4"
}
```

## 🏗️ Project Structure

```
danlwoedvideio/
├── app/
│   ├── Http/
│   │   ├── Controllers/VideoController.php
│   │   └── Middleware/
│   ├── Services/YtDlpService.php
│   ├── Jobs/ProcessVideoDownload.php
│   └── Models/Download.php
├── resources/
│   ├── views/index.blade.php
│   └── lang/
│       ├── ku/messages.php (Kurdish)
│       └── en/messages.php (English)
├── routes/
│   ├── web.php
│   └── api.php
├── database/
│   └── migrations/
├── config/
│   ├── ytdlp.php
│   └── ratelimit.php
└── storage/
    └── downloads/
```

## 🔐 Security Features

1. **URL Validation**: Only accepts valid URLs
2. **Platform Validation**: Only accepts supported platforms
3. **Command Injection Prevention**: Uses proper array-based command execution
4. **File Path Validation**: Prevents directory traversal attacks
5. **Rate Limiting**: Protects against abuse
6. **CSRF Protection**: Token validation for POST requests
7. **Input Sanitization**: All user inputs are validated

## 🌍 Language Support

### Kurdish (كوردی)
- File: `resources/lang/ku/messages.php`
- Set in `.env`: `APP_LOCALE=ku`

### English
- File: `resources/lang/en/messages.php`
- Set in `.env`: `APP_LOCALE=en`

## ⚙️ Configuration

### YT-DLP Config (`config/ytdlp.php`)
- Supported platforms
- Download path
- Timeout settings
- Max download size

### Rate Limit Config (`config/ratelimit.php`)
- Max attempts per period
- Time period in seconds

## 🚀 Usage

### Web Interface
1. Open `http://localhost:8000`
2. Paste video URL
3. Click "Analyze"
4. Select quality
5. Click "Download"
6. Track download status

### API Usage
```bash
# Analyze video
curl -X POST http://localhost:8000/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"url":"https://youtube.com/watch?v=..."}'

# Download video
curl -X POST http://localhost:8000/api/download \
  -H "Content-Type: application/json" \
  -d '{"url":"https://youtube.com/watch?v=...","format_id":"18","quality":"360p"}'

# Check status
curl http://localhost:8000/api/download/1
```

## 📝 Database

### Downloads Table
```
id              - Primary key
url             - Video URL
title           - Video title
format_id       - Selected format ID
quality         - Selected quality
status          - pending|downloading|completed|failed
file_path       - Path to downloaded file
error_message   - Error message if failed
ip_address      - Client IP address
created_at      - Created timestamp
updated_at      - Updated timestamp
```

## 🔄 Queue Processing

### Start Queue Worker
```bash
php artisan queue:work
```

### Process Failed Jobs
```bash
php artisan queue:retry all
```

### Clear Queue
```bash
php artisan queue:flush
```

## 🐛 Troubleshooting

### yt-dlp not found
- Verify yt-dlp is installed: `yt-dlp --version`
- Update `YTDLP_PATH` in `.env`

### Permission denied
```bash
chmod +x storage/downloads
sudo chmod +x /usr/local/bin/yt-dlp
```

### Queue not processing
```bash
# Check queue worker is running
php artisan queue:work

# Clear database queue
php artisan queue:flush
```

### Large downloads failing
- Increase `MAX_DOWNLOAD_SIZE` in `.env`
- Increase PHP timeout: `max_execution_time`

## 📚 Commands

```bash
# Run migrations
php artisan migrate

# Create migration
php artisan make:migration create_downloads_table

# Clear cache
php artisan cache:clear

# Clear config
php artisan config:clear

# Queue work
php artisan queue:work

# Tinker (debug console)
php artisan tinker
```

## 🎨 Frontend

The frontend is built with:
- HTML5
- CSS3 (Grid, Flexbox, Animations)
- Vanilla JavaScript (No dependencies)
- Responsive design
- RTL support for Kurdish

## 🆕 Latest Updates (2025)

### Major Additions:
- **Snapchat Support**: Download videos and stories from Snapchat
- **Enhanced Kurdish Language**: Improved terminology and natural phrasing throughout the interface
- **Expanded Platform Support**: 1000+ platforms supported (including Twitch, BBC, Reddit, Bilibili, and more)

## 📄 License

MIT License

## 👨‍💻 Author

Video Downloader - 2024-2025

## 📞 Support

For issues and support:
1. Check the [API Endpoints](#-api-endpoints) documentation
2. Review [Troubleshooting](#-troubleshooting) section
3. Check error messages in `.env` logs

## 🎯 Future Enhancements

- [ ] Download history page
- [ ] User authentication
- [ ] Playlist support
- [ ] Batch downloads
- [ ] Download scheduling
- [ ] Video conversion options
- [ ] Admin dashboard
- [ ] User statistics

---

**Disclaimer**: This application is provided for educational and administrative utility. Respect copyright laws and platform terms of service when downloading content.
