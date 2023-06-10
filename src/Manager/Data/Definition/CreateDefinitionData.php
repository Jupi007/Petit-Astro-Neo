<?php

declare(strict_types=1);

namespace App\Manager\Data\Definition;

class CreateDefinitionData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $routePath,
        public readonly string $locale,
    ) {
    }
}
