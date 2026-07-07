# 🏗️ Architecture & Components

## Project Architecture

```
Video Downloader Application
│
├── Frontend Layer
│   └── Blade Templates (index.blade.php)
│       └── Vanilla JavaScript + CSS3
│
├── API Layer (REST)
│   ├── POST /api/analyze
│   ├── POST /api/download
│   ├── GET /api/download/{id}
│   └── GET /download/{file}
│
├── Application Layer
│   ├── Controllers
│   │   └── VideoController
│   ├── Services
│   │   └── YtDlpService
│   └── Models
│       └── Download
│
├── Queue Layer
│   ├── ProcessVideoDownload Job
│   └── Queue Worker
│
└── Data Layer
    ├── SQLite Database
    ├── File Storage
    └── Cache Layer
```

---

## Core Components

### 1. VideoController (`app/Http/Controllers/VideoController.php`)

Main API controller with endpoints:

```php
// Analyze video metadata
public function analyze(Request $request): JsonResponse

// Queue video download
public function download(Request $request): JsonResponse

// Get download status
public function status(Request $request, int $id): JsonResponse

// Download completed file
public function downloadFile(Request $request, string $file)

// Index page
public function index()
```

**Responsibilities:**
- Handle HTTP requests
- Validate user input
- Call services
- Return JSON responses
- Handle errors

---

### 2. YtDlpService (`app/Services/YtDlpService.php`)

Service for yt-dlp integration:

```php
// Check if URL is from supported platform
public function isSupportedPlatform(string $url): bool

// Get video metadata
public function analyzeVideo(string $url): array

// Extract available formats
private function extractFormats(array $videoData): array

// Download video with format
public function downloadVideo(string $url, string $formatId): array

// Find downloaded file
private function findDownloadedFile(string $prefix): ?string

// Get downloads directory
public function getDownloadsPath(): string
```

**Responsibilities:**
- Execute yt-dlp commands safely
- Parse video metadata
- Extract quality formats
- Handle command execution
- Manage file storage

---

### 3. Download Model (`app/Models/Download.php`)

Database model for tracking downloads:

```php
// Properties
$fillable = [
    'url',
    'title',
    'format_id',
    'quality',
    'status',
    'file_path',
    'error_message',
    'ip_address',
];

// Status Constants
const STATUS_PENDING = 'pending';
const STATUS_DOWNLOADING = 'downloading';
const STATUS_COMPLETED = 'completed';
const STATUS_FAILED = 'failed';
```

**Responsibilities:**
- Store download records
- Track download status
- Log errors
- Maintain history

---

### 4. ProcessVideoDownload Job (`app/Jobs/ProcessVideoDownload.php`)

Queue job for async downloads:

```php
// Process download from queue
public function handle(YtDlpService $ytDlpService): void

// Handle job failure
public function failed(Exception $exception): void
```

**Responsibilities:**
- Execute downloads asynchronously
- Update download status
- Handle failures
- Log operations

**Retry Configuration:**
- Max attempts: 3
- Timeout: 3600 seconds (1 hour)

---

### 5. RateLimitMiddleware (`app/Http/Middleware/RateLimitMiddleware.php`)

Middleware for rate limiting:

```php
// Check and enforce rate limits
public function handle(Request $request, Closure $next): Response
```

**Configuration:**
- 30 requests per 60 seconds per IP
- Applied to API endpoints only

**Response Headers:**
- X-RateLimit-Limit
- X-RateLimit-Remaining

---

### 6. Other Middleware

**EncryptCookies** - Encrypt cookie data

**VerifyCsrfToken** - CSRF token validation

**Authenticate** - User authentication

---

## Data Flow Diagram

```
User Browser
    ↓
Web Server (port 8000)
    ↓
Request Router
    ├─ GET /        → index() view
    ├─ POST /api/analyze
    │   ├→ RateLimitMiddleware
    │   ├→ VideoController::analyze()
    │   ├→ YtDlpService::analyzeVideo()
    │   ├→ YtDlpService::extractFormats()
    │   └→ JSON Response
    │
    ├─ POST /api/download
    │   ├→ RateLimitMiddleware
    │   ├→ VideoController::download()
    │   ├→ Download::create()
    │   ├→ ProcessVideoDownload Job
    │   └→ JSON Response (download_id)
    │
    ├─ GET /api/download/{id}
    │   ├→ VideoController::status()
    │   ├→ Download::find()
    │   └→ JSON Response (status)
    │
    └─ GET /download/{file}
        ├→ VideoController::downloadFile()
        ├→ File Download
        └→ Delete File

Queue Worker (separate process)
    ↓
ProcessVideoDownload::handle()
    ├→ Update status: downloading
    ├→ YtDlpService::downloadVideo()
    ├→ Execute yt-dlp command
    ├→ Update status: completed
    └→ Log result
```

---

## Configuration Flow

```
.env
    ↓
Application Bootstrap
    ├─ Load environment variables
    ├─ Create service container
    └─ Register providers
        ↓
    Load Config Files
    ├─ config/ytdlp.php
    ├─ config/ratelimit.php
    ├─ config/queue.php
    └─ config/cache.php
        ↓
    Middleware Stack
    ├─ EncryptCookies
    ├─ VerifyCsrfToken
    ├─ StartSession
    ├─ RateLimitMiddleware
    └─ SubstituteBindings
        ↓
    Route Handler
```

---

## Database Schema

### downloads table
```sql
CREATE TABLE downloads (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  url VARCHAR(2048) NOT NULL,
  title VARCHAR(255),
  format_id VARCHAR(100) NOT NULL,
  quality VARCHAR(50) NOT NULL,
  status VARCHAR(50) DEFAULT 'pending',
  file_path VARCHAR(255),
  error_message TEXT,
  ip_address VARCHAR(50),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  INDEX idx_status (status),
  INDEX idx_created_at (created_at),
  INDEX idx_ip_address (ip_address)
);
```

### jobs table
```sql
CREATE TABLE jobs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  queue VARCHAR(255) NOT NULL,
  payload LONGTEXT NOT NULL,
  attempts TINYINT UNSIGNED DEFAULT 0,
  reserved_at INT UNSIGNED,
  available_at INT UNSIGNED DEFAULT 0,
  created_at INT UNSIGNED,
  
  INDEX idx_queue (queue)
);
```

### cache table
```sql
CREATE TABLE cache (
  key VARCHAR(255) PRIMARY KEY,
  value MEDIUMTEXT NOT NULL,
  expiration INT NOT NULL,
  
  INDEX idx_expiration (expiration)
);
```

---

## Request/Response Examples

### API Request: Analyze Video

**HTTP Request:**
```http
POST /api/analyze HTTP/1.1
Host: localhost:8000
Content-Type: application/json
X-CSRF-TOKEN: {token}

{
  "url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
}
```

**Processing Steps:**
1. Middleware: Check rate limit
2. Controller: Validate input
3. Service: Execute yt-dlp --dump-json
4. Service: Parse JSON output
5. Service: Extract formats
6. Controller: Return response

**Response:**
```json
{
  "success": true,
  "data": {
    "title": "Video Title",
    "thumbnail": "https://...",
    "duration": 213,
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

---

### Queue Processing Flow

**Download Request:**
1. Controller creates Download record (status: pending)
2. ProcessVideoDownload job dispatched to queue
3. Response sent immediately to user

**Queue Worker Processing:**
1. Pick job from queue
2. Load Download record
3. Update status: downloading
4. Execute yt-dlpService::downloadVideo()
5. Wait for process completion
6. Update Download (status: completed)
7. Log success
8. Remove from queue

**Error Handling:**
- If fails: Retry up to 3 times
- After 3 attempts: status = failed
- Log error message
- Move to failed_jobs table

---

## Security Implementation

### Input Validation
```php
// VideoController::analyze()
$validated = $request->validate([
    'url' => 'required|url|string|max:2048',
]);

// VideoController::download()
$validated = $request->validate([
    'url' => 'required|url|string|max:2048',
    'format_id' => 'required|string|max:50',
    'quality' => 'required|string|max:50',
]);

// Format ID validation
if (!preg_match('/^[a-zA-Z0-9_+-]+$/', $formatId)) {
    // Reject
}
```

### Command Execution
```php
// Using array-based Process (prevents shell injection)
$command = [
    $this->ytDlpPath,
    '-f', $formatId,        // Format specification
    '-o', $outputPath,      // Output path
    $url,                   // URL argument
];

$process = new Process($command);
$process->setTimeout($this->timeout);
$process->run();
```

### File Path Protection
```php
// Prevent directory traversal
if (preg_match('/\.\./', $file) || preg_match('/\//', $file)) {
    return error response;
}

$filePath = $this->getDownloadsPath() . '/' . $file;
if (!file_exists($filePath)) {
    return 404 error;
}
```

---

## Performance Considerations

### Optimization Strategies

1. **Async Processing**
   - Downloads don't block requests
   - Queue worker handles in background
   - User gets immediate response

2. **Caching**
   - Rate limit cache stored in database
   - Quick lookup for repeat requests

3. **Database Indexing**
   - status index for filtering
   - created_at for sorting
   - ip_address for tracking

4. **File Management**
   - Direct file streaming for downloads
   - Auto-delete after delivery
   - Efficient storage structure

---

## Deployment Architecture

```
Production Environment
│
├── Load Balancer (Optional)
│   └── Directs to Web Servers
│
├── Web Server(s)
│   ├── PHP-FPM processes
│   ├── Handles HTTP requests
│   └── Runs controllers
│
├── Queue Worker(s)
│   ├── Separate processes
│   ├── Process download jobs
│   └── Multiple workers for scale
│
├── Database
│   ├── MySQL / PostgreSQL
│   ├── Persistent storage
│   └── Replication for HA
│
├── File Storage
│   ├── Downloads directory
│   ├── Shared/NFS for multi-server
│   └── Regular cleanup
│
└── Cache Layer (Optional)
    ├── Redis / Memcached
    ├── Rate limiting
    └── Session storage
```

---

## Extension Points

### Adding New Features

1. **New Video Platform**
   - Add platform URL to config/ytdlp.php
   - yt-dlp will automatically support it

2. **Different Queue Driver**
   - Update QUEUE_CONNECTION in .env
   - Supported: database, redis, beanstalkd, sync

3. **Custom Format Extraction**
   - Modify YtDlpService::extractFormats()

4. **Download Webhooks**
   - Add webhook dispatch in ProcessVideoDownload

5. **Video Conversion**
   - Add new Job: ProcessVideoConversion
   - Call after download completion

---

**This architecture provides:**
- ✅ Scalability
- ✅ Security
- ✅ Performance
- ✅ Maintainability
- ✅ Extensibility

