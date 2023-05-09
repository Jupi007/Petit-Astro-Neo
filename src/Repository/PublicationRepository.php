<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Publication;
use App\Repository\Common\BaseRepository;

/** @extends BaseRepository<Publication> */
class PublicationRepository extends BaseRepository implements PublicationRepositoryInterface
{
    protected static function getClassName(): string
    {
        return Publication::class;
    }
}
