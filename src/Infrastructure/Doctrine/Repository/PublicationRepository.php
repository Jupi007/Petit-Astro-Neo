<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Entity\Publication;
use App\Exception\PublicationNotFoundException;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;
use App\Repository\PublicationRepositoryInterface;

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
