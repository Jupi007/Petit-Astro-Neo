<?php

declare(strict_types=1);

namespace App\DTO\Definition;

class UpdateDefinitionDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $routePath,
        public readonly string $locale,
    ) {
    }
}
