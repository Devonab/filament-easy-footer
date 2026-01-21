<?php

declare(strict_types=1);

namespace Devonab\FilamentEasyFooter\DTO;

final class DisplayOptions
{
    public function __construct(
        public readonly bool $showLatest,
        public readonly bool $showUpdatable,
    ) {}

    public static function fromConfig(): self
    {
        $versioning = (array) config('filament-easy-footer.versioning', []);

        return new self(
            showLatest: (bool) ($versioning['show_latest'] ?? true),
            showUpdatable: (bool) ($versioning['show_updatable_flag'] ?? true),
        );
    }
}
