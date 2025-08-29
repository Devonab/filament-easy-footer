<?php

return [
    // Optional: override the displayed app name in the footer
    'app_name' => null,

    'versioning' => [
        // toggle whether to fetch/display the latest GitHub version
        'show_latest' => true,

        // whether to compute a boolean "updatable"
        'show_updatable_flag' => true,

        // how to resolve the local version if Composer fails
        'local_fallback' => 'config', // null | 'config'
    ],
    'github' => [
        'repository' => null,
        'token' => null,
        'cache_ttl' => 3600,
    ],
];
