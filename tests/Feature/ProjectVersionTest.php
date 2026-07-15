<?php

use Devonab\FilamentEasyFooter\Livewire\ProjectVersion;
use Devonab\FilamentEasyFooter\Services\GitHubService;
use Devonab\FilamentEasyFooter\Services\ProjectVersionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    Config::set('filament-easy-footer.github.repository', 'devonab/filament-easy-footer');
});

it('shows only the installed version when GitHub is not enabled', function () {
    Http::fake();

    $github = app(GitHubService::class); // disabled by default
    $component = new ProjectVersion;
    $component->mount(app(ProjectVersionService::class), $github, showLogo: true, showUrl: true, showLatest: true, showUpdatable: true);

    expect($component->installed)->not->toBeNull()
        ->and($component->latest)->toBeNull()
        ->and($component->updatable)->toBeFalse()
        ->and($component->showLogo)->toBeFalse()
        ->and($component->showUrl)->toBeFalse()
        ->and($component->repository)->toBeNull();

    Http::assertNothingSent();
});

it('does not call GitHub when neither showLatest nor showUpdatable is enabled', function () {
    Http::fake();

    $github = app(GitHubService::class)->enable();
    $component = new ProjectVersion;
    $component->mount(app(ProjectVersionService::class), $github, showLatest: false, showUpdatable: false);

    expect($component->latest)->toBeNull();

    Http::assertNothingSent();
});

it('exposes latest version and github link data when enabled together with GitHub', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response(['tag_name' => 'v99.0.0'], 200),
    ]);

    $github = app(GitHubService::class)->enable(showLogo: true, showUrl: true);
    $component = new ProjectVersion;
    $component->mount(app(ProjectVersionService::class), $github, showLogo: true, showUrl: true, showLatest: true, showUpdatable: true);

    expect($component->latest)->toBe('v99.0.0')
        ->and($component->showLogo)->toBeTrue()
        ->and($component->showUrl)->toBeTrue()
        ->and($component->repository)->toBe('devonab/filament-easy-footer')
        ->and($component->getGithubUrl())->toBe('https://github.com/devonab/filament-easy-footer');
});

it('renders the installed version and hides the update badge when showUpdatable is disabled', function () {
    $html = view('filament-easy-footer::project-version', [
        'installed' => 'v1.0.0',
        'latest' => 'v2.0.0',
        'updatable' => true,
        'showLatest' => true,
        'showUpdatable' => false,
        'showLogo' => false,
        'showUrl' => false,
        'repository' => null,
    ])->render();

    expect($html)->toContain('v1.0.0')
        ->toContain('v2.0.0')
        ->not->toContain(__('filament-easy-footer::labels.updatable'));
});

it('renders the update badge when updatable and showUpdatable are both true', function () {
    $html = view('filament-easy-footer::project-version', [
        'installed' => 'v1.0.0',
        'latest' => 'v2.0.0',
        'updatable' => true,
        'showLatest' => true,
        'showUpdatable' => true,
        'showLogo' => false,
        'showUrl' => false,
        'repository' => null,
    ])->render();

    expect($html)->toContain(__('filament-easy-footer::labels.updatable'));
});
