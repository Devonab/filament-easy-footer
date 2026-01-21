<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services;

use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Devonab\FilamentEasyFooter\Services\Contracts\VersionComparatorInterface;
use Devonab\FilamentEasyFooter\Services\Contracts\VersionComparisonServiceInterface;
use Devonab\FilamentEasyFooter\Services\Contracts\VersionServiceInterface;

/**
 * Orchestrates local and remote version services
 * and compares them via a comparator.
 */
final class VersionComparisonService implements VersionComparisonServiceInterface
{
    public function __construct(
        private readonly VersionServiceInterface $localVersionService,
        private readonly VersionServiceInterface $remoteVersionService,
        private readonly VersionComparatorInterface $comparator
    ) {}

    public function getUpdateInfo(): UpdateInfo
    {
        $installed = $this->localVersionService->getCurrentVersion();
        $latest = $this->remoteVersionService->getCurrentVersion();

        $updatable = $this->comparator->isLower($installed, $latest);

        return new UpdateInfo($installed, $latest, $updatable);
    }
}
