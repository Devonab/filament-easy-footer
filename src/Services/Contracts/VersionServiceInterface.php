<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services\Contracts;

/**
 * Defines a contract for services that provide the
 * currently installed version of the application/root package.
 */
interface VersionServiceInterface
{
    /**
     * Returns the installed version string or null if it cannot be determined.
     */
    public function getCurrentVersion(): ?string;
}