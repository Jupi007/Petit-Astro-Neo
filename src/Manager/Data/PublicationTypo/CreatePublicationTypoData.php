<?php

declare(strict_types=1);

namespace App\Manager\Data\PublicationTypo;

class CreatePublicationTypoData
{
    public function __construct(
        public readonly int $publicationId,
        public readonly string $description,
    ) {
    }
}
