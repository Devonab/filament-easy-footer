<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Devonab\FilamentEasyFooter\Services\LocalVersionService;

beforeEach(function () {
    $this->composerVersion = InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name']);
});

it('returns composer version when available', function () {
    $service = new LocalVersionService(fn (): ?string => 'fallback');
    expect($service->getCurrentVersion())->toBe($this->composerVersion);
});

it('ignores empty fallback and returns composer version', function () {
    $service = new LocalVersionService(fn (): ?string => '');
    expect($service->getCurrentVersion())->toBe($this->composerVersion);
});

it('swallows exceptions from fallback and returns composer version', function () {
    $service = new LocalVersionService(function (): ?string {
        throw new RuntimeException('boom');
    });
    expect($service->getCurrentVersion())->toBe($this->composerVersion);
});

/*
 * NOTE: Testing fallback behaviour without Composer InstalledVersions
 * would require altering Composer's runtime data, which is beyond the
 * scope of these unit tests in this environment.
 */
