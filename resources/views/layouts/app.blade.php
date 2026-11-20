<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ku' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.title') . ' — YouTube, TikTok, Instagram')</title>
    <meta name="description" content="@yield('description', __('messages.subtitle'))">
    <meta name="theme-color" content="#0b0d17" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#f6f7fb" media="(prefers-color-scheme: light)">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('messages.title') }}">
    <meta property="og:description" content="{{ __('messages.subtitle') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('favicon.png') }}">
    <meta name="twitter:card" content="summary">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">

    <script>
        // apply saved theme before paint (no flash)
        try { const t = localStorage.getItem('theme'); if (t) document.documentElement.dataset.theme = t; } catch (e) {}
    </script>

    @if(config('ads.ga_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode(config('ads.ga_id')) }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date()); gtag('config', @json(config('ads.ga_id')));
        </script>
    @endif

    @if(config('ads.adsense_client'))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ urlencode(config('ads.adsense_client')) }}" crossorigin="anonymous"></script>
    @endif

    @if(config('ads.custom'))
        @include('ads.custom-head')
    @endif

    <style>
        :root {
            --bg: #f6f7fb;
            --surface: #ffffff;
            --surface-2: #f0f2f8;
            --text: #12142b;
            --muted: #5d6280;
            --border: #e2e5f0;
            --primary: #5b4cf0;
            --primary-2: #7c3aed;
            --primary-soft: rgba(91, 76, 240, .10);
            --on-primary: #ffffff;
            --success: #12a150;
            --success-soft: rgba(18, 161, 80, .12);
            --danger: #d93a3a;
            --danger-soft: rgba(217, 58, 58, .10);
            --warning: #b7791f;
            --warning-soft: rgba(183, 121, 31, .12);
            --shadow: 0 10px 40px rgba(20, 22, 60, .08);
            --radius: 20px;
            --radius-sm: 12px;
        }
        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) {
                --bg: #0b0d17;
                --surface: #141728;
                --surface-2: #1c2036;
                --text: #eef0fa;
                --muted: #9aa0c0;
                --border: #262b47;
                --primary: #7b6dff;
                --primary-2: #a066ff;
                --primary-soft: rgba(123, 109, 255, .14);
                --success: #34d17a;
                --success-soft: rgba(52, 209, 122, .14);
                --danger: #ff6b6b;
                --danger-soft: rgba(255, 107, 107, .12);
                --warning: #f5b84a;
                --warning-soft: rgba(245, 184, 74, .12);
                --shadow: 0 10px 40px rgba(0, 0, 0, .35);
            }
        }
        :root[data-theme="dark"] {
            --bg: #0b0d17;
            --surface: #141728;
            --surface-2: #1c2036;
            --text: #eef0fa;
            --muted: #9aa0c0;
            --border: #262b47;
            --primary: #7b6dff;
            --primary-2: #a066ff;
            --primary-soft: rgba(123, 109, 255, .14);
            --success: #34d17a;
            --success-soft: rgba(52, 209, 122, .14);
            --danger: #ff6b6b;
            --danger-soft: rgba(255, 107, 107, .12);
            --warning: #f5b84a;
            --warning-soft: rgba(245, 184, 74, .12);
            --shadow: 0 10px 40px rgba(0, 0, 0, .35);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: 'Noto Kufi Arabic', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.7;
            min-height: 100vh;
            overflow-x: hidden;
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .wrap { width: 100%; max-width: 760px; margin: 0 auto; padding: 0 16px; }

        /* nav */
        .nav { display: flex; align-items: center; justify-content: space-between; padding: 16px 0; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 18px; color: var(--text); }
        .brand:hover { text-decoration: none; }
        .brand-mark {
            width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #fff;
        }
        .icon-btn {
            width: 40px; height: 40px; border-radius: 12px; border: 1px solid var(--border);
            background: var(--surface); color: var(--text); cursor: pointer; display: grid; place-items: center;
        }
        .icon-btn:hover { border-color: var(--primary); }
        .icon-btn svg { width: 20px; height: 20px; }

        .ad-slot { margin: 20px auto; text-align: center; min-height: 0; overflow: hidden; }

        footer { border-top: 1px solid var(--border); margin-top: 56px; padding: 28px 0 40px; color: var(--muted); font-size: 13px; text-align: center; }
        footer nav { display: flex; gap: 18px; justify-content: center; margin-bottom: 10px; flex-wrap: wrap; }
        footer a { color: var(--muted); }

        .prose h1 { font-size: 26px; margin: 16px 0; }
        .prose h2 { font-size: 18px; margin: 22px 0 8px; }
        .prose p, .prose li { color: var(--muted); margin-bottom: 8px; }
        .prose ul { padding-inline-start: 20px; }

        @yield('styles')
    </style>
</head>
<body>
    <div class="wrap">
        <header class="nav">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ __('messages.home') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                </span>
                {{ trim(__('messages.title')) }}
            </a>
            <button class="icon-btn" id="themeBtn" type="button" aria-label="{{ __('messages.theme_toggle') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            </button>
        </header>

        @yield('content')

        <footer>
            <nav>
                <a href="{{ route('home') }}">{{ __('messages.home') }}</a>
                <a href="{{ route('privacy') }}">{{ __('messages.privacy') }}</a>
                <a href="{{ route('terms') }}">{{ __('messages.terms') }}</a>
            </nav>
            <p>{{ __('messages.footer_note') }}</p>
            <p>© {{ date('Y') }} {{ trim(__('messages.title')) }}</p>
        </footer>
    </div>

    <script>
        document.getElementById('themeBtn').addEventListener('click', () => {
            const root = document.documentElement;
            const dark = root.dataset.theme
                ? root.dataset.theme === 'dark'
                : matchMedia('(prefers-color-scheme: dark)').matches;
            root.dataset.theme = dark ? 'light' : 'dark';
            try { localStorage.setItem('theme', root.dataset.theme); } catch (e) {}
        });
    </script>
    @yield('scripts')
</body>
</html>
