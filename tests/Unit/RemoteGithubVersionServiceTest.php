<?php

declare(strict_types=1);

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Devonab\FilamentEasyFooter\Services\RemoteGithubVersionService;

it('delegates fetching to GitHubService with given repo', function () {
    $github = new class('v1.2.3') extends GitHubService
    {
        public ?string $lastRepo = null;

        public function __construct(private string $tag) {}

        public function getLatestTag(?string $repository = null): string
        {
            $this->lastRepo = $repository;

            return $this->tag;
        }
    };

    $service = new RemoteGithubVersionService($github, 'devonab/repo');

    expect($service->getCurrentVersion())->toBe('v1.2.3')
        ->and($github->lastRepo)->toBe('devonab/repo');
});

it('passes null repository when none provided', function () {
    $github = new class('v2.0.0') extends GitHubService
    {
        public ?string $lastRepo = null;

        public function __construct(private string $tag) {}

        public function getLatestTag(?string $repository = null): string
        {
            $this->lastRepo = $repository;

            return $this->tag;
        }
    };

    $service = new RemoteGithubVersionService($github);

    expect($service->getCurrentVersion())->toBe('v2.0.0')
        ->and($github->lastRepo)->toBeNull();
});
