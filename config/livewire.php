<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Component Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root class component namespace of your application.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path where Livewire views will be stored.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | The layout view that will be used by default when rendering pages.
    |
    */

    'layout' => 'components.layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |--------------------------------------------------------------------------
    |
    | Livewire allows lazy loading components that are not immediately needed.
    |
    */

    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire handles file uploads by storing uploaded files in a temporary
    | directory before saving them. Here you can configure the disk and
    | middleware used for temporary uploads.
    |
    */

    'temporary_file_upload' => [
        'disk' => 'local',        // Selalu gunakan disk local untuk temporary upload
        'rules' => null,
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg',
            'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    |
    | This option enables or disables rendering the component before redirecting.
    |
    */

    'render_on_redirect' => false,

    /*
    |--------------------------------------------------------------------------
    | Legacy Assets
    |--------------------------------------------------------------------------
    |
    | This option enables support for legacy assets (v2) in Livewire.
    |
    */

    'legacy_model_binding' => false,

    'inject_assets' => true,

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    'inject_morph_markers' => true,

    'pagination_theme' => 'tailwind',

];
