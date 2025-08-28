<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\Services\Contracts;

/**
 * Defines a contract for comparing two version strings.
 * Implementations are free to normalize the input (e.g. stripping leading "v").
 */
interface VersionComparatorInterface
{
    /**
     * Compares two versions.
     *
     * @return int|null -1 if $a < $b, 0 if equal, 1 if $a > $b, null if not comparable.
     */
    public function compare(?string $a, ?string $b): ?int;

    public function isLower(?string $a, ?string $b): bool;

    public function isEqual(?string $a, ?string $b): bool;

    public function isHigher(?string $a, ?string $b): bool;
}