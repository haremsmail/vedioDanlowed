<?php

return [
    /*
     * Path to yt-dlp binary ('yt-dlp' if it's in PATH).
     */
    'yt_dlp_path' => env('YTDLP_PATH', 'yt-dlp'),

    /*
     * PHP CLI binary used to run background download workers.
     * Leave empty to auto-detect.
     */
    'php_binary' => env('PHP_CLI_BINARY', ''),

    /*
     * How download jobs are started:
     *  daemon = `php artisan video:worker` runs in the background (Docker / production)
     *  spawn  = start a process per download directly (handy for local Windows/Laragon)
     */
    'worker_mode' => env('DOWNLOAD_WORKER', PHP_OS_FAMILY === 'Windows' ? 'spawn' : 'daemon'),

    /*
     * Where downloaded files and job state are stored.
     */
    'downloads_path' => storage_path(env('DOWNLOADS_PATH', 'downloads')),
    'jobs_path'      => storage_path('app/jobs'),

    /*
     * Optional cookies.txt (Netscape format). Needed on cloud servers for
     * YouTube ("Sign in to confirm you're not a bot") and some Instagram videos.
     */
    'cookies_file' => env('YTDLP_COOKIES_FILE', storage_path('app/cookies.txt')),

    /*
     * Optional proxy for yt-dlp (e.g. a residential proxy if your host's IP is blocked).
     */
    'proxy' => env('YTDLP_PROXY', ''),

    /*
     * Max size of one downloaded file (yt-dlp syntax: 500M, 2G ...).
     */
    'max_download_size' => env('MAX_DOWNLOAD_SIZE', '2G'),

    /*
     * Max duration of a single download in seconds.
     */
    'timeout' => (int) env('DOWNLOAD_TIMEOUT', 1800),

    /*
     * How many downloads may run on the server at the same time (protects RAM/CPU),
     * and how many per visitor IP.
     */
    'max_concurrent'        => (int) env('MAX_CONCURRENT_DOWNLOADS', 3),
    'max_concurrent_per_ip' => (int) env('MAX_CONCURRENT_PER_IP', 1),

    /*
     * Delete downloaded files after this many minutes.
     */
    'file_ttl_minutes' => (int) env('FILE_TTL_MINUTES', 60),

    /*
     * Cache analyze results (seconds) — repeated links load instantly.
     */
    'analyze_cache_seconds' => (int) env('ANALYZE_CACHE_SECONDS', 900),

    /*
     * Supported video platforms.
     */
    'supported_platforms' => [
        'youtube.com',
        'youtu.be',
        'tiktok.com',
        'instagram.com',
        'facebook.com',
        'fb.watch',
        'snapchat.com',
        'twitter.com',
        'x.com',
        'twitch.tv',
        'vimeo.com',
        'dailymotion.com',
        'bbc.co.uk',
        'bbc.com',
        'reddit.com',
        'redd.it',
        'pinterest.com',
        'pin.it',
        'threads.net',
        'threads.com',
        'soundcloud.com',
        'iq.com',
        'bilibili.com',
        'weibo.com',
        'douyin.com',
    ],

    /*
     * Path to ffmpeg binary directory. Empty = auto-detect / PATH.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', ''),
];
