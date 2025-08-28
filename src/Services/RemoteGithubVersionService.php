<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services;

use Devonab\FilamentEasyFooter\Services\Contracts\VersionServiceInterface;

class RemoteGithubVersionService implements VersionServiceInterface
{
    public function __construct(private readonly GitHubService $github, private readonly string $repo)
    {
    }

    public function getCurrentVersion(): ?string
    {
        return $this->github->getLatestTag($this->repo);
    }
}