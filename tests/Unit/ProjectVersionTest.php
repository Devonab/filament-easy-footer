<?php

declare(strict_types=1);

use Devonab\FilamentEasyFooter\DTO\DisplayOptions;
use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Devonab\FilamentEasyFooter\Livewire\ProjectVersion;
use Devonab\FilamentEasyFooter\Services\GitHubService;
use Illuminate\Support\Facades\Config;

it('mounts with update info and github data', function () {
    Config::set('filament-easy-footer.github.repository', 'devonab/repo');

    $updateInfo = new UpdateInfo('1.0.0', '1.1.0', true);
    $opts = new DisplayOptions(showLatest: true, showUpdatable: true);
    $github = (new GitHubService)->enable(showLogo: true, showUrl: false);

    $component = new ProjectVersion;
    $component->mount($updateInfo, $github, $opts);

    expect($component->installed)->toBe('v1.0.0')
        ->and($component->latest)->toBe('v1.1.0')
        ->and($component->updatable)->toBeTrue()
        ->and($component->showLatest)->toBeTrue()
        ->and($component->showUpdatable)->toBeTrue()
        ->and($component->showLogo)->toBeTrue()
        ->and($component->showUrl)->toBeFalse()
        ->and($component->repository)->toBe('devonab/repo')
        ->and($component->getGithubUrl())->toBe('https://github.com/devonab/repo');
});

it('does not expose github data when service disabled', function () {
    Config::set('filament-easy-footer.github.repository', 'devonab/repo');

    $updateInfo = new UpdateInfo('1.0.0', '1.1.0', true);
    $opts = new DisplayOptions(showLatest: true, showUpdatable: true);
    $github = new GitHubService; // disabled by default

    $component = new ProjectVersion;
    $component->mount($updateInfo, $github, $opts);

    expect($component->showLogo)->toBeFalse()
        ->and($component->showUrl)->toBeFalse()
        ->and($component->repository)->toBeNull();
});
