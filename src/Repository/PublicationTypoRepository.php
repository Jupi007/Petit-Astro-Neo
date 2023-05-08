<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicationTypo;
use App\Repository\Utils\BaseRepository;

/** @extends BaseRepository<PublicationTypo> */
class PublicationTypoRepository extends BaseRepository implements PublicationTypoRepositoryInterface
{
    protected static function getClassName(): string
    {
        return PublicationTypo::class;
    }
}
