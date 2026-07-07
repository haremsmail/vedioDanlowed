# 🔧 Troubleshooting Guide

## Installation Issues

### 1. Composer Not Found
**Error**: `composer: command not found`

**Solution**:
```bash
# Install Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Or download from https://getcomposer.org
```

### 2. PHP Version Error
**Error**: `PHP 8.1 or higher required`

**Solution**:
```bash
# Check PHP version
php -v

# Update PHP
# Ubuntu/Debian
sudo apt-get install php8.1-cli

# macOS
brew install php@8.1
```

### 3. yt-dlp Not Found
**Error**: `yt-dlp: command not found`

**Solution**:
- Check installation: `yt-dlp --version`
- Install if missing:
  ```bash
  pip install yt-dlp
  ```
- Update .env with correct path:
  ```env
  # Find path first
  which yt-dlp  # Linux/macOS
  where yt-dlp  # Windows
  
  YTDLP_PATH=/usr/local/bin/yt-dlp
  ```

## Runtime Issues

### 1. Queue Not Processing
**Error**: Downloads stay in "pending" status

**Solution**:
```bash
# Ensure queue worker is running
php artisan queue:work

# Use these options for debugging
php artisan queue:work --verbose

# Increase timeout if needed
php artisan queue:work --timeout=3600 --tries=3
```

### 2. Large Downloads Failing
**Error**: Download stops or fails for large files

**Solution**:
```env
# Increase max download size in .env
MAX_DOWNLOAD_SIZE=52428800000  # 50GB

# Also increase PHP limits in php.ini
max_execution_time = 7200
upload_max_filesize = 50G
post_max_size = 50G
```

### 3. Permission Denied Error
**Error**: `Permission denied` on storage or yt-dlp

**Solution**:
```bash
# Fix storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Fix yt-dlp permissions
chmod +x /usr/local/bin/yt-dlp

# On Windows, ensure yt-dlp.exe is in PATH
```

### 4. Database Locked Error
**Error**: `database is locked`

**Solution**:
```bash
# Restart application
php artisan serve

# Or clear cache
php artisan cache:clear

# Reset database if needed
php artisan migrate:refresh
```

## Video Analysis Issues

### 1. Video Fetch Failed
**Error**: "Failed to fetch video information"

**Causes & Solutions**:
- URL is invalid → Copy exact URL from browser
- Platform not supported → Check if platform is in `config/ytdlp.php`
- yt-dlp outdated → Update: `pip install -U yt-dlp`
- Network issue → Check internet connection
- Video is private → Use public videos for testing

### 2. No Formats Available
**Error**: Video shows no qualities to select

**Solution**:
```bash
# Test yt-dlp directly
yt-dlp -F "https://youtube.com/watch?v=..."

# Update yt-dlp
pip install -U yt-dlp

# Check YtDlpService.php extractFormats() method
```

### 3. Thumbnail Not Loading
**Error**: Video thumbnail shows broken image

**Solution**:
- This is not critical, video will still download
- Update yt-dlp: `pip install -U yt-dlp`
- Check network connection

## Performance Issues

### 1. Slow Download Analysis
**Problem**: Taking too long to analyze video

**Solution**:
```env
# Increase timeout in config/ytdlp.php
'timeout' => 7200
```

### 2. High Memory Usage
**Problem**: Application consuming too much memory

**Solution**:
- Limit concurrent downloads: Configure queue workers
- Clear old downloads: `storage/downloads/`
- Check PHP memory limit: `php -r "echo ini_get('memory_limit');"`
- Increase if needed in php.ini: `memory_limit = 512M`

### 3. Slow File Downloads
**Problem**: Downloaded files download slowly from browser

**Solution**:
- This depends on network speed
- Check server disk speed
- Use SSD for `storage/downloads/`

## API Issues

### 1. CSRF Token Mismatch
**Error**: `419 Page Expired`

**Solution**:
- Make sure CSRF token is sent in headers:
  ```javascript
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
  ```

### 2. Rate Limit Exceeded
**Error**: `429 Too Many Requests`

**Solution**:
```env
# Increase rate limit in .env
RATE_LIMIT=100
RATE_LIMIT_PERIOD=60
```

### 3. Invalid JSON Response
**Error**: Cannot parse API response

**Solution**:
- Check API response: `curl -X POST http://localhost:8000/api/analyze -H "Content-Type: application/json" -d '{"url":"..."}'`
- Ensure URL is valid
- Check server logs: `storage/logs/laravel.log`

## Language Issues

### 1. Kurdish Not Displaying
**Problem**: Text shows as squares or incorrect characters

**Solution**:
```env
# Set proper locale
APP_LOCALE=ku
APP_FALLBACK_LOCALE=en
```

### 2. Change Language
**Solution**:
```env
# Use English
APP_LOCALE=en

# Use Kurdish
APP_LOCALE=ku
```

## Network Issues

### 1. Cannot Connect to Application
**Error**: `Connection refused` on localhost:8000

**Solution**:
```bash
# Ensure artisan serve is running
ps aux | grep artisan

# Try different port
php artisan serve --port=8001

# Check if port is in use
netstat -an | grep 8000
```

### 2. External Access Issues
**Problem**: Cannot access from other machines

**Solution**:
```bash
# Allow external access
php artisan serve --host=0.0.0.0 --port=8000

# Update APP_URL in .env
APP_URL=http://your-ip:8000
```

## Logging & Debugging

### 1. View Logs
```bash
# Real-time log viewing
tail -f storage/logs/laravel.log

# Windows
powershell -Command "Get-Content storage/logs/laravel.log -Wait"
```

### 2. Enable Debug Mode
```env
APP_DEBUG=true
APP_ENV=local
```

### 3. Use Tinker for Debugging
```bash
php artisan tinker

# Inside tinker
>>> \App\Models\Download::all()
>>> \App\Models\Download::where('status', 'failed')->get()
```

## Getting Help

1. Check logs: `storage/logs/laravel.log`
2. Run diagnostic: `php artisan about`
3. Test API manually with curl
4. Check .env configuration
5. Verify yt-dlp installation and path

---

**Still having issues?** Review the README.md and QUICK_START.md files for more information.
