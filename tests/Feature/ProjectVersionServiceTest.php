<?php

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Devonab\FilamentEasyFooter\Services\ProjectVersionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    Config::set('filament-easy-footer.github.repository', 'devonab/filament-easy-footer');
});

it('does not contact GitHub when fetchLatest is false', function () {
    Http::fake();

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService($github, installedVersionResolver: fn () => '1.0.0');

    $info = $service->getUpdateInfo(fetchLatest: false);

    expect($info->installed)->toBe('v1.0.0')
        ->and($info->latest)->toBeNull()
        ->and($info->updatable)->toBeFalse();

    Http::assertNothingSent();
});

it('flags updatable when installed is lower than the latest release', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response(['tag_name' => 'v2.0.0'], 200),
    ]);

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService($github, installedVersionResolver: fn () => '1.0.0');

    $info = $service->getUpdateInfo();

    expect($info->installed)->toBe('v1.0.0')
        ->and($info->latest)->toBe('v2.0.0')
        ->and($info->updatable)->toBeTrue();
});

it('is not updatable when installed matches the latest release', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response(['tag_name' => 'v2.0.0'], 200),
    ]);

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService($github, installedVersionResolver: fn () => 'v2.0.0');

    $info = $service->getUpdateInfo();

    expect($info->updatable)->toBeFalse();
});

it('never flags a dev branch as updatable', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response(['tag_name' => 'v9.9.9'], 200),
    ]);

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService($github, installedVersionResolver: fn () => 'dev-main');

    $info = $service->getUpdateInfo();

    expect($info->installed)->toBe('dev-main')
        ->and($info->updatable)->toBeFalse();
});

it('falls back to a config key when the resolver returns nothing', function () {
    Config::set('app.version', '3.2.1');

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService(
        $github,
        localFallbackConfigKey: 'app.version',
        installedVersionResolver: fn () => null,
    );

    $info = $service->getUpdateInfo(fetchLatest: false);

    expect($info->installed)->toBe('v3.2.1');
});

it('treats "0.0" and empty versions as unknown', function () {
    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService($github, installedVersionResolver: fn () => '0.0');

    $info = $service->getUpdateInfo(fetchLatest: false);

    expect($info->installed)->toBeNull();
});

it('treats Composer\'s "no-version-set" placeholder as unknown, not as an outdated version', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response(['tag_name' => 'v2.2.1'], 200),
    ]);

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService($github, installedVersionResolver: fn () => '1.0.0+no-version-set');

    $info = $service->getUpdateInfo();

    expect($info->installed)->toBeNull()
        ->and($info->updatable)->toBeFalse();
});

it('falls back to a config key when the resolver returns the "no-version-set" placeholder', function () {
    Config::set('app.version', '1.0.0');

    $github = app(GitHubService::class)->enable();
    $service = new ProjectVersionService(
        $github,
        localFallbackConfigKey: 'app.version',
        installedVersionResolver: fn () => '1.0.0+no-version-set',
    );

    $info = $service->getUpdateInfo(fetchLatest: false);

    expect($info->installed)->toBe('v1.0.0');
});
