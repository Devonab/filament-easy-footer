<?php

use Devonab\FilamentEasyFooter\DTO\DisplayOptions;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

it('shows latest version when enabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_latest', true);

    $options = DisplayOptions::fromConfig();

    expect($options->showLatest)->toBeTrue();
});

it('hides latest version when disabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_latest', false);

    $options = DisplayOptions::fromConfig();

    expect($options->showLatest)->toBeFalse();
});

it('shows updatable flag when enabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_updatable_flag', true);

    $options = DisplayOptions::fromConfig();

    expect($options->showUpdatable)->toBeTrue();
});

it('hides updatable flag when disabled in config', function () {
    config()->set('filament-easy-footer.versioning.show_updatable_flag', false);

    $options = DisplayOptions::fromConfig();

    expect($options->showUpdatable)->toBeFalse();
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
