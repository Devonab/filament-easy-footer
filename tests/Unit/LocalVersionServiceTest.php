<?php

declare(strict_types=1);

use Devonab\FilamentEasyFooter\Services\LocalVersionService;

it('returns version from fallback when composer is not available', function () {
    // Arrange: define a deterministic fallback
    $fallback = fn (): ?string => '1.2.3';

    // Act
    $service = new LocalVersionService($fallback);
    $installed = $service->getCurrentVersion();

    // Assert
    expect($installed)->toBe('1.2.3');
});

it('returns null when fallback returns empty/invalid', function () {
    $fallback = fn (): ?string => '';
    $service = new LocalVersionService($fallback);

    expect($service->getCurrentVersion())->toBeNull();
});

it('swallows exceptions from fallback and returns null', function () {
    $fallback = function (): ?string {
        throw new RuntimeException('boom');
    };

    $service = new LocalVersionService($fallback);

    // getCurrentVersion() should not bubble up the exception
    expect($service->getCurrentVersion())->toBeNull();
});

/*
 * NOTE:
 * We intentionally do not assert the Composer-based path here,
 * because it depends on the test environment's vendor tree.
 * If you want an integration test for Composer\InstalledVersions,
 * you can add a separate test that only runs when a known root package
 * is present in the CI environment.
 */
