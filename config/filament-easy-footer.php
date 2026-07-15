<?php

return [
    'app_name' => env('APP_NAME', 'Filament Footer'),
    'github' => [
        'repository' => env('GITHUB_REPOSITORY', ''),
        'token' => env('GITHUB_TOKEN', ''),
        'cache_ttl' => env('GITHUB_CACHE_TTL', 3600),
    ],

    'versioning' => [
        // It's the config key to read the installed version from if Composer\InstalledVersions
        // cannot resolve it (e.g. 'app.version'). You can leave null to disable the fallback.
        'local_fallback_config_key' => null,
    ],
];
