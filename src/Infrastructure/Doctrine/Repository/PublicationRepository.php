<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Publication;
use App\Domain\Exception\PublicationNotFoundException;
use App\Domain\Repository\PublicationRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;

/** @extends BaseRepository<Publication> */
class PublicationRepository extends BaseRepository implements PublicationRepositoryInterface
{
    protected static function getClassName(): string
    {
        return Publication::class;
    }

    public function throwNotFoundException(array $criteria): never
    {
        throw new PublicationNotFoundException();
    }
}
