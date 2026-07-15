<span class="flex items-center gap-2">
    @if($installed)
        <span>{{ __('filament-easy-footer::labels.installed') }}: {{ $installed }}</span>
    @endif

    @if($showUpdatable && $updatable && $latest)
        <span class="inline-flex items-center rounded px-2 py-0.5 text-xs bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
            {{ $latest }} {{ __('filament-easy-footer::labels.updatable') }}
        </span>
    @elseif($showLatest && $latest)
        <span>{{ __('filament-easy-footer::labels.latest') }}: {{ $latest }}</span>
    @endif

    @include('filament-easy-footer::partials.github-link', [
        'githubUrl' => $repository ? $this->getGithubUrl() : null,
        'showUrl' => $showUrl,
        'showLogo' => $showLogo,
        'repository' => $repository,
    ])
</span>
