@extends('layouts.app')

@section('title', __('messages.terms') . ' — ' . trim(__('messages.title')))

@section('content')
<article class="prose">
    <h1>{{ __('messages.terms') }} / Terms of Use</h1>

    <h2>کوردی</h2>
    <ul>
        <li>تەنها ئەو ڤیدیۆیانە دابگرە کە مافی خۆتن، یان خاوەنەکەی ڕێگەی پێداوی.</li>
        <li>تۆ بەرپرسیاری بەکارهێنانی ئەو فایلانەیت کە دایدەگریت.</li>
        <li>خزمەتگوزارییەکە "وەک خۆی" پێشکەش دەکرێت و هیچ گەرەنتییەک نییە.</li>
    </ul>

    <h2>English</h2>
    <ul>
        <li>Only download content you own or have permission to download. Respect copyright and each platform's terms.</li>
        <li>You are responsible for how you use downloaded files.</li>
        <li>The service is provided "as is", without warranty of any kind.</li>
    </ul>
</article>
@endsection
