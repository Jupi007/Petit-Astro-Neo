<?php

declare(strict_types=1);

namespace App\API\Request\Definition;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractDefinitionRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $title,
        // ------------------
        #[Assert\NotBlank]
        public readonly string $description,
        // ------------------
        #[Assert\NotBlank]
        public readonly string $routePath,
    ) {
    }
}
