<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Publication;
use App\Exception\PublicationNotFoundException;
use App\Repository\Doctrine\Common\BaseRepository;
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
