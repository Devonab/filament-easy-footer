<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services;

use Composer\InstalledVersions;
use Devonab\FilamentEasyFooter\Services\Contracts\VersionServiceInterface;

/**
 * Provides the locally installed version of the application.
 * Uses Composer\InstalledVersions as the primary source.
 * Optionally accepts a fallback callback (e.g. config('app.version')).
 */
final class LocalVersionService implements VersionServiceInterface
{
    /** @var callable|null */
    private $fallback;

    /**
     * @param  callable():(?string)|null  $fallback
     *                                               Optional fallback if Composer does not return a version.
     */
    public function __construct(?callable $fallback = null)
    {
        $this->fallback = $fallback;
    }

    public function getCurrentVersion(): ?string
    {
        try {
            $root = InstalledVersions::getRootPackage();
            $name = $root['name'] ?? null;

            if ($name !== '') {
                $version = InstalledVersions::getPrettyVersion($name);
                if ($version !== '') {
                    return $version;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if ($this->fallback !== null) {
            $version = ($this->fallback)();

            return is_string($version) && $version !== '' ? $version : null;
        }

        return null;
    }
}
