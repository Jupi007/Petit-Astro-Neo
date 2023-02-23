<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\PublicationTypo;
use App\Repository\PublicationTypoRepository;

class PublicationTypoManager
{
    public function __construct(
        private readonly PublicationTypoRepository $repository,
    ) {
    }

    public function create(PublicationTypo $typo): void
    {
        $this->repository->save($typo);
    }

    public function remove(PublicationTypo $typo): void
    {
        $this->repository->remove($typo);
    }
}
