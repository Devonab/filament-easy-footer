<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Livewire;

use Devonab\FilamentEasyFooter\DTO\DisplayOptions;
use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Devonab\FilamentEasyFooter\Services\GitHubService;
use Livewire\Component;

class ProjectVersion extends Component
{
    // version info (scalars for Livewire)
    public ?string $installed = null;

    public ?string $latest = null;

    public bool $updatable = false;

    // display options (scalars!)
    public bool $showLatest = true;
    public bool $showUpdatable = true;

    // github link/visuals (layout like before)
    public bool $showLogo = false;

    public bool $showUrl = false;

    public ?string $repository = null;

    public function mount(UpdateInfo $updateInfo, GitHubService $github, DisplayOptions $opts): void
    {
        $this->installed = $updateInfo->getInstalled();
        $this->latest = $updateInfo->getLatest();
        $this->updatable = $updateInfo->updatable;

        $this->showLatest    = $opts->showLatest;
        $this->showUpdatable = $opts->showUpdatable;

        // only use GitHubService for layout bits (no version call)
        if ($github->isEnabled()) {
            $this->showLogo = $github->shouldShowLogo();
            $this->showUrl = $github->shouldShowUrl();
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
