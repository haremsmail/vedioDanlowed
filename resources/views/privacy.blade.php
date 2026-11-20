@extends('layouts.app')

@section('title', __('messages.privacy') . ' — ' . trim(__('messages.title')))

@section('content')
<article class="prose">
    <h1>{{ __('messages.privacy') }} / Privacy Policy</h1>

    <h2>کورتە</h2>
    <ul>
        <li>ئێمە ئەکاونت و ناو و ئیمەیڵی تۆ کۆناکەینەوە.</li>
        <li>ئەو فایلانەی دادەگیرێن دوای کەمتر لە کاتژمێرێک بە خۆکاری دەسڕدرێنەوە.</li>
        <li>ناونیشانی IP تەنها بۆ ڕێگریکردن لە بەکارهێنانی خراپ (rate limit) بە شێوەی کاتی بەکاردێت.</li>
        <li>ئەم ماڵپەڕە لەوانەیە ڕیکلام (وەک Google AdSense) و Google Analytics بەکاربهێنێت، کە کوکی (cookies) بەکاردەهێنن.</li>
    </ul>

    <h2>Summary (English)</h2>
    <ul>
        <li>We do not collect accounts, names or e-mail addresses.</li>
        <li>Downloaded files are deleted automatically within about one hour.</li>
        <li>IP addresses are used temporarily and only for abuse protection (rate limiting).</li>
        <li>This site may show ads (e.g. Google AdSense) and use Google Analytics. These third parties use cookies to serve and measure ads. You can opt out of personalized ads at <a href="https://www.google.com/settings/ads" rel="noopener" target="_blank">google.com/settings/ads</a>.</li>
    </ul>
</article>
@endsection
