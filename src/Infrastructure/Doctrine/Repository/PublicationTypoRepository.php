<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\PublicationTypo;
use App\Domain\Exception\PublicationTypoNotFoundException;
use App\Domain\Repository\PublicationTypoRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;

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
