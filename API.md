# 📡 API Documentation

## Base URL
```
http://localhost:8000/api
```

## Response Format
All responses are in JSON format.

### Success Response
```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message"
}
```

## Endpoints

### 1. POST /analyze
Analyze a video and get metadata.

**Request:**
```json
{
  "url": "https://youtube.com/watch?v=dQw4w9WgXcQ"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "title": "Rick Astley - Never Gonna Give You Up",
    "thumbnail": "https://...",
    "duration": 213,
    "formats": [
      {
        "id": "18",
        "format": "video",
        "quality": "360p",
        "height": 360,
        "ext": "mp4"
      },
      {
        "id": "22",
        "format": "video",
        "quality": "720p",
        "height": 720,
        "ext": "mp4"
      },
      {
        "id": "251",
        "format": "audio",
        "quality": "Audio Only",
        "ext": "m4a"
      }
    ],
    "url": "https://youtube.com/watch?v=dQw4w9WgXcQ"
  }
}
```

**Errors:**
- `400 Bad Request` - Invalid URL
- `400 Bad Request` - Unsupported platform
- `400 Bad Request` - Failed to fetch video

---

### 2. POST /download
Request a video download. The file is downloaded synchronously on the server and served directly.

**Request:**
```json
{
  "url": "https://youtube.com/watch?v=dQw4w9WgXcQ",
  "format_id": "18",
  "quality": "360p"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Your video is ready! Download will start...",
  "download_url": "http://localhost:8000/download/v_abc123.mp4",
  "filename": "v_abc123.mp4"
}
```

**Response (429 Too Many Requests):**
```json
{
  "success": false,
  "message": "Please wait a few minutes before downloading again"
}
```

**Errors:**
- `400 Bad Request` - Invalid parameters
- `400 Bad Request` - Unsupported platform
- `429 Too Many Requests` - Rate limit exceeded

**Rate Limit Headers:**
```
X-RateLimit-Limit: 30
X-RateLimit-Remaining: 29
```

---

### 3. GET /download/{file}
Download the completed file.

**Request:**
```
GET /download/v_abc123.mp4
```

**Response (200 OK):**
- File binary content
- Content-Type: application/octet-stream
- File is automatically deleted from server after download

**Response (404 Not Found):**
```json
{
  "success": false,
  "message": "File not found"
}
```

---

## Examples

### cURL

**Analyze Video:**
```bash
curl -X POST http://localhost:8000/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"url":"https://youtube.com/watch?v=dQw4w9WgXcQ"}'
```

**Download Video:**
```bash
curl -X POST http://localhost:8000/api/download \
  -H "Content-Type: application/json" \
  -d '{
    "url":"https://youtube.com/watch?v=dQw4w9WgXcQ",
    "format_id":"18",
    "quality":"360p"
  }'
```

**Download File:**
```bash
curl -O http://localhost:8000/download/v_abc123.mp4
```

### JavaScript

**Analyze Video:**
```javascript
const response = await fetch('/api/analyze', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    url: 'https://youtube.com/watch?v=dQw4w9WgXcQ'
  })
});

const data = await response.json();
console.log(data);
```

**Download Video:**
```javascript
const response = await fetch('/api/download', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    url: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
    format_id: '18',
    quality: '360p'
  })
});

const data = await response.json();
if (data.success) {
  console.log('Download URL:', data.download_url);
  // Trigger file download
  window.location.href = data.download_url;
}
```

### Python

**Analyze Video:**
```python
import requests

response = requests.post(
    'http://localhost:8000/api/analyze',
    json={'url': 'https://youtube.com/watch?v=dQw4w9WgXcQ'}
)
print(response.json())
```

**Download Video:**
```python
import requests

response = requests.post(
    'http://localhost:8000/api/download',
    json={
        'url': 'https://youtube.com/watch?v=dQw4w9WgXcQ',
        'format_id': '18',
        'quality': '360p'
    }
)
result = response.json()
if result.get('success'):
    print(f"Download URL: {result['download_url']}")
```

---

## Platform Support

Supported platforms:
- YouTube (youtube.com, youtu.be)
- TikTok (tiktok.com, vm.tiktok.com, vt.tiktok.com)
- Instagram (instagram.com)
- Facebook (facebook.com, fb.watch)

## Rate Limiting

- **Limit**: 30 requests per minute per IP
- **Applies to**: /api/analyze, /api/download
- **Headers**: X-RateLimit-Limit, X-RateLimit-Remaining

## Security

- All URLs are validated before processing
- Command injection is prevented with proper shell escaping
- File paths are validated against directory traversal
- CSRF tokens are required for POST requests
- Input is sanitized and validated

---

**Disclaimer**: This API is provided for educational and administrative utility. Respect copyright laws and platform terms of service.
