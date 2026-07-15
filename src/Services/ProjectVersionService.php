<?php

namespace Devonab\FilamentEasyFooter\Services;

use Composer\InstalledVersions;
use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Throwable;

class ProjectVersionService
{
    /**
     * @param  (callable(): (?string))|null  $installedVersionResolver  Overrides the Composer lookup (mainly for testing or custom version sources).
     */
    public function __construct(
        protected GitHubService $github,
        protected ?string $localFallbackConfigKey = null,
        protected $installedVersionResolver = null,
    ) {}

    public function getUpdateInfo(bool $fetchLatest = true): UpdateInfo
    {
        $installed = $this->normalize($this->resolveInstalledVersion());
        $latest = $fetchLatest ? $this->normalize($this->github->getLatestTag()) : null;

        return new UpdateInfo(
            installed: $installed,
            latest: $latest,
            updatable: $this->isUpdatable($installed, $latest),
        );
    }

    protected function resolveInstalledVersion(): ?string
    {
        $version = $this->installedVersionResolver
            ? ($this->installedVersionResolver)()
            : $this->resolveFromComposer();

        if (is_string($version) && $version !== '' && ! $this->isPlaceholder($version)) {
            return $version;
        }

        return $this->localFallbackConfigKey ? config($this->localFallbackConfigKey) : null;
    }

    protected function resolveFromComposer(): ?string
    {
        try {
            $root = InstalledVersions::getRootPackage();

            return InstalledVersions::getPrettyVersion($root['name']);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * A dev branch (e.g. "dev-main") is never flagged as updatable: it has no
     * fixed position relative to tagged releases, and nagging someone who is
     * intentionally running a dev branch would be noise, not signal.
     */
    protected function isUpdatable(?string $installed, ?string $latest): bool
    {
        if (! $installed || ! $latest || str_starts_with($installed, 'dev-')) {
            return false;
        }

        return version_compare(ltrim($installed, 'vV'), ltrim($latest, 'vV'), '<');
    }

    protected function normalize(?string $version): ?string
    {
        if ($this->isPlaceholder($version)) {
            return null;
        }

        if (str_starts_with($version, 'dev-')) {
            return $version;
        }

        return str_starts_with($version, 'v') ? $version : "v{$version}";
    }

    /**
     * Composer's own placeholder when it cannot detect a version for the
     * root package (e.g. no VCS metadata available) — not a real version.
     */
    protected function isPlaceholder(?string $version): bool
    {
        return ! $version || $version === '0.0' || str_contains($version, 'no-version-set');
    }
}
