<?php

declare(strict_types=1);

namespace App\Tests\Application\Repository\Doctrine;

use App\Repository\Doctrine\Common\BaseRepository;
use App\Repository\Doctrine\Common\LocalizedRepositoryTrait;
use App\Tests\Application\Entity\LocalizedTestEntity;
use App\Tests\Application\Exception\NotFoundEntityException;

/** @extends BaseRepository<LocalizedTestEntity> */
class LocalizedRepositoryTraitImplementation extends BaseRepository
{
    /** @use LocalizedRepositoryTrait<LocalizedTestEntity> */
    use LocalizedRepositoryTrait;

    protected static function getClassName(): string
    {
        return LocalizedTestEntity::class;
    }

    public function throwNotFoundException(array $criteria): never
    {
        throw new NotFoundEntityException();
    }
}
