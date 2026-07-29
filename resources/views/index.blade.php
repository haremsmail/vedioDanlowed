<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.title') }} - داونلۆدەری ویدیۆ</title>
    <meta name="description" content="{{ __('messages.description') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #6c63ff;
            --primary-dark:  #5a52d5;
            --primary-light: #8b85ff;
            --accent:        #ff6584;
            --success:       #2ecc71;
            --warning:       #f39c12;
            --danger:        #e74c3c;
            --bg:            #0d0e1a;
            --surface:       #161728;
            --surface2:      #1e2035;
            --surface3:      #252740;
            --text:          #eeeef5;
            --text-muted:    #9898b8;
            --border:        rgba(108,99,255,0.18);
            --glow:          rgba(108,99,255,0.35);
            --radius:        16px;
            --radius-sm:     10px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Noto Kufi Arabic', 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(108,99,255,0.18) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(255,101,132,0.10) 0%, transparent 60%);
        }

        /* ── HEADER ────────────────────────────────────── */
        .header {
            text-align: center;
            padding: 40px 20px 20px;
            width: 100%;
            max-width: 700px;
        }
        .logo {
            font-size: 52px;
            margin-bottom: 12px;
            filter: drop-shadow(0 0 18px rgba(108,99,255,0.6));
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        .header h1 {
            font-size: 30px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        .header p {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.6;
            margin: 0;
        }
        .platform-badges {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 14px;
        }
        .badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, var(--surface2), var(--surface3));
            color: var(--text-muted);
            transition: all 0.2s;
            cursor: default;
        }
        .badge:hover {
            border-color: var(--primary);
            background: var(--surface3);
            color: var(--primary-light);
        }

        /* ── CARD ──────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
            margin-top: 10px;
        }

        /* ── URL INPUT ─────────────────────────────────── */
        .input-section { margin-bottom: 24px; }
        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .input-wrapper {
            display: flex;
            gap: 10px;
            align-items: stretch;
        }
        .url-input {
            flex: 1;
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px 18px;
            font-size: 14px;
            color: var(--text);
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            direction: ltr;
            text-align: left;
        }
        .url-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.2);
        }
        .url-input::placeholder { color: var(--text-muted); font-size: 13px; }

        .btn-paste {
            background: var(--surface3);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--primary-light);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 0 18px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-paste:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ── BUTTONS ───────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 28px;
            cursor: pointer;
            transition: all 0.25s;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 8px 24px rgba(108,99,255,0.35);
        }
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(108,99,255,0.5);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .btn-secondary {
            background: var(--surface3);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--surface2); color: var(--text); }

        .btn-success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
            box-shadow: 0 8px 24px rgba(46,204,113,0.3);
        }
        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(46,204,113,0.5);
        }
        .btn-success:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ── ALERTS ────────────────────────────────────── */
        .alert {
            display: none;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
            animation: slideDown 0.3s ease;
        }
        .alert.show { display: flex; }
        .alert-icon { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: rgba(46,204,113,0.15); border: 1px solid rgba(46,204,113,0.3); color: #6dffa8; }
        .alert-error   { background: rgba(231,76,60,0.15);  border: 1px solid rgba(231,76,60,0.3);  color: #ff9d95; }
        .alert-warning { background: rgba(243,156,18,0.15); border: 1px solid rgba(243,156,18,0.3); color: #ffd080; }
        .alert-info    { background: rgba(108,99,255,0.15); border: 1px solid rgba(108,99,255,0.3); color: #b8b3ff; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── LOADING ───────────────────────────────────── */
        .loading-bar-wrap {
            display: none;
            margin: 20px 0;
            text-align: center;
        }
        .loading-bar-wrap.show { display: block; }
        .loading-text {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }
        .loading-bar {
            height: 4px;
            background: var(--surface3);
            border-radius: 4px;
            overflow: hidden;
        }
        .loading-bar-inner {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
            width: 100%;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ── VIDEO INFO ────────────────────────────────── */
        #videoInfo { display: none; }
        #videoInfo.show {
            display: block;
            animation: slideDown 0.4s ease;
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 24px 0;
        }

        .thumbnail-wrap {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 20px;
            background: var(--surface2);
            aspect-ratio: 16/9;
        }
        .thumbnail-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .thumbnail-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60%;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
        }
        .duration-badge {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        .video-meta { margin-bottom: 20px; }
        .video-meta-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.4;
            margin-bottom: 6px;
        }
        .video-meta-sub {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ── QUALITY GRID ──────────────────────────────── */
        .quality-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .quality-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            margin-bottom: 24px;
        }
        .quality-btn {
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text);
            user-select: none;
            position: relative;
            overflow: hidden;
        }
        .quality-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .quality-btn:hover {
            border-color: var(--primary-light);
            background: var(--surface3);
            transform: translateY(-2px);
        }
        .quality-btn:hover::before {
            left: 100%;
        }
        .quality-btn.selected {
            border-color: var(--primary);
            background: rgba(108,99,255,0.18);
            box-shadow: 0 0 0 2px rgba(108,99,255,0.3), 0 4px 16px rgba(108,99,255,0.25);
        }
        .quality-btn .q-label {
            font-size: 15px;
            font-weight: 700;
            display: block;
            margin-bottom: 3px;
        }
        .quality-btn .q-sub {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
        }
        .quality-btn .q-info {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 4px;
            opacity: 0.7;
        }
        .quality-btn.selected .q-sub { color: var(--primary-light); }
        .quality-btn.selected .q-info { color: var(--primary-light); opacity: 1; }
        .quality-btn.audio-btn .q-label { font-size: 13px; }

        /* ── FORMAT BADGES ────────────────────────────── */
        .format-info {
            background: rgba(108, 99, 255, 0.08);
            border: 1px solid rgba(108, 99, 255, 0.2);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }
        .format-badge {
            display: inline-block;
            background: rgba(108, 99, 255, 0.15);
            color: var(--primary-light);
            padding: 2px 8px;
            border-radius: 4px;
            margin: 2px 4px 2px 0;
            font-size: 12px;
            font-weight: 600;
        }

        /* ── ACTION BUTTONS ────────────────────────────── */
        .action-row {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }
        .action-row .btn { flex: 1; }

        /* ── FOOTER ────────────────────────────────────── */
        .footer {
            text-align: center;
            margin-top: 28px;
            padding-bottom: 30px;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.7;
        }
        .footer span { color: var(--accent); }

        /* ── RESPONSIVE ────────────────────────────────── */
        @media (max-width: 500px) {
            .card { padding: 24px 18px; }
            .header h1 { font-size: 22px; }
            .input-wrapper { flex-direction: column; }
            .btn-paste { padding: 14px; }
            .quality-grid { grid-template-columns: repeat(3, 1fr); }
            .action-row { flex-direction: column-reverse; }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="logo">🎬</div>
        <h1>{{ __('messages.title') }}</h1>
        <p>{{ __('messages.subtitle') ?? __('messages.description') }}</p>
        <div class="platform-badges">
            <span class="badge">▶ {{ __('messages.platform_youtube') }}</span>
            <span class="badge">♪ {{ __('messages.platform_tiktok') }}</span>
            <span class="badge">📷 {{ __('messages.platform_instagram') }}</span>
            <span class="badge">📸 {{ __('messages.platform_snapchat') }}</span>
            <span class="badge">👍 {{ __('messages.platform_facebook') }}</span>
            <span class="badge">🐦 {{ __('messages.platform_twitter') }}</span>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="card">

        <!-- Alerts -->
        <div id="alertSuccess" class="alert alert-success">
            <span class="alert-icon">✓</span>
            <span id="alertSuccessText"></span>
        </div>
        <div id="alertError" class="alert alert-error">
            <span class="alert-icon">✕</span>
            <span id="alertErrorText"></span>
        </div>
        <div id="alertWarning" class="alert alert-warning">
            <span class="alert-icon">⚠</span>
            <span id="alertWarningText"></span>
        </div>
        <div id="alertInfo" class="alert alert-info">
            <span class="alert-icon">ℹ</span>
            <span id="alertInfoText"></span>
        </div>

        <!-- URL Input -->
        <div class="input-section">
            <label class="input-label" for="videoUrl">{{ __('messages.input_url') }}</label>
            <div class="input-wrapper">
                <input
                    type="text"
                    id="videoUrl"
                    class="url-input"
                    placeholder="{{ __('messages.input_placeholder') }}"
                    autocomplete="off"
                    spellcheck="false"
                >
                <button class="btn-paste" id="pasteBtn" title="{{ __('messages.paste_button') }}">
                    📋 {{ __('messages.paste_button') }}
                </button>
            </div>
        </div>

        <!-- Analyze Button -->
        <button class="btn btn-primary" id="analyzeBtn">
            🔍 {{ __('messages.analyze_button') }}
        </button>

        <!-- Loading Bar -->
        <div class="loading-bar-wrap" id="loadingBar">
            <div class="loading-text" id="loadingText">{{ __('messages.analyzing') }}</div>
            <div class="loading-bar"><div class="loading-bar-inner"></div></div>
        </div>

        <!-- Video Info Section -->
        <div id="videoInfo">
            <div class="divider"></div>

            <!-- Thumbnail -->
            <div class="thumbnail-wrap">
                <img id="vThumb" src="" alt="thumbnail">
                <div class="thumbnail-overlay"></div>
                <div class="duration-badge" id="vDuration"></div>
            </div>

            <!-- Meta -->
            <div class="video-meta">
                <div class="video-meta-title" id="vTitle"></div>
                <div class="video-meta-sub" id="vUploader"></div>
            </div>

            <!-- Qualities -->
            <div class="quality-label">{{ __('messages.available_qualities') }}</div>
            <div class="quality-grid" id="qualityGrid"></div>

            <!-- Download & Back -->
            <div class="action-row">
                <button class="btn btn-secondary" id="backBtn">
                    ↩ {{ __('messages.back_button') }}
                </button>
                <button class="btn btn-success" id="downloadBtn" disabled>
                    ⬇ {{ __('messages.download_button') }}
                </button>
            </div>
        </div>
    </div>
    <!-- FOOTER -->
    <div class="footer">
        <p>{{ __('messages.footer_text') }}</p>
        <p style="margin-bottom: 0;">{{ __('messages.footer_note') }}</p>
    </div>

<script>
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const API    = '/api';

    let currentUrl    = null;
    let selectedFmt   = null;

    const $  = id => document.getElementById(id);

    // ── Alerts ──────────────────────────────────────
    function showAlert(type, msg) {
        ['Success','Error','Warning','Info'].forEach(t => {
            $('alert' + t).classList.remove('show');
        });
        const cap = type.charAt(0).toUpperCase() + type.slice(1);
        $('alert' + cap + 'Text').textContent = msg;
        $('alert' + cap).classList.add('show');
        if (type !== 'error') {
            setTimeout(() => $('alert' + cap).classList.remove('show'), 6000);
        }
    }
    function clearAlerts() {
        ['Success','Error','Warning','Info'].forEach(t => {
            $('alert' + t).classList.remove('show');
        });
    }

    // ── Loading ──────────────────────────────────────
    function setLoading(show, text = '{{ __("messages.analyzing") }}') {
        $('loadingBar').classList.toggle('show', show);
        $('loadingText').textContent = text;
        $('analyzeBtn').disabled = show;
    }

    // ── Format duration ──────────────────────────────
    function fmtDuration(sec) {
        if (!sec) return '';
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = Math.floor(sec % 60);
        if (h > 0) return `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        return `${m}:${String(s).padStart(2,'0')}`;
    }

    // ── Paste from clipboard ─────────────────────────
    $('pasteBtn').addEventListener('click', async () => {
        try {
            const text = await navigator.clipboard.readText();
            if (text) {
                $('videoUrl').value = text.trim();
                $('videoUrl').focus();
                analyzeVideo();
            }
        } catch {
            showAlert('warning', '{{ __("messages.paste_warning") }}');
        }
    });

    // ── Auto-paste on document paste event ───────────
    document.addEventListener('paste', (e) => {
        // Prevent auto-paste if user is already typing in an input
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        
        const text = (e.clipboardData || window.clipboardData).getData('text');
        if (text && (text.includes('http://') || text.includes('https://'))) {
            $('videoUrl').value = text.trim();
            $('videoUrl').focus();
            analyzeVideo();
        }
    });

    // ── Analyze ──────────────────────────────────────
    $('analyzeBtn').addEventListener('click', analyzeVideo);
    $('videoUrl').addEventListener('keydown', e => { if (e.key === 'Enter') analyzeVideo(); });

    async function analyzeVideo() {
        const url = $('videoUrl').value.trim();
        if (!url) {
            showAlert('warning', '{{ __("messages.input_url") }}');
            return;
        }

        clearAlerts();
        setLoading(true, '{{ __("messages.analyzing") }}');
        $('videoInfo').classList.remove('show');
        selectedFmt = null;
        $('downloadBtn').disabled = true;

        try {
            const res  = await fetch(`${API}/analyze`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ url })
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                showAlert('error', data.message || '{{ __("messages.video_fetch_failed") }}');
                return;
            }

            currentUrl = url;
            renderVideo(data.data);
        } catch (e) {
            showAlert('error', '{{ __("messages.video_fetch_failed") }}');
            console.error(e);
        } finally {
            setLoading(false);
        }
    }

    // ── Render video info ────────────────────────────
    function renderVideo(v) {
        // Thumbnail
        const thumb = $('vThumb');
        if (v.thumbnail) {
            thumb.src = v.thumbnail;
            thumb.onerror = () => { thumb.src = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 9%22%3E%3Crect fill=%22%231e2035%22 width=%2216%22 height=%229%22/%3E%3Ctext x=%228%22 y=%225.5%22 text-anchor=%22middle%22 fill=%22%239898b8%22 font-size=%223%22%3E🎬%3C/text%3E%3C/svg%3E'; };
        }
        $('vDuration').textContent = fmtDuration(v.duration);
        $('vTitle').textContent    = v.title || '';
        $('vUploader').textContent = v.uploader ? '📺 ' + v.uploader : '';

        // Quality grid
        const grid = $('qualityGrid');
        grid.innerHTML = '';

        if (!v.formats || v.formats.length === 0) {
            grid.innerHTML = `<p style="color:var(--text-muted);font-size:13px">{{ __('messages.no_qualities') }}</p>`;
        } else {
            // Group formats by type
            const videoFormats = v.formats.filter(f => f.format === 'video');
            const audioFormats = v.formats.filter(f => f.format === 'audio');

            // Video formats
            videoFormats.forEach(fmt => {
                const btn = document.createElement('div');
                btn.className = 'quality-btn';
                btn.dataset.fmt = JSON.stringify(fmt);

                let icon = '🎬';
                let sub  = fmt.ext ? fmt.ext.toUpperCase() : 'MP4';
                let info = '';

                if (fmt.height >= 2160) { icon = '🔥'; info = '4K'; }
                else if (fmt.height >= 1440) { icon = '✨'; info = '2K'; }
                else if (fmt.height >= 1080) { icon = '🌟'; info = 'FHD'; }
                else if (fmt.height >= 720) { icon = '⭐'; info = 'HD'; }
                else if (fmt.height >= 480) { icon = '◆'; info = 'SD'; }
                else { icon = '▪'; info = 'LQ'; }

                btn.innerHTML = `
                    <span class="q-label">${icon} ${fmt.quality}</span>
                    <span class="q-sub">${sub}</span>
                    ${info ? `<span class="q-info">${info}</span>` : ''}
                `;

                btn.addEventListener('click', () => {
                    document.querySelectorAll('.quality-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    selectedFmt = fmt;
                    $('downloadBtn').disabled = false;
                });

                grid.appendChild(btn);
            });

            // Audio formats
            audioFormats.forEach(fmt => {
                const btn = document.createElement('div');
                btn.className = 'quality-btn audio-btn';
                btn.dataset.fmt = JSON.stringify(fmt);

                const sub = fmt.ext ? fmt.ext.toUpperCase() : 'MP3';
                btn.innerHTML = `
                    <span class="q-label">🎵 ${fmt.quality}</span>
                    <span class="q-sub">${sub}</span>
                    <span class="q-info">Audio</span>
                `;

                btn.addEventListener('click', () => {
                    document.querySelectorAll('.quality-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    selectedFmt = fmt;
                    $('downloadBtn').disabled = false;
                });

                grid.appendChild(btn);
            });
        }

        $('videoInfo').classList.add('show');
    }

    // ── Download ─────────────────────────────────────
    $('downloadBtn').addEventListener('click', async () => {
        if (!selectedFmt || !currentUrl) {
            showAlert('warning', '{{ __("messages.select_quality") }}');
            return;
        }

        clearAlerts();
        const origText = $('downloadBtn').innerHTML;
        $('downloadBtn').disabled = true;
        $('downloadBtn').innerHTML = '⏳ {{ __("messages.downloading") }}...';
        setLoading(true, '{{ __("messages.downloading") }}');

        try {
            const res  = await fetch(`${API}/download`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({
                    url:       currentUrl,
                    format_id: selectedFmt.id,
                    quality:   selectedFmt.quality
                })
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                showAlert('error', data.message || '{{ __("messages.download_failed") }}');
                $('downloadBtn').disabled = false;
                $('downloadBtn').innerHTML = origText;
                return;
            }

            showAlert('success', '{{ __("messages.download_ready") }}');

            // Trigger browser file download
            const a = document.createElement('a');
            a.href     = data.download_url;
            a.download = data.filename || 'video';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            // Re-enable button after short delay
            setTimeout(() => {
                $('downloadBtn').disabled = false;
                $('downloadBtn').innerHTML = origText;
            }, 3000);

        } catch (e) {
            showAlert('error', '{{ __("messages.download_failed") }}');
            $('downloadBtn').disabled = false;
            $('downloadBtn').innerHTML = origText;
            console.error(e);
        } finally {
            setLoading(false);
        }
    });

    // ── Back button ───────────────────────────────────
    $('backBtn').addEventListener('click', () => {
        $('videoInfo').classList.remove('show');
        $('videoUrl').value = '';
        currentUrl  = null;
        selectedFmt = null;
        $('downloadBtn').disabled = true;
        clearAlerts();
    });
</script>
</body>
</html>
