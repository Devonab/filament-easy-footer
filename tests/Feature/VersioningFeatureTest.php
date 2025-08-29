<?php

use Devonab\FilamentEasyFooter\DTO\DisplayOptions;
use Devonab\FilamentEasyFooter\DTO\UpdateInfo;

function renderVersionView(UpdateInfo $info, DisplayOptions $opts): string {
    return view('filament-easy-footer::project-version', [
        'installed' => $info->getInstalled(),
        'latest' => $info->getLatest(),
        'updatable' => $info->updatable,
        'showLatest' => $opts->showLatest,
        'showUpdatable' => $opts->showUpdatable,
        'showLogo' => false,
        'showUrl' => false,
        'repository' => null,
    ])->render();
}

it('renders latest version when enabled', function () {
    config()->set('filament-easy-footer.versioning.show_latest', true);

    $html = renderVersionView(
        new UpdateInfo('1.0.0', '1.2.3', true),
        DisplayOptions::fromConfig()
    );

    expect($html)->toContain('v1.2.3');
});

it('hides latest version when disabled', function () {
    config()->set('filament-easy-footer.versioning.show_latest', false);

    $html = renderVersionView(
        new UpdateInfo('1.0.0', '1.2.3', true),
        DisplayOptions::fromConfig()
    );

    expect($html)->not->toContain('v1.2.3');
});

it('shows updatable badge when enabled', function () {
    config()->set('filament-easy-footer.versioning.show_updatable_flag', true);

    $html = renderVersionView(
        new UpdateInfo('1.0.0', '1.2.3', true),
        DisplayOptions::fromConfig()
    );

    expect($html)->toContain(__('filament-easy-footer::labels.updatable'));
});

it('hides updatable badge when disabled', function () {
    config()->set('filament-easy-footer.versioning.show_updatable_flag', false);

    $html = renderVersionView(
        new UpdateInfo('1.0.0', '1.2.3', true),
        DisplayOptions::fromConfig()
    );

    expect($html)->not->toContain(__('filament-easy-footer::labels.updatable'));
});

it('falls back to app.version when composer data is missing', function () {
    config()->set('app.version', '9.9.9');

    $info = new UpdateInfo('9.9.9', null, false);
    $html = renderVersionView($info, DisplayOptions::fromConfig());

    expect($html)->toContain('v9.9.9');
});

