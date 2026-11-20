<?php

/*
 * Advertising / monetization settings.
 * Everything is controlled from environment variables so you can turn ads on
 * from your hosting dashboard without touching code.
 */
return [
    // Google AdSense publisher id, e.g. ca-pub-1234567890123456
    'adsense_client' => env('ADSENSE_CLIENT', ''),

    // AdSense ad-unit slot ids (create them in AdSense → Ads → By ad unit)
    'adsense_slot_top'    => env('ADSENSE_SLOT_TOP', ''),
    'adsense_slot_middle' => env('ADSENSE_SLOT_MIDDLE', ''),
    'adsense_slot_bottom' => env('ADSENSE_SLOT_BOTTOM', ''),

    /*
     * Any other ad network (Adsterra, Monetag, PropellerAds, ...):
     * paste their code into resources/views/ads/custom-head.blade.php or
     * resources/views/ads/custom-banner.blade.php and set ADS_CUSTOM=true.
     */
    'custom' => (bool) env('ADS_CUSTOM', false),

    // Content of /ads.txt (AdSense: "google.com, pub-XXXX, DIRECT, f08c47fec0942fa0")
    // Use \n to separate multiple lines.
    'ads_txt' => env('ADS_TXT', ''),

    // Google Analytics 4 measurement id (G-XXXXXXX) — see your visitors
    'ga_id' => env('GA_MEASUREMENT_ID', ''),
];
