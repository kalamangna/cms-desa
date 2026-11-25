<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Layanan Tanda Tangan Elektronik BSrE BSSN
    |--------------------------------------------------------------------------
    |
    | Kredensial API e-Sign BSrE disimpan melalui variabel lingkungan .env.
    | Aktifkan BSRE_MOCK hanya untuk pengujian lokal tanpa server BSrE nyata.
    |
    */
    'base_url' => env('BSRE_BASE_URL', 'https://tte.sinjaikab.go.id/bridge'),

    'username' => env('BSRE_USERNAME', ''),

    'password' => env('BSRE_PASSWORD', ''),

    'timeout' => (int) env('BSRE_TIMEOUT', 60),

    'verify_ssl' => filter_var(env('BSRE_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),

    'mock' => filter_var(env('BSRE_MOCK', false), FILTER_VALIDATE_BOOLEAN),

    'tag_coordinate' => env('BSRE_TAG_COORDINATE', '#'),

    'x_axis' => (int) env('BSRE_X_AXIS', 350),

    'y_axis' => (int) env('BSRE_Y_AXIS', 180),

    'width' => (int) env('BSRE_WIDTH', 85),

    'height' => (int) env('BSRE_HEIGHT', 85),
];
