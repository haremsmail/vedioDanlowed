<?php

return [
    /*
     * Path to yt-dlp binary.
     * On Windows with Laragon: use full path like C:/laragon/bin/yt-dlp/yt-dlp.exe
     * Or just 'yt-dlp' if it's in your PATH.
     */
    'yt_dlp_path' => env('YTDLP_PATH', 'yt-dlp'),

    /*
     * Where to store downloaded files.
     * storage_path('downloads') = storage/downloads/
     */
    'downloads_path' => storage_path(env('DOWNLOADS_PATH', 'downloads')),

    /*
     * Max file size in bytes (default 10 GB).
     */
    'max_download_size' => env('MAX_DOWNLOAD_SIZE', null),

    /*
     * Process timeout in seconds (1 hour).
     */
    'timeout' => 3600,

    /*
     * Supported video platforms.
     * Includes social media, video platforms, and film/movie streaming services
     */
    'supported_platforms' => [
        // Social Media & Video Platforms
        'youtube.com',
        'youtu.be',
        'tiktok.com',
        'vm.tiktok.com',
        'vt.tiktok.com',
        'instagram.com',
        'facebook.com',
        'fb.watch',
        'snapchat.com',
        'snap.com',
        'twitter.com',
        'x.com',
        'twitch.tv',
        'vimeo.com',
        'dailymotion.com',
        'bbc.co.uk',
        'bbc.com',
        'reddit.com',
        'iq.com', // iQIYI
        'bilibili.com',
        'weibo.com',
        'douyin.com', // Chinese TikTok
    ],

    /*
     * API Tokens for external API consumers
     */
    /*
     * Path to ffmpeg binary directory.
     * Leave empty to auto-detect or use PATH.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', ''),

    'api_token' => env('API_TOKEN'),
    'api_token_secondary' => env('API_TOKEN_SECONDARY'),
];
