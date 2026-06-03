<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection details for your Meilisearch
    | instance. By default, the plugin will use the same configuration
    | as your Laravel Scout configuration if available.
    |
    */

    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),

    'key' => env('MEILISEARCH_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the plugin behaves within your Filament admin panel.
    |
    */

    'navigation' => [
        'group' => 'Meilisearch',
        'icon' => 'heroicon-o-magnifying-glass',
        'sort' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the plugin.
    |
    */

    'features' => [
        'indexes' => true,
        'documents' => true,
        'keys' => true,
        'tasks' => true,
        'dumps' => true,
        'snapshots' => true,
        'settings' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for lists.
    |
    */

    'pagination' => [
        'default_page_size' => 10,
        'page_size_options' => [10, 25, 50, 100],
    ],

];
