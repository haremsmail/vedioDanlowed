# ✅ Complete Setup & Verification Guide

This guide will walk you through setting up the Video Downloader application from scratch.

## Prerequisites Checklist

Before starting, ensure you have:

### Required
- [ ] PHP 8.1 or higher installed
- [ ] Composer installed
- [ ] yt-dlp installed
- [ ] Git (optional)

### Optional but Recommended
- [ ] MySQL or PostgreSQL (for production)
- [ ] Docker & Docker Compose
- [ ] Visual Studio Code or similar editor

---

## Step 1: Verify Installation

### On Linux/macOS:
```bash
bash verify.sh
```

### On Windows:
```bash
verify.bat
```

This will check all requirements and show what's missing.

---

## Step 2: Install yt-dlp

### Linux (Debian/Ubuntu):
```bash
sudo apt-get update
sudo apt-get install yt-dlp -y
```

### macOS:
```bash
brew install yt-dlp
```

### Windows:
```bash
pip install yt-dlp
```

### Verify Installation:
```bash
yt-dlp --version
```

### Find yt-dlp Path:
This will be needed for configuration.

**Linux/macOS:**
```bash
which yt-dlp
# Output: /usr/local/bin/yt-dlp
```

**Windows (PowerShell):**
```powershell
where.exe yt-dlp
# Output: C:\Python39\Scripts\yt-dlp.exe
```

---

## Step 3: Install PHP Dependencies

```bash
composer install
```

This creates a `vendor/` directory and installs all Laravel packages.

**Expected output:**
```
Loading composer repositories...
Updating dependencies
...
composer install successfully completed
```

---

## Step 4: Configure Environment

### Copy environment template:
```bash
cp .env.example .env
```

### Open `.env` in your editor and configure:

```env
# Application
APP_NAME="Video Downloader"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ku                # ku for Kurdish, en for English

# Database (SQLite for quick start)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# yt-dlp path (from Step 2)
# Linux/macOS example:
YTDLP_PATH=/usr/local/bin/yt-dlp
# Windows example:
YTDLP_PATH=C:\Python39\Scripts\yt-dlp.exe

# Downloads
DOWNLOADS_PATH=storage/downloads
MAX_DOWNLOAD_SIZE=10737418240  # 10GB

# Queue (for async downloads)
QUEUE_CONNECTION=database

# Rate limiting
RATE_LIMIT=30
RATE_LIMIT_PERIOD=60
```

---

## Step 5: Generate Application Key

```bash
php artisan key:generate
```

This creates a unique encryption key and adds it to `.env`:
```
Application key set successfully.
```

---

## Step 6: Setup Database

Create all necessary tables:

```bash
php artisan migrate
```

Expected output:
```
Migrating: 2024_01_01_000000_create_downloads_table
Migrated:  2024_01_01_000000_create_downloads_table (50.00ms)
Migrating: 2024_01_01_000001_create_jobs_table
Migrated:  2024_01_01_000001_create_jobs_table (45.00ms)
Migrating: 2024_01_01_000002_create_failed_jobs_table
Migrated:  2024_01_01_000002_create_failed_jobs_table (40.00ms)
Migrating: 2024_01_01_000003_create_cache_table
Migrated:  2024_01_01_000003_create_cache_table (35.00ms)
```

### Verify Database Created:
```bash
# Check if database.sqlite exists
ls database/database.sqlite  # Linux/macOS
dir database\database.sqlite # Windows
```

---

## Step 7: Set Permissions (Linux/macOS only)

```bash
# Make storage writable
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Make artisan executable
chmod +x artisan
chmod +x verify.sh install.sh
```

---

## Step 8: Start the Application

### Terminal 1 - Web Server:
```bash
php artisan serve
```

You should see:
```
Starting Laravel development server: http://127.0.0.1:8000
```

### Terminal 2 - Queue Worker (open in new terminal):
```bash
php artisan queue:work
```

You should see:
```
Processing jobs from the 'default' queue.
```

---

## Step 9: Test the Application

### Open in Browser:
```
http://localhost:8000
```

You should see the Video Downloader interface in Kurdish.

### Test With Sample URL:
1. Paste: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
2. Click "شیکاری کردن" (Analyze)
3. Select quality
4. Click "داونلۆد کردن" (Download)

---

## Troubleshooting

### Issue: "yt-dlp command not found"

**Solution:**
```bash
# Verify yt-dlp is installed
yt-dlp --version

# Update .env with full path
which yt-dlp  # Find path
# Update YTDLP_PATH in .env
```

### Issue: "port 8000 is already in use"

**Solution:**
```bash
# Use different port
php artisan serve --port=8001
```

### Issue: "database.sqlite not found"

**Solution:**
```bash
# Recreate database
touch database/database.sqlite
php artisan migrate
```

### Issue: "Permission denied" on storage

**Solution (Linux/macOS):**
```bash
chmod -R 755 storage bootstrap/cache
```

### Issue: Queue not processing downloads

**Solution:**
```bash
# Ensure queue worker is running
# In Terminal 2:
php artisan queue:work

# Or use longer timeout:
php artisan queue:work --timeout=3600
```

---

## Verification Checklist

After setup, verify everything works:

- [ ] Web server starts with `php artisan serve`
- [ ] Queue worker starts with `php artisan queue:work`
- [ ] Application accessible at http://localhost:8000
- [ ] UI displays in Kurdish
- [ ] Can analyze a YouTube video
- [ ] Can select quality options
- [ ] Can queue download
- [ ] Queue worker processes jobs
- [ ] Downloaded files appear in storage/downloads

---

## Configuration Reference

### .env Variables

| Variable | Purpose | Example |
|----------|---------|---------|
| APP_NAME | Application name | "Video Downloader" |
| APP_ENV | Environment | local / production |
| APP_DEBUG | Debug mode | true / false |
| APP_URL | Application URL | http://localhost:8000 |
| APP_LOCALE | Language | ku / en |
| DB_CONNECTION | Database type | sqlite / mysql |
| DB_DATABASE | Database path | database/database.sqlite |
| YTDLP_PATH | yt-dlp path | /usr/local/bin/yt-dlp |
| QUEUE_CONNECTION | Queue driver | database / redis / sync |
| RATE_LIMIT | Max requests | 30 |
| RATE_LIMIT_PERIOD | Time period | 60 (seconds) |

---

## Useful Commands

```bash
# Development
php artisan serve                    # Start web server
php artisan queue:work               # Start queue worker
php artisan tinker                   # Debug console

# Database
php artisan migrate                  # Run migrations
php artisan migrate:refresh          # Reset database
php artisan migrate:status           # Check migrations

# Cache & Configuration
php artisan cache:clear              # Clear cache
php artisan config:clear             # Clear config cache
php artisan config:cache             # Cache config

# Queue Management
php artisan queue:work               # Process jobs
php artisan queue:failed             # View failed jobs
php artisan queue:retry all          # Retry failed jobs
php artisan queue:flush              # Clear queue

# About Application
php artisan about                    # Show environment info
```

---

## Next Steps

1. **Customize** - Modify UI and translations
2. **Deploy** - Set up production environment
3. **Extend** - Add new features
4. **Integrate** - Connect with other systems via API
5. **Scale** - Add multiple queue workers

---

## Support Resources

- README.md - Full documentation
- QUICK_START.md - Quick reference
- API.md - API documentation
- TROUBLESHOOTING.md - Problem solutions
- ARCHITECTURE.md - System design

---

**Your application is now ready to use! 🎉**
