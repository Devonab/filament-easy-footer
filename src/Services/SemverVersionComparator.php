<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services;

use Devonab\FilamentEasyFooter\Services\Contracts\VersionComparatorInterface;

/**
 * Compares version strings using PHP's built-in version_compare.
 * Normalizes leading "v"/"V" characters before comparison.
 */
final class SemverVersionComparator implements VersionComparatorInterface
{
    public function compare(?string $a, ?string $b): ?int
    {
        if (!$a || !$b) {
            return null;
        }

        $na = $this->normalize($a);
        $nb = $this->normalize($b);

        // Returns -1, 0, or 1
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
     * Removes leading "v" or "V" from a version string.
     */
    private function normalize(string $version): string
    {
        $v = trim($version);
        if ($v !== '' && ($v[0] === 'v' || $v[0] === 'V')) {
            $v = substr($v, 1);
        }
        return $v;
    }
}