<?php

return [
    // Optional: override the displayed app name in the footer
    'app_name' => null,

    'versioning' => [
        // toggle whether to show installed version from composer
        'show_installed' => true,

        // toggle whether to fetch/display the latest GitHub version
        'show_latest' => true,

        // whether to compute a boolean "updatable"
        'show_updatable_flag' => true,

        // how to resolve the local version if Composer fails
        'local_fallback' => 'config', // null | 'config' | 'env'

        // keys for fallbacks
        'local_config_key' => 'app.version',
        'local_env_key'    => 'APP_VERSION',

        // optional labels for your view
        'labels' => [
            'installed' => 'Installed',
            'latest'    => 'Latest',
            'updatable' => 'Update available',
            'unknown'   => 'N/A',
        ],
    ],
    'github' => [
        'repository' => null,
        'token' => null,
        'cache_ttl' => 3600,
    ],
];
