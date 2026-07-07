# 🚀 Complete Installation Guide

## System Requirements

Before starting, ensure you have:

### Required Software
- **PHP 8.1 or higher**
  ```bash
  php -v  # Check version
  ```
- **Composer** (for dependency management)
  ```bash
  composer -v  # Check version
  ```
- **yt-dlp** (for video downloading)
  ```bash
  yt-dlp --version  # Check version
  ```

### Optional but Recommended
- **Git** (for version control)
- **Docker** (for containerization)
- **MySQL/PostgreSQL** (for production databases)

---

## Step-by-Step Installation

### Step 1: Navigate to Project Directory

```bash
cd danlwoedvideio
```

### Step 2: Install yt-dlp

Choose the appropriate command for your operating system:

**On Ubuntu/Debian Linux:**
```bash
sudo apt-get update
sudo apt-get install yt-dlp
```

**On macOS:**
```bash
brew install yt-dlp
```

**On Windows:**
```bash
# Using pip
pip install yt-dlp

# Or download from GitHub releases:
# https://github.com/yt-dlp/yt-dlp/releases
```

**Verify Installation:**
```bash
yt-dlp --version
```

### Step 3: Find yt-dlp Path

You'll need the full path to yt-dlp:

**Linux/macOS:**
```bash
which yt-dlp
# Output: /usr/local/bin/yt-dlp
```

**Windows (in PowerShell):**
```powershell
where.exe yt-dlp
# Output: C:\Python39\Scripts\yt-dlp.exe
```

**Note:** Copy the path for Step 5.

### Step 4: Install PHP Dependencies

```bash
composer install
```

This installs all required Laravel packages (may take 2-3 minutes).

### Step 5: Configure Environment

**Copy the example environment file:**
```bash
cp .env.example .env
```

**Edit `.env` file and update these values:**

#### Windows Users:
```env
# Open in Notepad or your editor
# Find and update these lines:

APP_NAME="Video Downloader"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ku

# Database (use SQLite for quick start)
DB_CONNECTION=sqlite

# yt-dlp - USE THE PATH FROM STEP 3
YTDLP_PATH=C:\Python39\Scripts\yt-dlp.exe

# Queue for async downloads
QUEUE_CONNECTION=database

# Rate limiting
RATE_LIMIT=30
RATE_LIMIT_PERIOD=60
```

#### Linux/macOS Users:
```env
APP_NAME="Video Downloader"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ku

DB_CONNECTION=sqlite

# yt-dlp - USE THE PATH FROM STEP 3
YTDLP_PATH=/usr/local/bin/yt-dlp

QUEUE_CONNECTION=database

RATE_LIMIT=30
RATE_LIMIT_PERIOD=60
```

### Step 6: Generate Application Key

```bash
php artisan key:generate
```

This generates a secure encryption key for your application.

### Step 7: Setup Database

Create tables for downloads, jobs, and cache:

```bash
php artisan migrate
```

You should see output indicating tables were created:
```
Creating table downloads
Creating table jobs
Creating table failed_jobs
Creating table cache
```

### Step 8: Verify Installation

Test if everything is working:

```bash
# Check yt-dlp is accessible
php artisan tinker
>>> shell_exec('yt-dlp --version')
# Should show version number

# Exit tinker
>>> exit
```

---

## Starting the Application

### Option A: Using PHP Development Server (Recommended for Testing)

**Terminal 1 - Start Web Server:**
```bash
php artisan serve
```

You should see:
```
Laravel development server started: http://127.0.0.1:8000
```

**Terminal 2 - Start Queue Worker (in new terminal):**
```bash
php artisan queue:work
```

You should see:
```
[2024-01-01 12:00:00] Processing jobs from the 'default' queue.
```

### Option B: Using Installation Scripts

#### On Linux/macOS:
```bash
chmod +x install.sh
./install.sh
```

#### On Windows:
```bash
install.bat
```

### Option C: Using Docker

```bash
docker-compose up -d
docker-compose logs -f
```

---

## Access the Application

### Web Interface
```
http://localhost:8000
```

### API Endpoints
```
POST    /api/analyze       - Analyze video
POST    /api/download      - Download video
GET     /api/download/{id} - Check status
GET     /download/{file}   - Get file
```

---

## Testing the Application

### 1. Test Web Interface

1. Open browser: `http://localhost:8000`
2. Paste a YouTube URL: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
3. Click "شیکاری کردن" (Analyze)
4. Select quality
5. Click "داونلۆد کردن" (Download)

### 2. Test API with curl

**Analyze a video:**
```bash
curl -X POST http://localhost:8000/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"url":"https://www.youtube.com/watch?v=dQw4w9WgXcQ"}'
```

**Download a video:**
```bash
curl -X POST http://localhost:8000/api/download \
  -H "Content-Type: application/json" \
  -d '{
    "url":"https://www.youtube.com/watch?v=dQw4w9WgXcQ",
    "format_id":"18",
    "quality":"360p"
  }'
```

---

## Stopping the Application

**To stop the web server:**
```bash
Ctrl + C (in Terminal 1)
```

**To stop the queue worker:**
```bash
Ctrl + C (in Terminal 2)
```

---

## Changing Language

To change from Kurdish to English:

**Edit `.env`:**
```env
APP_LOCALE=en  # Change from 'ku' to 'en'
```

Refresh the browser page to see English interface.

---

## Troubleshooting Common Issues

### Issue: "yt-dlp: command not found"

**Solution:**
1. Verify yt-dlp is installed: `yt-dlp --version`
2. Get the full path: `which yt-dlp` (Linux/macOS) or `where yt-dlp` (Windows)
3. Update YTDLP_PATH in `.env` with the full path

### Issue: "port 8000 is already in use"

**Solution:**
```bash
php artisan serve --port=8001  # Use different port
```

### Issue: "php artisan command not found"

**Solution:**
```bash
# Make sure you're in the project directory
cd danlwoedvideio

# Try with full PHP path
/usr/bin/php artisan serve  # Linux/macOS
C:\PHP\php.exe artisan serve  # Windows
```

### Issue: "Composer command not found"

**Solution:**
1. Install Composer from: https://getcomposer.org/download/
2. Or use: `php composer.phar install`

### Issue: "Database error"

**Solution:**
```bash
# Reset database
php artisan migrate:refresh

# Clear cache
php artisan cache:clear
```

---

## Configuration Files Reference

### Main Configuration Files

| File | Purpose |
|------|---------|
| `.env` | Environment variables |
| `config/ytdlp.php` | yt-dlp settings |
| `config/ratelimit.php` | Rate limiting |
| `config/queue.php` | Queue configuration |
| `config/filesystems.php` | File storage |

### Key Environment Variables

```env
APP_NAME              # Application name
APP_ENV               # Environment (local/production)
APP_DEBUG             # Debug mode (true/false)
APP_URL               # Application URL
APP_LOCALE            # Language (ku/en)
DB_CONNECTION         # Database (sqlite/mysql)
YTDLP_PATH           # Path to yt-dlp executable
QUEUE_CONNECTION      # Queue driver (database/redis/sync)
RATE_LIMIT           # Max requests per period
RATE_LIMIT_PERIOD    # Time period in seconds
```

---

## Production Deployment

### Before Going Live

1. **Set APP_ENV to production:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Use MySQL instead of SQLite:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=video_downloader
   DB_USERNAME=root
   DB_PASSWORD=password
   ```

3. **Set proper permissions:**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

4. **Use a process manager:**
   ```bash
   # Install Supervisor (Ubuntu)
   sudo apt-get install supervisor
   ```

---

## Next Steps

1. ✅ Complete the installation above
2. ✅ Test the web interface
3. ✅ Test the API
4. ✅ Check the documentation files for more info
5. ✅ Customize as needed for your use case

---

## Additional Resources

- **README.md** - Full documentation
- **QUICK_START.md** - Quick reference
- **TROUBLESHOOTING.md** - Problem solutions
- **API.md** - API reference
- **PROJECT_SUMMARY.md** - Project overview

---

## Getting Help

If you encounter issues:

1. Check **TROUBLESHOOTING.md**
2. Review error messages in `storage/logs/laravel.log`
3. Run: `php artisan about`
4. Test yt-dlp directly: `yt-dlp --version`

---

**Your installation is complete! Enjoy your video downloader! 🎉**
