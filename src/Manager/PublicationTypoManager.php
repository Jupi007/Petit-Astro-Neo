<?php

declare(strict_types=1);

namespace App\Manager;

use App\Repository\PublicationTypoRepository;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepository $publicationTypoRepository,
    ) {
    }

    public function create(): void
    {
        // code...
    }
}
