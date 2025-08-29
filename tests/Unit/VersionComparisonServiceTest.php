<?php

declare(strict_types=1);

use Devonab\FilamentEasyFooter\Services\SemverVersionComparator;
use Devonab\FilamentEasyFooter\Services\VersionComparisonService;
use Devonab\FilamentEasyFooter\Services\Contracts\VersionServiceInterface;

it('reports update available when remote version is higher', function () {
    $local = new class implements VersionServiceInterface {
        public function getCurrentVersion(): ?string { return '1.0.0'; }
    };
    $remote = new class implements VersionServiceInterface {
        public function getCurrentVersion(): ?string { return '1.1.0'; }
    };
    $cmp = new SemverVersionComparator();

    $service = new VersionComparisonService($local, $remote, $cmp);
    $info = $service->getUpdateInfo();

    expect($info->installed)->toBe('1.0.0')
        ->and($info->latest)->toBe('1.1.0')
        ->and($info->updatable)->toBeTrue();
});

it('reports not updatable when versions are equal', function () {
    $local = new class implements VersionServiceInterface {
        public function getCurrentVersion(): ?string { return '1.1.0'; }
    };
    $remote = new class implements VersionServiceInterface {
        public function getCurrentVersion(): ?string { return '1.1.0'; }
    };
    $cmp = new SemverVersionComparator();

    $service = new VersionComparisonService($local, $remote, $cmp);
    $info = $service->getUpdateInfo();

    expect($info->updatable)->toBeFalse();
});

