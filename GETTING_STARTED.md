# 🎬 Laravel Video Downloader - Getting Started

**Version:** 1.0 | **Status:** ✅ Ready to Deploy | **Created:** 2024

---

## ✨ What You Have

A complete, production-ready Laravel web application for downloading videos from YouTube, TikTok, Instagram, and Facebook with:

- ✅ Beautiful responsive web interface
- ✅ REST API endpoints for programmatic access
- ✅ Async queue-based downloads
- ✅ Multi-language support (Kurdish + English)
- ✅ Rate limiting and security features
- ✅ Docker support for deployment
- ✅ Complete documentation

---

## ⚡ Get Started in 10 Minutes

### Step 1: Install yt-dlp (2 minutes)

Your system needs yt-dlp to download videos.

#### Windows:
```bash
pip install yt-dlp
```

#### Linux (Ubuntu/Debian):
```bash
pip install yt-dlp
# OR
sudo apt-get install yt-dlp
```

#### macOS:
```bash
brew install yt-dlp
```

**Verify:**
```bash
yt-dlp --version  # Should show version number
```

---

### Step 2: Find yt-dlp Path (1 minute)

You need the full path to yt-dlp.

#### Windows (PowerShell):
```powershell
where.exe yt-dlp
# Example output: C:\Python39\Scripts\yt-dlp.exe
```

#### Linux/macOS:
```bash
which yt-dlp
# Example output: /usr/local/bin/yt-dlp
```

**Save this path** - you'll need it in Step 3.

---

### Step 3: Configure Project (3 minutes)

Open the project folder and configure it:

```bash
# Navigate to project
cd c:\Desktop\danlwoedvideio

# Install PHP dependencies
composer install

# Copy configuration file
cp .env.example .env

# Generate encryption key
php artisan key:generate

# Create database
php artisan migrate
```

---

### Step 4: Update .env File (2 minutes)

1. Open `.env` in your text editor
2. Find the line: `YTDLP_PATH=`
3. Replace it with your path from Step 2:

```env
# Windows example
YTDLP_PATH=C:\Python39\Scripts\yt-dlp.exe

# Linux/macOS example
YTDLP_PATH=/usr/local/bin/yt-dlp
```

4. Save the file

---

### Step 5: Run Application (2 minutes)

#### Terminal 1 - Start Web Server:
```bash
php artisan serve
```

You'll see:
```
Starting Laravel development server: http://127.0.0.1:8000
```

#### Terminal 2 - Start Queue Worker:
Open a **NEW terminal** in the same project folder:

```bash
php artisan queue:work
```

You'll see:
```
Processing jobs from the 'default' queue.
```

---

### Step 6: Open in Browser

Visit: **http://localhost:8000** 🎉

You should see the Video Downloader interface!

---

## 🎮 First Test

1. **Paste a video URL** from YouTube, TikTok, Instagram, or Facebook
2. **Click "Analyze"** button (or "شیکاری کردن" in Kurdish)
3. **Select quality** (360p, 720p, 1080p, or audio)
4. **Click "Download"** (or "داونلۆد کردن" in Kurdish)
5. **Check download** - Watch Terminal 2 for processing
6. **Find file** - Downloaded videos are in `storage/downloads/`

---

## 📱 Both Terminals Must Be Running

| Terminal 1 | Terminal 2 |
|-----------|-----------|
| `php artisan serve` | `php artisan queue:work` |
| Web server (port 8000) | Job processor (downloads) |
| **Keep running** ✓ | **Keep running** ✓ |
| Ctrl+C to stop | Ctrl+C to stop |

---

## 🌍 Change Language

Edit `.env` and set:

```env
APP_LOCALE=ku  # Kurdish (كوردی) - DEFAULT
APP_LOCALE=en  # English
```

Reload http://localhost:8000

---

## 🐛 Quick Troubleshooting

### Problem: "yt-dlp command not found"
```bash
# Make sure yt-dlp is installed
yt-dlp --version

# Check your YTDLP_PATH in .env is correct
```

### Problem: "Port 8000 is already in use"
```bash
# Use different port
php artisan serve --port=8001
```

### Problem: "Queue not processing"
Make sure Terminal 2 is running with `php artisan queue:work`

### Problem: "Database error"
```bash
# Recreate database
php artisan migrate:refresh
```

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for more solutions.

---

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| [QUICK_START.md](QUICK_START.md) | Quick commands reference |
| [SETUP.md](SETUP.md) | Detailed setup guide |
| [API.md](API.md) | REST API documentation |
| [ARCHITECTURE.md](ARCHITECTURE.md) | How the system works |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Problem solutions |

---

## 🔌 API Examples

### Analyze Video
```bash
curl -X POST http://localhost:8000/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"url":"https://www.youtube.com/watch?v=..."}'
```

### Queue Download
```bash
curl -X POST http://localhost:8000/api/download \
  -H "Content-Type: application/json" \
  -d '{
    "url":"https://www.youtube.com/watch?v=...",
    "format_id":"18",
    "quality":"360p"
  }'
```

### Check Download Status
```bash
curl http://localhost:8000/api/download/1
```

---

## 🚀 Supported Platforms

| Platform | Status | Notes |
|----------|--------|-------|
| YouTube | ✅ Works | All quality options |
| TikTok | ✅ Works | Watermark removal available |
| Instagram | ✅ Works | Stories, Reels, Posts |
| Facebook | ✅ Works | Videos only |

---

## ⚙️ Useful Commands

```bash
# Development
php artisan serve                    # Start web server
php artisan queue:work               # Start queue worker
php artisan tinker                   # Debug console

# Database
php artisan migrate                  # Run migrations
php artisan migrate:refresh          # Reset database
php artisan migrate:status           # Check status

# Debugging
php artisan about                    # System info
php artisan routes:list              # Show all routes

# Cache
php artisan cache:clear              # Clear cache
php artisan config:cache             # Cache config
```

---

## 🔒 Security Features

- ✅ CSRF token protection
- ✅ Input validation
- ✅ Rate limiting (30 requests/min per IP)
- ✅ Safe command execution (no shell injection)
- ✅ File path validation (no directory traversal)
- ✅ HTTPS ready for production

---

## 📊 Project Structure

```
danlwoedvideio/
├── app/                          # Application code
│   ├── Http/Controllers/         # API & Web controllers
│   ├── Services/                 # yt-dlp service
│   ├── Jobs/                     # Queue jobs
│   ├── Models/                   # Database models
│   └── Middleware/               # Request middleware
├── resources/
│   ├── views/                    # HTML templates
│   └── lang/                     # Translations (ku, en)
├── routes/                       # URL routes
├── config/                       # Configuration
├── database/
│   ├── migrations/               # Schema files
│   └── database.sqlite           # SQLite database
├── storage/
│   └── downloads/                # Downloaded videos
├── .env                          # Configuration (your settings)
└── README.md, SETUP.md, API.md, ... (Documentation)
```

---

## 💾 Database

Uses **SQLite** by default (no setup needed).

For production with **MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=videodl
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

## 🐳 Docker Deployment

```bash
# Start with Docker
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# View logs
docker-compose logs -f

# Stop
docker-compose down
```

---

## 🎯 Next Steps

1. ✅ **Quick Test** - Analyze a YouTube video
2. ✅ **Download Test** - Queue a download
3. ✅ **Language Test** - Switch between Kurdish and English
4. ✅ **API Test** - Try curl commands
5. 🚀 **Deploy** - Move to production server

---

## 📞 Support Resources

- 📄 [README.md](README.md) - Full documentation
- ⚡ [QUICK_START.md](QUICK_START.md) - Quick reference
- 🔧 [SETUP.md](SETUP.md) - Detailed setup
- 🐛 [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Problem solutions
- 📡 [API.md](API.md) - API documentation

---

## ✨ Summary

Your application has everything needed for a production video downloader:

- ✅ Complete backend with API
- ✅ Beautiful web interface
- ✅ Multi-language support
- ✅ Async downloading
- ✅ Security features
- ✅ Complete documentation
- ✅ Docker support

**Time to get started: Just follow the steps above! ⏱️**

---

**Questions?** Check the documentation files or the [TROUBLESHOOTING.md](TROUBLESHOOTING.md) guide.

**Ready to go?** → http://localhost:8000 🚀
