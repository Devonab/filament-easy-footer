<?php

return [
    'app_name' => null,
    'github' => [
        'repository' => null,
        'token' => null,
        'cache_ttl' => 3600,
    ],

    'versioning' => [
        // It's the config key to read the installed version from if Composer\InstalledVersions
        // cannot resolve it (e.g. 'app.version'). You can leave null to disable the fallback.
        'local_fallback_config_key' => null,
    ],
];
