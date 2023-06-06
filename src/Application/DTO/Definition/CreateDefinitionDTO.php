<?php

declare(strict_types=1);

namespace App\Application\DTO\Definition;

class CreateDefinitionDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $routePath,
        public readonly string $locale,
    ) {
    }
}
