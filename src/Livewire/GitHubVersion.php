<?php

namespace Devonab\FilamentEasyFooter\Livewire;

use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Devonab\FilamentEasyFooter\Services\GitHubService;
use Livewire\Component;

class GitHubVersion extends Component
{
    public bool $showLogo;

    public bool $showUrl;

    public ?string $version = null;

    public ?string $repository = null;

    /** New fields for local control logic */
    public ?string $installed = null;
    public ?string $latest = null;
    public bool $updatable = false;

    public function mount(GitHubService $githubService, UpdateInfo $info): void
    {
        if (! $githubService->isEnabled()) {
            return;
        }

        $this->showLogo = $githubService->shouldShowLogo();
        $this->showUrl = $githubService->shouldShowUrl();
        $this->repository = config('filament-easy-footer.github.repository');

        // Use UpdateInfo instead of calling GitHubService again
        $this->installed = $info->installed;
        $this->latest    = $info->latest;
        $this->updatable = $info->updatable;

        // Keep old $version for existing blade partials (BC: shows "latest")
        $this->version = $this->latest;
    }

    public function getGithubUrl(): string
    {
        return "https://github.com/{$this->repository}";
    }

    public function render()
    {
        return view('filament-easy-footer::github-version');
    }
}
