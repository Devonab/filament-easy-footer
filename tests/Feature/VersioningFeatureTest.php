<?php

use Devonab\FilamentEasyFooter\DTO\DisplayOptions;
use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

beforeEach(function () {
    config()->set('filament-easy-footer.versioning', []);
    config()->set('app.version', '');
});

afterEach(function () {
    app()->forgetInstance(EasyFooterPlugin::class);
});

function renderVersionView(UpdateInfo $info, DisplayOptions $options): string
{
    $installed = $info->getInstalled() ?? (config('app.version') ? 'v' . config('app.version') : null);

    return view('filament-easy-footer::project-version', [
        'installed' => $installed,
        'latest' => $info->getLatest(),
        'updatable' => $info->updatable,
        'showLatest' => $options->showLatest,
        'showUpdatable' => $options->showUpdatable,
        'showUrl' => false,
        'repository' => null,
        'showLogo' => false,
    ])->render();
}

it('defaults to showing latest and updatable when config keys are absent', function () {
    $options = DisplayOptions::fromConfig();

    expect($options->showLatest)->toBeTrue()
        ->and($options->showUpdatable)->toBeTrue();
});

it('shows latest version when enabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_latest', true);

    $info = new UpdateInfo('1.0.0', '1.1.0', true);
    $html = renderVersionView($info, DisplayOptions::fromConfig());

    expect($html)->toContain('v1.1.0');
});

it('hides latest version when disabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_latest', false);

    $info = new UpdateInfo('1.0.0', '1.1.0', true);
    $html = renderVersionView($info, DisplayOptions::fromConfig());

    expect($html)->not->toContain('v1.1.0');
});

it('shows updatable flag when enabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_updatable_flag', true);

    $info = new UpdateInfo('1.0.0', '1.1.0', true);
    $html = renderVersionView($info, DisplayOptions::fromConfig());

    expect($html)->toContain(__('filament-easy-footer::labels.updatable'));
});

it('hides updatable flag when disabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_updatable_flag', false);

    $info = new UpdateInfo('1.0.0', '1.1.0', true);
    $html = renderVersionView($info, DisplayOptions::fromConfig());

    expect($html)->not->toContain(__('filament-easy-footer::labels.updatable'));
});

it('falls back to app.version when composer data is missing', function () {
    config()->set('app.version', '9.9.9');

    // Simulate no composer version info available
    $info = new UpdateInfo(null, null, false);
    $html = renderVersionView($info, DisplayOptions::fromConfig());

    expect($html)->toContain('v9.9.9');
});

it('can enable installed version display explicitly', function () {
    $plugin = EasyFooterPlugin::make()
        ->withShowInstalledVersion(false)
        ->withShowInstalledVersion(true);

    $property = new ReflectionProperty(EasyFooterPlugin::class, 'showInstalledVersion');
    $property->setAccessible(true);

    expect($property->getValue($plugin))->toBeTrue();
});

it('can disable installed version display explicitly', function () {
    $plugin = EasyFooterPlugin::make()
        ->withShowInstalledVersion()
        ->withShowInstalledVersion(false);

    $property = new ReflectionProperty(EasyFooterPlugin::class, 'showInstalledVersion');
    $property->setAccessible(true);

    expect($property->getValue($plugin))->toBeFalse();
});
