{{-- Usage: @include('ads.slot', ['slot' => config('ads.adsense_slot_top')]) --}}
@if(config('ads.adsense_client') && !empty($slot))
    <div class="ad-slot">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ config('ads.adsense_client') }}"
             data-ad-slot="{{ $slot }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
@elseif(config('ads.custom') && !empty($custom))
    <div class="ad-slot">@include('ads.custom-banner')</div>
@endif
