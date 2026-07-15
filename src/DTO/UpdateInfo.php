<?php

namespace Devonab\FilamentEasyFooter\DTO;

final class UpdateInfo
{
    public function __construct(
        public readonly ?string $installed,
        public readonly ?string $latest,
        public readonly bool $updatable,
    ) {}
}
