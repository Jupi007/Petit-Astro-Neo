<?php

declare(strict_types=1);

namespace App\DTO\PublicationTypo;

class CreatePublicationTypoDTO
{
    public function __construct(
        public readonly int $publicationId,
        public readonly string $description,
    ) {
    }
}
