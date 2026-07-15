<span class="flex items-center gap-2">
    @if($version)
        <span>{{ str()->startsWith($version, 'v') ? $version : 'v' . $version }}</span>
        @include('filament-easy-footer::partials.github-link', [
            'githubUrl' => $repository ? $this->getGithubUrl() : null,
            'showUrl' => $showUrl,
            'showLogo' => $showLogo,
            'repository' => $repository,
        ])
    @endif
</span>
