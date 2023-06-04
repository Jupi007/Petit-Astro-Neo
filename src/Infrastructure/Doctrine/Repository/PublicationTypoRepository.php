<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Entity\PublicationTypo;
use App\Exception\PublicationTypoNotFoundException;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;
use App\Repository\PublicationTypoRepositoryInterface;

/** @extends BaseRepository<PublicationTypo> */
class PublicationTypoRepository extends BaseRepository implements PublicationTypoRepositoryInterface
{
    protected static function getClassName(): string
    {
        return PublicationTypo::class;
    }

    public function throwNotFoundException(array $criteria): never
    {
        throw new PublicationTypoNotFoundException();
    }
}
