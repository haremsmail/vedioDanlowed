# داونلۆدەری ڤیدیۆ — Video Downloader

ماڵپەڕێکی Laravel بۆ داگرتنی ڤیدیۆ لە YouTube، TikTok، Instagram، Facebook، X و زۆری تر.

## چۆن کاردەکات

```
بەکارهێنەر لینک دادەنێت  →  POST /api/analyze   (yt-dlp زانیاری وەردەگرێت، 15 خولەک cache)
کوالیتی هەڵدەبژێرێت       →  POST /api/download  (کارێک دروست دەکات، دەستبەجێ وەڵام دەداتەوە)
شریتی پێشکەوتن            →  GET  /api/status/{id}  (هەر 1.2 چرکە جارێک)
فایلەکە                    →  GET  /download/{id}  (Range/resume پشتگیری دەکرێت)
```

داگرتنەکە لە پرۆسێسێکی جیاوازدا ئەنجام دەدرێت (`php artisan video:worker`)، بۆیە بە کاتی درێژیش ماڵپەڕەکە ناوەستێت و timeout ڕوونادات.

## ئاسایش

- پاراستنی CSRF و session لەسەر هەموو API ـەکان (کەس ناتوانێت وەک API ـی بێبەرامبەر بەکاریبهێنێت)
- تەنها ئەو ماڵپەڕانەی لە `config/ytdlp.php` ن ڕێگەپێدراون، و پاراستن لە SSRF
- هەر فایلێک تەنها بۆ ئەو کەسەیە کە دایگرتووە (session)
- سنووری داواکاری بۆ هەر IP، و سنووری داگرتنی هاوکات
- فایلەکان دوای `FILE_TTL_MINUTES` بە خۆکاری دەسڕدرێنەوە
- CSP، HSTS، X-Frame-Options و هێدەرەکانی تری ئاسایش

---

## بڵاوکردنەوە بە خۆڕایی لەسەر Render.com

1. کۆدەکە push بکە بۆ GitHub:
   ```bash
   git add -A
   git commit -m "Rebuild downloader"
   git push origin main
   ```
2. بچۆ بۆ <https://render.com> و بە GitHub ـەکەت sign up بکە (کارتی بانکی پێویست نییە).
3. **New → Blueprint** → ڕیپۆکەت هەڵبژێرە (`vedioDanlowed`) → **Apply**.
   Render خۆی `render.yaml` دەخوێنێتەوە و هەموو شتێک ڕێکدەخات.
4. 5 بۆ 10 خولەک چاوەڕێ بکە. پاشان لینکێکی وەک `https://video-downloader-xxxx.onrender.com` وەردەگریت.

> تێبینی: لە پلانی خۆڕایی Render، ئەگەر 15 خولەک کەس سەردانی نەکات ماڵپەڕەکە دەخەوێت و یەکەم کردنەوە نزیکەی 1 خولەک دەخایەنێت.
> بۆ ئەوەی هەمیشە بەئاگا بێت، لە <https://uptimerobot.com> (خۆڕایی) مۆنیتەرێک دروست بکە بۆ `https://YOUR-SITE.onrender.com/up` هەر 5 خولەک جارێک.

### YouTube لەسەر سێرڤەر (گرنگ)

YouTube زۆرجار IP ی سێرڤەرەکان بلۆک دەکات ("Sign in to confirm you're not a bot"). چارەسەر: فایلی cookies.txt

1. لە Chrome، بە ئەکاونتێکی **Google ی لاوەکی** (نەک ئەکاونتە سەرەکییەکەت) بچۆ ناو youtube.com.
2. ئێکستێنشنی **"Get cookies.txt LOCALLY"** دابمەزرێنە و cookies ـی youtube.com هەناردە بکە.
3. لە Render: **Service → Environment → Secret Files → Add**، ناوەکەی بنووسە `cookies.txt` و ناوەڕۆکەکەی تێدا paste بکە → Save.
4. **Manual Deploy → Deploy latest commit**.

---

## پارە پەیداکردن لە ڕیکلام

هەموو شتێک ئامادەیە. تەنها لە **Render → Environment** ئەم گۆڕاوانە دابنێ:

### بژاردەی 1: Adsterra / Monetag (پێشنیارکراو بۆ ماڵپەڕی داونلۆد)
ئەم تۆڕانە ماڵپەڕی داونلۆدەر و subdomain ی خۆڕایی وەردەگرن و زوو پەسەند دەکەن.
1. لە <https://adsterra.com> یان <https://monetag.com> وەک **Publisher** خۆت تۆمار بکە و لینکی ماڵپەڕەکەت زیاد بکە.
2. کۆدی **Social Bar** یان **Popunder** paste بکە لە `resources/views/ads/custom-head.blade.php`.
3. کۆدی **Banner** paste بکە لە `resources/views/ads/custom-banner.blade.php`.
4. لە Render: `ADS_CUSTOM` = `true`. ئەگەر ads.txt یان دا، لە `ADS_TXT` دایبنێ.
5. push بکە بۆ GitHub (Render خۆی دووبارە deploy دەکات).

### بژاردەی 2: Google AdSense
- پێویستی بە دۆمەینی تایبەتی خۆتە (وەک `kurddownload.com`، نزیکەی 10$ ساڵانە)، `onrender.com` قبووڵ ناکرێت.
- ئاگاداربە: AdSense زۆرجار ماڵپەڕی داگرتنی YouTube ڕەتدەکاتەوە بەهۆی یاسای copyright.
- ئەگەر پەسەند کرا: `ADSENSE_CLIENT=ca-pub-xxxx`، `ADSENSE_SLOT_TOP`، `ADSENSE_SLOT_MIDDLE`، `ADSENSE_SLOT_BOTTOM`، و `ADS_TXT=google.com, pub-xxxx, DIRECT, f08c47fec0942fa0`

### بینینی ژمارەی سەردانکەران
`GA_MEASUREMENT_ID=G-XXXXXXX` لە Google Analytics.

---

## گۆڕاوە گرنگەکان (Environment)

| ناو | بنەڕەت | ڕوونکردنەوە |
|---|---|---|
| `MAX_CONCURRENT_DOWNLOADS` | 3 | ژمارەی داگرتنی هاوکات لە سێرڤەر |
| `MAX_CONCURRENT_PER_IP` | 1 | داگرتنی هاوکات بۆ هەر کەسێک |
| `MAX_DOWNLOAD_SIZE` | 2G | گەورەترین قەبارەی فایل |
| `FILE_TTL_MINUTES` | 60 | دوای چەند خولەک فایل بسڕدرێتەوە |
| `WEB_WORKERS` | 8 | ژمارەی worker ـی وێب سێرڤەر |
| `YTDLP_PROXY` | — | proxy ئەگەر IP ی سێرڤەر بلۆک کرا |
| `YTDLP_COOKIES_B64` | — | cookies.txt بە base64 (جێگرەوەی Secret File) |

## کارکردن لە کۆمپیوتەری خۆت (Laragon)

```bash
composer install
cp .env.example .env && php artisan key:generate
# yt-dlp.exe لە https://github.com/yt-dlp/yt-dlp/releases دابەزێنە و لە فۆڵدەری پرۆژەکە دایبنێ
php artisan serve
```

یان بە Docker: `docker compose up --build` و بکەرەوە `http://localhost:8000`.

## یاسایی

تەنها ئەو ناوەڕۆکانە دابگرە کە مافی خۆتن یان ڕێگەت پێدراوە. لاپەڕەکانی `/privacy` و `/terms` ئامادەن.
