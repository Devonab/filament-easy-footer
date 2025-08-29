<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services;

use Devonab\FilamentEasyFooter\Services\Contracts\VersionComparatorInterface;

/**
 * Compares version strings with pragmatic rules:
 * - "dev-*" branches are treated as HIGHER than tagged releases (ahead of stable).
 * - two "dev-*" versions are considered equal.
 * - leading "v"/"V" is stripped before comparing.
 * - "0.0" or empty values are treated as not comparable.
 */
final class SemverVersionComparator implements VersionComparatorInterface
{
    public function compare(?string $a, ?string $b): ?int
    {
        if (! $a || ! $b) {
            return null;
        }

        $na = $this->normalize($a);
        $nb = $this->normalize($b);

        // treat "0.0" as not comparable (often a placeholder/fallback)
        if ($this->isZeroPlaceholder($na) || $this->isZeroPlaceholder($nb)) {
            return null;
        }

        $aDev = $this->isDev($na);
        $bDev = $this->isDev($nb);

        // dev-branch handling
        switch (true) {
            case $aDev && $bDev:
                // dev vs dev → considered equal
                return 0;

            case $aDev && ! $bDev:
                // dev vs release → dev is considered ahead
                return 1;

            case ! $aDev && $bDev:
                // release vs dev → release is considered behind
                return -1;
        }

        // both non-dev: fall back to semantic comparison
        return version_compare($na, $nb);
    }

    public function isLower(?string $a, ?string $b): bool
    {
        $cmp = $this->compare($a, $b);

        return $cmp !== null && $cmp < 0;
    }

    public function isEqual(?string $a, ?string $b): bool
    {
        $cmp = $this->compare($a, $b);

        return $cmp === 0;
    }

    public function isHigher(?string $a, ?string $b): bool
    {
        $cmp = $this->compare($a, $b);

        return $cmp !== null && $cmp > 0;
    }

    /**
     * Removes leading "v" or "V" and trims whitespace.
     */
    private function normalize(string $version): string
    {
        $v = trim($version);
        if ($v !== '' && ($v[0] === 'v' || $v[0] === 'V')) {
            $v = substr($v, 1);
        }

        return $v;
    }

    /**
     * Returns true if the version represents a dev branch/reference (e.g. "dev-develop").
     */
    private function isDev(string $v): bool
    {
        return str_starts_with($v, 'dev-');
    }

    /**
     * Treats "0.0" (common placeholder) as not comparable.
     */
    private function isZeroPlaceholder(string $v): bool
    {
        return $v === '0.0';
    }
}
