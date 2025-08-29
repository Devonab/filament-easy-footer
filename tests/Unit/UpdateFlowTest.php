<?php

declare(strict_types=1);

use Devonab\FilamentEasyFooter\Services\Contracts\VersionServiceInterface;
use Devonab\FilamentEasyFooter\Services\SemverVersionComparator;

it('computes updatable correctly from installed and latest', function () {
    $local = new class implements VersionServiceInterface {
        public function getCurrentVersion(): ?string { return 'v1.2.3'; }
    };
    $cmp = new SemverVersionComparator;

    $installed = $local->getCurrentVersion();
    $latest = '1.3.0';

    $updatable = $cmp->isLower($installed, $latest);

    expect($installed)->toBe('v1.2.3')
        ->and($updatable)->toBeTrue(); // 1.2.3 < 1.3.0
});

it('reports not updatable when installed equals latest', function () {
    $local = new class implements VersionServiceInterface {
        public function getCurrentVersion(): ?string { return '1.2.3'; }
    };
    $cmp = new SemverVersionComparator;

    $installed = $local->getCurrentVersion();
    $latest = 'v1.2.3';

    $updatable = $cmp->isLower($installed, $latest);

    expect($updatable)->toBeFalse();
});
