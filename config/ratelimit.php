<?php

// Requests per minute, per visitor IP
return [
    'analyze'  => (int) env('RATE_LIMIT_ANALYZE', 15),
    'download' => (int) env('RATE_LIMIT_DOWNLOAD', 8),
    'status'   => (int) env('RATE_LIMIT_STATUS', 120),
];
