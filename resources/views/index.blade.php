@extends('layouts.app')

@section('styles')
    .hero { text-align: center; padding: 20px 0 8px; }
    .hero h1 { font-size: clamp(24px, 6vw, 36px); font-weight: 800; line-height: 1.4; letter-spacing: -.3px; }
    .hero h1 span { background: linear-gradient(135deg, var(--primary), var(--primary-2)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .hero p { color: var(--muted); margin-top: 8px; font-size: 15px; }

    .chips { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin: 18px 0 4px; }
    .chip { font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 999px; background: var(--surface); border: 1px solid var(--border); color: var(--muted); }

    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); padding: 22px; margin-top: 20px; }

    .input-row { display: flex; gap: 8px; background: var(--surface-2); border: 1.5px solid var(--border); border-radius: 16px; padding: 6px; transition: border-color .2s, box-shadow .2s; }
    .input-row:focus-within { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-soft); }
    .input-row input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; color: var(--text); font: inherit; font-size: 15px; padding: 10px 12px; direction: ltr; text-align: left; }
    .input-row input::placeholder { color: var(--muted); }
    .btn-ghost { border: 0; background: var(--surface); color: var(--primary); font: inherit; font-weight: 700; font-size: 13px; padding: 0 14px; border-radius: 12px; cursor: pointer; white-space: nowrap; }
    .btn-ghost:hover { background: var(--primary-soft); }

    .btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; border: 0; border-radius: 14px; font: inherit; font-weight: 700; font-size: 16px; padding: 14px 20px; cursor: pointer; transition: transform .15s, opacity .2s, box-shadow .2s; text-decoration: none !important; }
    .btn:active { transform: scale(.98); }
    .btn:disabled { opacity: .55; cursor: not-allowed; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: var(--on-primary); box-shadow: 0 8px 24px var(--primary-soft); margin-top: 12px; }
    .btn-success { background: var(--success); color: #fff; }
    .btn-soft { background: var(--surface-2); color: var(--text); border: 1px solid var(--border); }

    .spinner { width: 18px; height: 18px; border: 2.5px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: spin .8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .alert { display: none; margin-top: 14px; padding: 12px 14px; border-radius: 12px; font-size: 14px; font-weight: 600; }
    .alert.show { display: block; }
    .alert.error { background: var(--danger-soft); color: var(--danger); }
    .alert.success { background: var(--success-soft); color: var(--success); }
    .alert.warning { background: var(--warning-soft); color: var(--warning); }

    .skeleton { display: none; margin-top: 18px; gap: 14px; }
    .skeleton.show { display: flex; }
    .sk { background: linear-gradient(90deg, var(--surface-2), var(--border), var(--surface-2)); background-size: 200% 100%; animation: sh 1.2s infinite; border-radius: 12px; }
    @keyframes sh { to { background-position: -200% 0; } }

    #result { display: none; margin-top: 20px; }
    #result.show { display: block; animation: fade .3s ease; }
    @keyframes fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

    .video { display: flex; gap: 14px; align-items: flex-start; }
    .thumb { position: relative; flex: 0 0 160px; aspect-ratio: 16/9; border-radius: 12px; overflow: hidden; background: var(--surface-2); }
    .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .thumb .dur { position: absolute; bottom: 6px; left: 6px; background: rgba(0,0,0,.75); color: #fff; font-size: 11px; font-weight: 700; padding: 1px 7px; border-radius: 6px; direction: ltr; }
    .video h2 { font-size: 15px; font-weight: 700; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word; }
    .video .by { color: var(--muted); font-size: 13px; margin-top: 4px; }

    .section-label { font-size: 13px; font-weight: 700; color: var(--muted); margin: 20px 0 10px; }
    .qgrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(118px, 1fr)); gap: 8px; }
    .q { position: relative; border: 1.5px solid var(--border); background: var(--surface); border-radius: 12px; padding: 10px; cursor: pointer; text-align: center; font: inherit; color: var(--text); transition: border-color .15s, background .15s; }
    .q:hover { border-color: var(--primary); }
    .q.sel { border-color: var(--primary); background: var(--primary-soft); }
    .q b { display: block; font-size: 16px; direction: ltr; }
    .q small { display: block; color: var(--muted); font-size: 11px; direction: ltr; }
    .q .tag { position: absolute; top: -8px; inset-inline-end: 8px; font-size: 10px; font-weight: 800; padding: 0 6px; border-radius: 6px; background: var(--primary); color: #fff; }

    .actions { display: flex; gap: 10px; margin-top: 18px; }
    .actions .btn { margin: 0; }
    .actions .btn-soft { flex: 0 0 34%; }

    .progress { display: none; margin-top: 18px; }
    .progress.show { display: block; }
    .bar { height: 12px; background: var(--surface-2); border-radius: 999px; overflow: hidden; border: 1px solid var(--border); }
    .bar i { display: block; height: 100%; width: 0; background: linear-gradient(90deg, var(--primary), var(--primary-2)); border-radius: 999px; transition: width .4s ease; }
    .bar.indeterminate i { width: 35% !important; animation: ind 1.2s ease-in-out infinite; }
    @keyframes ind { 0% { margin-inline-start: -35%; } 100% { margin-inline-start: 100%; } }
    .pmeta { display: flex; justify-content: space-between; font-size: 13px; color: var(--muted); margin-top: 8px; gap: 10px; }
    .pmeta b { color: var(--text); direction: ltr; }

    .done { display: none; margin-top: 18px; text-align: center; }
    .done.show { display: block; animation: fade .3s ease; }
    .done .ok { width: 56px; height: 56px; border-radius: 50%; display: grid; place-items: center; margin: 0 auto 10px; background: var(--success-soft); color: var(--success); }
    .done .btn { margin-top: 10px; }

    .grid4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 28px; }
    .feat { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 14px; text-align: center; }
    .feat .ic { width: 38px; height: 38px; margin: 0 auto 6px; border-radius: 10px; display: grid; place-items: center; background: var(--primary-soft); color: var(--primary); }
    .feat b { display: block; font-size: 14px; }
    .feat span { font-size: 12px; color: var(--muted); }

    h3.h { font-size: 20px; font-weight: 800; margin: 40px 0 14px; text-align: center; }
    .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .step { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 16px; }
    .step .n { width: 28px; height: 28px; border-radius: 8px; background: var(--primary); color: #fff; font-weight: 800; display: grid; place-items: center; margin-bottom: 8px; }
    .step p { font-size: 14px; }

    details { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 14px 16px; margin-bottom: 8px; }
    summary { cursor: pointer; font-weight: 700; font-size: 15px; }
    details p { color: var(--muted); font-size: 14px; margin-top: 8px; }

    @media (max-width: 600px) {
        .card { padding: 16px; }
        .video { flex-direction: column; }
        .thumb { flex: none; width: 100%; }
        .grid4 { grid-template-columns: repeat(2, 1fr); }
        .steps { grid-template-columns: 1fr; }
        .qgrid { grid-template-columns: repeat(3, 1fr); }
        .actions { flex-direction: column-reverse; }
        .actions .btn-soft { flex: none; }
    }
@endsection

@section('content')
    @include('ads.slot', ['slot' => config('ads.adsense_slot_top'), 'custom' => true])

    <section class="hero">
        <h1><span>{{ trim(__('messages.title')) }}</span><br>YouTube · TikTok · Instagram</h1>
        <p>{{ __('messages.subtitle') }}</p>
        <div class="chips" aria-hidden="true">
            <span class="chip">YouTube</span><span class="chip">TikTok</span><span class="chip">Instagram</span>
            <span class="chip">Facebook</span><span class="chip">X / Twitter</span><span class="chip">Snapchat</span>
            <span class="chip">Pinterest</span><span class="chip">Reddit</span>
        </div>
    </section>

    <main class="card">
        <form id="form" autocomplete="off" novalidate>
            <label for="url" class="section-label" style="display:block;margin-top:0">{{ __('messages.input_url') }}</label>
            <div class="input-row">
                <input type="url" id="url" inputmode="url" placeholder="https://..." spellcheck="false" required>
                <button type="button" class="btn-ghost" id="pasteBtn">{{ __('messages.paste_button') }}</button>
            </div>
            <button type="submit" class="btn btn-primary" id="analyzeBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <span>{{ __('messages.analyze_button') }}</span>
            </button>
        </form>

        <div class="alert" id="alert" role="alert"></div>

        <div class="skeleton" id="skeleton" aria-hidden="true">
            <div class="sk" style="flex:0 0 160px;aspect-ratio:16/9"></div>
            <div style="flex:1;display:flex;flex-direction:column;gap:10px">
                <div class="sk" style="height:16px"></div><div class="sk" style="height:16px;width:70%"></div><div class="sk" style="height:12px;width:40%"></div>
            </div>
        </div>

        <section id="result" aria-live="polite">
            <div class="video">
                <div class="thumb"><img id="thumb" alt="" referrerpolicy="no-referrer" loading="lazy"><span class="dur" id="dur"></span></div>
                <div><h2 id="vtitle"></h2><div class="by" id="vby"></div></div>
            </div>

            <div id="picker">
                <div class="section-label">{{ __('messages.available_qualities') }}</div>
                <div class="qgrid" id="qgrid"></div>
                <div class="actions">
                    <button type="button" class="btn btn-soft" id="backBtn">{{ __('messages.back_button') }}</button>
                    <button type="button" class="btn btn-primary" id="dlBtn" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                        <span>{{ __('messages.download_button') }}</span>
                    </button>
                </div>
            </div>

            <div class="progress" id="progress">
                <div class="bar" id="bar"><i></i></div>
                <div class="pmeta"><span id="pstatus">{{ __('messages.starting') }}</span><b id="ppct">0%</b></div>
                <div class="pmeta"><span id="pspeed"></span><span id="peta"></span></div>
                @include('ads.slot', ['slot' => config('ads.adsense_slot_middle'), 'custom' => true])
            </div>

            <div class="done" id="done">
                <div class="ok"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
                <b>{{ __('messages.download_complete') }}</b>
                <a class="btn btn-success" id="saveBtn" href="#" download>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                    <span>{{ __('messages.save_file') }}</span> <small id="fsize" style="opacity:.8;direction:ltr"></small>
                </a>
                <button type="button" class="btn btn-soft" id="againBtn">{{ __('messages.download_another') }}</button>
            </div>
        </section>
    </main>

    <div class="grid4">
        <div class="feat"><div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg></div><b>{{ __('messages.feat_fast') }}</b><span>{{ __('messages.feat_fast_desc') }}</span></div>
        <div class="feat"><div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><b>{{ __('messages.feat_safe') }}</b><span>{{ __('messages.feat_safe_desc') }}</span></div>
        <div class="feat"><div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21.2l8.8-8.8a5.5 5.5 0 0 0 0-7.8z"/></svg></div><b>{{ __('messages.feat_free') }}</b><span>{{ __('messages.feat_free_desc') }}</span></div>
        <div class="feat"><div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M7 15V9l3 3 3-3v6M17 9v6"/></svg></div><b>{{ __('messages.feat_hd') }}</b><span>{{ __('messages.feat_hd_desc') }}</span></div>
    </div>

    <h3 class="h">{{ __('messages.how_title') }}</h3>
    <div class="steps">
        <div class="step"><div class="n">1</div><p>{{ __('messages.how_1') }}</p></div>
        <div class="step"><div class="n">2</div><p>{{ __('messages.how_2') }}</p></div>
        <div class="step"><div class="n">3</div><p>{{ __('messages.how_3') }}</p></div>
    </div>

    @include('ads.slot', ['slot' => config('ads.adsense_slot_bottom'), 'custom' => false])

    <h3 class="h">{{ __('messages.faq_title') }}</h3>
    <details><summary>{{ __('messages.faq_q1') }}</summary><p>{{ __('messages.faq_a1') }}</p></details>
    <details><summary>{{ __('messages.faq_q2') }}</summary><p>{{ __('messages.faq_a2') }}</p></details>
    <details><summary>{{ __('messages.faq_q3') }}</summary><p>{{ __('messages.faq_a3') }}</p></details>
@endsection

@section('scripts')
<script>
(() => {
    const T = {!! json_encode([
        'analyze'      => __('messages.analyze_button'),
        'analyzing'    => __('messages.analyzing'),
        'download'     => __('messages.download_button'),
        'starting'     => __('messages.starting'),
        'downloading'  => __('messages.downloading'),
        'processing'   => __('messages.processing'),
        'fetchFailed'  => __('messages.video_fetch_failed'),
        'dlFailed'     => __('messages.download_failed'),
        'pasteWarn'    => __('messages.paste_warning'),
        'enterUrl'     => __('messages.input_url'),
        'network'      => __('messages.network_error'),
        'audio'        => __('messages.audio_only'),
        'ready'        => __('messages.download_ready'),
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const $ = id => document.getElementById(id);

    let current = null;   // { url, data }
    let selected = null;  // format object
    let pollTimer = null;

    // ── helpers ───────────────────────────────────
    function alertMsg(type, msg) {
        const el = $('alert');
        el.className = 'alert show ' + type;
        el.textContent = msg;
    }
    function clearAlert() { $('alert').className = 'alert'; }

    function fmtDuration(s) {
        s = Math.floor(s || 0); if (!s) return '';
        const h = Math.floor(s / 3600), m = Math.floor(s % 3600 / 60), sec = s % 60;
        return (h ? h + ':' + String(m).padStart(2, '0') : m) + ':' + String(sec).padStart(2, '0');
    }
    function fmtBytes(b) {
        if (!b) return '';
        const u = ['B', 'KB', 'MB', 'GB']; let i = 0;
        while (b >= 1024 && i < u.length - 1) { b /= 1024; i++; }
        return b.toFixed(i > 1 ? 1 : 0) + ' ' + u[i];
    }

    function setBusy(btn, busy, label) {
        btn.disabled = busy;
        const span = btn.querySelector('span');
        if (busy) {
            btn.dataset.html = btn.innerHTML;
            btn.innerHTML = '<span class="spinner"></span>';
            const s = document.createElement('span'); s.textContent = label; btn.appendChild(s);
        } else if (btn.dataset.html) {
            btn.innerHTML = btn.dataset.html;
        }
    }

    async function api(method, path, body) {
        const res = await fetch(path, {
            method,
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            credentials: 'same-origin',
            body: body ? JSON.stringify(body) : undefined,
        });
        let data = {};
        try { data = await res.json(); } catch (e) {}
        if (res.status === 419) { location.reload(); throw new Error('expired'); }
        if (!res.ok || data.success === false) {
            const msg = data.message || (data.errors && Object.values(data.errors)[0][0]) || T.fetchFailed;
            throw new Error(msg);
        }
        return data;
    }

    function resetView() {
        clearInterval(pollTimer);
        $('result').classList.remove('show');
        $('picker').style.display = '';
        $('progress').classList.remove('show');
        $('done').classList.remove('show');
        selected = null;
        $('dlBtn').disabled = true;
    }

    // ── paste ─────────────────────────────────────
    $('pasteBtn').addEventListener('click', async () => {
        try {
            const text = (await navigator.clipboard.readText()).trim();
            if (text) { $('url').value = text; analyze(); }
        } catch (e) { alertMsg('warning', T.pasteWarn); $('url').focus(); }
    });
    $('url').addEventListener('paste', () => setTimeout(() => {
        if (/^https?:\/\//i.test($('url').value.trim())) analyze();
    }, 50));

    // ── analyze ───────────────────────────────────
    $('form').addEventListener('submit', e => { e.preventDefault(); analyze(); });

    async function analyze() {
        const url = $('url').value.trim();
        if (!/^https?:\/\/\S+$/i.test(url)) { alertMsg('warning', T.enterUrl); $('url').focus(); return; }
        if ($('analyzeBtn').disabled) return;

        clearAlert(); resetView();
        setBusy($('analyzeBtn'), true, T.analyzing);
        $('skeleton').classList.add('show');

        try {
            const res = await api('POST', '/api/analyze', { url });
            current = { url, data: res.data };
            render(res.data);
        } catch (e) {
            if (e.message !== 'expired') alertMsg('error', e.message === 'Failed to fetch' ? T.network : e.message);
        } finally {
            $('skeleton').classList.remove('show');
            setBusy($('analyzeBtn'), false);
        }
    }

    function render(v) {
        const img = $('thumb');
        img.hidden = !v.thumbnail;
        if (v.thumbnail) { img.src = v.thumbnail; img.onerror = () => { img.hidden = true; }; }
        $('dur').textContent = fmtDuration(v.duration);
        $('dur').hidden = !v.duration;
        $('vtitle').textContent = v.title || '';
        $('vby').textContent = v.uploader || '';

        const grid = $('qgrid');
        grid.replaceChildren();
        v.formats.forEach((f, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'q';
            const label = document.createElement('b');
            label.textContent = f.type === 'audio' ? 'MP3' : f.quality + (f.fps > 30 ? f.fps : '');
            const sub = document.createElement('small');
            sub.textContent = f.type === 'audio' ? T.audio : (fmtBytes(f.size) || 'MP4');
            b.append(label, sub);
            const tagText = f.height >= 2160 ? '4K' : f.height >= 1440 ? '2K' : f.height >= 1080 ? 'FHD' : f.height >= 720 ? 'HD' : '';
            if (tagText) { const t = document.createElement('span'); t.className = 'tag'; t.textContent = tagText; b.appendChild(t); }
            b.addEventListener('click', () => {
                grid.querySelectorAll('.q').forEach(x => x.classList.remove('sel'));
                b.classList.add('sel');
                selected = f;
                $('dlBtn').disabled = false;
            });
            grid.appendChild(b);
            // preselect best ≤1080p video for convenience
            if (!selected && f.type === 'video' && (f.height <= 1080)) b.click();
        });
        if (!selected && grid.firstChild) grid.firstChild.click();

        $('result').classList.add('show');
        $('result').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── download ──────────────────────────────────
    $('dlBtn').addEventListener('click', async () => {
        if (!selected || !current) return;
        clearAlert();
        setBusy($('dlBtn'), true, T.starting);
        try {
            const res = await api('POST', '/api/download', { url: current.url, format_id: selected.id });
            $('picker').style.display = 'none';
            showProgress({ status: 'queued', progress: 0 });
            $('progress').classList.add('show');
            poll(res.job_id);
        } catch (e) {
            if (e.message !== 'expired') alertMsg('error', e.message);
        } finally {
            setBusy($('dlBtn'), false);
        }
    });

    function showProgress(s) {
        const bar = $('bar');
        const pct = Math.max(0, Math.min(100, s.progress || 0));
        bar.classList.toggle('indeterminate', s.status === 'queued' || (s.status === 'downloading' && pct === 0) || s.status === 'processing');
        bar.firstElementChild.style.width = pct + '%';
        $('ppct').textContent = s.status === 'processing' ? '' : pct + '%';
        $('pstatus').textContent = s.status === 'processing' ? T.processing : (s.status === 'queued' ? T.starting : T.downloading);
        $('pspeed').textContent = s.speed ? fmtBytes(s.speed) + '/s' : '';
        $('peta').textContent = s.eta ? fmtDuration(s.eta) : '';
    }

    function poll(id) {
        let failures = 0;
        clearInterval(pollTimer);
        pollTimer = setInterval(async () => {
            try {
                const s = await api('GET', '/api/status/' + id);
                failures = 0;
                if (s.status === 'done') {
                    clearInterval(pollTimer);
                    finish(s);
                } else if (s.status === 'error') {
                    clearInterval(pollTimer);
                    $('progress').classList.remove('show');
                    $('picker').style.display = '';
                    alertMsg('error', s.error || T.dlFailed);
                } else {
                    showProgress(s);
                }
            } catch (e) {
                if (++failures >= 8) {
                    clearInterval(pollTimer);
                    $('progress').classList.remove('show');
                    $('picker').style.display = '';
                    alertMsg('error', T.dlFailed);
                }
            }
        }, 1200);
    }

    function finish(s) {
        $('progress').classList.remove('show');
        const href = new URL(s.file_url, location.origin).href;
        $('saveBtn').href = href;
        $('fsize').textContent = s.size ? '(' + fmtBytes(s.size) + ')' : '';
        $('done').classList.add('show');
        alertMsg('success', T.ready);
        // auto-start the download; the button stays for iOS / blocked downloads
        const a = document.createElement('a');
        a.href = href; a.download = ''; a.style.display = 'none';
        document.body.appendChild(a); a.click(); a.remove();
    }

    $('backBtn').addEventListener('click', () => { resetView(); clearAlert(); $('url').value = ''; $('url').focus(); });
    $('againBtn').addEventListener('click', () => { resetView(); clearAlert(); $('url').value = ''; $('url').focus(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
})();
</script>
@endsection
