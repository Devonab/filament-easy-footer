<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services\Contracts;

use Devonab\FilamentEasyFooter\DTO\UpdateInfo;

interface VersionComparisonServiceInterface
{
    public function getUpdateInfo(): UpdateInfo;
}