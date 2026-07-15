<?php

namespace Devonab\FilamentEasyFooter\Livewire;

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Devonab\FilamentEasyFooter\Services\ProjectVersionService;
use Livewire\Component;

class ProjectVersion extends Component
{
    public bool $showLogo = false;

    public bool $showUrl = false;

    public bool $showLatest = false;

    public bool $showUpdatable = false;

    public ?string $installed = null;

    public ?string $latest = null;

    public bool $updatable = false;

    public ?string $repository = null;

    public function mount(
        ProjectVersionService $projectVersion,
        GitHubService $github,
        bool $showLogo = false,
        bool $showUrl = false,
        bool $showLatest = false,
        bool $showUpdatable = false,
    ): void {
        $this->showLatest = $showLatest;
        $this->showUpdatable = $showUpdatable;

        $info = $projectVersion->getUpdateInfo(
            fetchLatest: $github->isEnabled() && ($showLatest || $showUpdatable),
        );

        $this->installed = $info->installed;
        $this->latest = $info->latest;
        $this->updatable = $info->updatable;

        if ($github->isEnabled()) {
            $this->showLogo = $showLogo && $github->shouldShowLogo();
            $this->showUrl = $showUrl && $github->shouldShowUrl();
            $this->repository = config('filament-easy-footer.github.repository');
        }
    }

    public function getGithubUrl(): string
    {
        return "https://github.com/{$this->repository}";
    }

    public function render()
    {
        return view('filament-easy-footer::project-version');
    }
}
