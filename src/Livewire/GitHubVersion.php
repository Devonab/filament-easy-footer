<?php

namespace Devonab\FilamentEasyFooter\Livewire;

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Livewire\Component;

class GitHubVersion extends Component
{
    public bool $showLogo;

    public bool $showUrl;

    public ?string $version = null;

    public ?string $repository = null;

    public function mount(GitHubService $githubService): void
    {
        if (! $githubService->isEnabled()) {
            return;
        }

        $this->showLogo = $githubService->shouldShowLogo();
        $this->showUrl = $githubService->shouldShowUrl();
        $this->repository = config('filament-easy-footer.github.repository');
        $this->version = $githubService->getLatestTag($this->repository);
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
