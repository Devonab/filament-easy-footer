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

    public function getInstalled(): ?string
    {
        return $this->normalize($this->installed);
    }

    public function getLatest(): ?string
    {
        return $this->normalize($this->latest);
    }

    private function normalize(?string $v): ?string
    {
        if (! $v) {
            return null;
        }

        // don't touch branch refs
        if (str_starts_with($v, 'dev-')) {
            return $v;
        }

        // don't show meaningless default
        if ($v === '0.0') {
            return null;
        }

        return str_starts_with($v, 'v') ? $v : 'v' . $v;
    }
}
