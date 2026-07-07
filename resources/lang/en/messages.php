<?php

return [
    // ═══════════════════════════════════════════════════════════════════
    // General
    // ═══════════════════════════════════════════════════════════════════
    'title'                      => 'Video Downloader',
    'subtitle'                   => 'Download videos from YouTube, TikTok, Instagram and thousands more',
    'description'                => 'Download videos from YouTube, TikTok, Instagram, Facebook and many other platforms',
    'loading'                    => 'Loading...',
    'close'                      => 'Close',
    'try_again'                  => 'Try Again',
    'success'                    => 'Success',
    'error'                      => 'Error',
    'warning'                    => 'Warning',
    'no_internet'                => 'No internet connection',
    'info'                       => 'Information',

    // ═══════════════════════════════════════════════════════════════════
    // Form Input
    // ═══════════════════════════════════════════════════════════════════
    'input_url'                  => 'Enter video or film URL',
    'input_placeholder'          => 'https://youtube.com/watch?v=... or any other URL',
    'paste_button'               => 'Paste',
    'paste_warning'              => 'Please type the link manually.',
    'analyze_button'             => 'Analyze',
    'download_button'            => 'Download',
    'cancel_button'              => 'Cancel',
    'back_button'                => 'Back',
    'next_button'                => 'Next',

    // ═══════════════════════════════════════════════════════════════════
    // Video Information
    // ═══════════════════════════════════════════════════════════════════
    'video_title'                => 'Video Title',
    'video_duration'             => 'Video Duration',
    'video_uploader'             => 'Uploader',
    'video_size'                 => 'File Size',
    'video_codec'                => 'Codec',
    'video_bitrate'              => 'Bitrate',
    'video_fps'                  => 'Frames Per Second (FPS)',
    'available_qualities'        => 'Available Qualities',
    'select_quality'             => 'Select Quality',
    'available_formats'          => 'Available Formats',
    'format_type'                => 'Format Type',
    'video_resolution'           => 'Resolution',

    // ═══════════════════════════════════════════════════════════════════
    // Quality Labels
    // ═══════════════════════════════════════════════════════════════════
    'quality_audio'              => 'Audio Only (MP3)',
    'quality_audio_aac'          => 'Audio (AAC)',
    'quality_audio_opus'         => 'Audio (Opus)',
    'quality_240p'               => '240p - Small',
    'quality_360p'               => '360p - Standard',
    'quality_480p'               => '480p - Better',
    'quality_720p'               => '720p - HD',
    'quality_1080p'              => '1080p - Full HD',
    'quality_1440p'              => '1440p - 2K',
    'quality_2160p'              => '2160p - 4K',
    'quality_4320p'              => '4320p - 8K',

    // ═══════════════════════════════════════════════════════════════════
    // File Formats
    // ═══════════════════════════════════════════════════════════════════
    'format_mp4'                 => 'MP4 (All Devices)',
    'format_webm'                => 'WebM (Web Quality)',
    'format_mkv'                 => 'MKV (Best Quality)',
    'format_mov'                 => 'MOV (Apple)',
    'format_avi'                 => 'AVI (Legacy)',
    'format_flv'                 => 'FLV (Flash)',
    'format_m4a'                 => 'M4A (Audio)',
    'format_mp3'                 => 'MP3 (Audio)',
    'format_wav'                 => 'WAV (High Quality Audio)',
    'format_aac'                 => 'AAC (Audio)',

    // ═══════════════════════════════════════════════════════════════════
    // Platforms
    // ═══════════════════════════════════════════════════════════════════
    'platform_youtube'           => 'YouTube',
    'platform_tiktok'            => 'TikTok',
    'platform_instagram'         => 'Instagram',
    'platform_facebook'          => 'Facebook',
    'platform_snapchat'          => 'Snapchat',
    'platform_twitter'           => 'Twitter / X',
    'platform_twitch'            => 'Twitch',
    'platform_vimeo'             => 'Vimeo',
    'platform_dailymotion'       => 'DailyMotion',
    'platform_reddit'            => 'Reddit',
    'platform_soundcloud'        => 'SoundCloud',
    'platform_spotify'           => 'Spotify',
    'supported_platforms'        => 'Supports thousands of platforms and video services',
    'more_platforms'             => 'and more...',

    // ═══════════════════════════════════════════════════════════════════
    // Status Messages
    // ═══════════════════════════════════════════════════════════════════
    'analyzing'                  => 'Fetching video information... Please wait',
    'analyzing_formats'          => 'Preparing available formats...',
    'downloading'                => 'Downloading video... Please wait',
    'download_ready'             => 'Your video is ready! Download will start...',
    'download_queued'            => 'Download added to queue',
    'download_complete'          => 'Download complete! ✓',
    'download_progress'          => 'Progress: :percent%',
    'download_speed'             => 'Speed: :speed',
    'estimated_time'             => 'Estimated time: :time',
    'initializing'               => 'Initializing...',
    'converting'                 => 'Converting format...',
    'merging'                    => 'Merging files...',

    // ═══════════════════════════════════════════════════════════════════
    // Error Messages
    // ═══════════════════════════════════════════════════════════════════
    'video_fetch_failed'         => 'Failed to fetch video information. Please check the URL or try again.',
    'video_blocked'              => 'This video is not accessible. It may be geo-blocked.',
    'video_private'              => 'This video is private.',
    'video_deleted'              => 'This video has been deleted.',
    'invalid_video_data'         => 'Invalid video data. Please correct the URL.',
    'download_failed'            => 'Download failed. Please try again.',
    'download_failed_permanently' => 'Download failed permanently. Please try again or try a different URL.',
    'unsupported_platform'       => 'Please enter a valid video URL.',
    'unsupported_format'         => 'Unsupported format.',
    'invalid_format'             => 'Invalid format selected',
    'download_not_found'         => 'Download not found',
    'invalid_file'               => 'Invalid file',
    'file_not_found'             => 'File not found',
    'file_corrupted'             => 'File is corrupted.',
    'storage_full'               => 'Server storage is full.',
    'rate_limit_exceeded'        => 'Too many requests. Please wait :seconds seconds before downloading again.',
    'network_error'              => 'Network error. Please check your internet connection.',
    'permission_denied'          => 'Permission denied.',

    // ═══════════════════════════════════════════════════════════════════
    // Time Units
    // ═══════════════════════════════════════════════════════════════════
    'seconds'                    => 'seconds',
    'second'                     => 'second',
    'minutes'                    => 'minutes',
    'minute'                     => 'minute',
    'hours'                      => 'hours',
    'hour'                       => 'hour',
    'days'                       => 'days',
    'day'                        => 'day',

    // ═══════════════════════════════════════════════════════════════════
    // Footer
    // ═══════════════════════════════════════════════════════════════════
    'footer_text'                => 'Made with ❤',
    'footer_note'                => 'Please respect copyright and content owner rights.',
    'footer_help'                => 'Help',
    'footer_about'               => 'About',
    'footer_contact'             => 'Contact',
    'footer_privacy'             => 'Privacy',
    'footer_terms'               => 'Terms of Use',

    // ═══════════════════════════════════════════════════════════════════
    // Miscellaneous
    // ═══════════════════════════════════════════════════════════════════
    'no_qualities'               => 'No qualities available',
    'copy_link'                  => 'Copy Link',
    'share'                      => 'Share',
    'download_list'              => 'Downloads',
    'history'                    => 'History',
    'settings'                   => 'Settings',
    'language'                   => 'Language',
    'dark_mode'                  => 'Dark Mode',
    'light_mode'                 => 'Light Mode',
    'auto_mode'                  => 'Auto Mode',
];
