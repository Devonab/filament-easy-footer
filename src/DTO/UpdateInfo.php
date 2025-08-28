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
    ) {
    }
}