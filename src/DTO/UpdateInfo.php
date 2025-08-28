<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\DTO;

/**
 * Data transfer object holding version information
 * for the view or consumer.
 */
final class UpdateInfo
{
    public function __construct(
        public readonly ?string $installed,
        public readonly ?string $latest,
        public readonly bool $updatable
    ) {}

    public function displayInstalled(): ?string
    {
        return $this->normalize($this->installed);
    }

    public function displayLatest(): ?string
    {
        return $this->normalize($this->latest);
    }

    private function normalize(?string $v): ?string
    {
        if (! $v) {
            return null;
        }

        return str_starts_with($v, 'v') ? $v : 'v' . $v;
    }
}
