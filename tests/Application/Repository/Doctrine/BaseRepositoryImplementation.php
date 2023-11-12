<?php

declare(strict_types=1);

namespace App\Tests\Application\Repository\Doctrine;

use App\Repository\Doctrine\Common\BaseRepository;
use App\Tests\Application\Entity\TestEntity;
use App\Tests\Application\Exception\NotFoundEntityException;

/** @extends BaseRepository<TestEntity> */
class BaseRepositoryImplementation extends BaseRepository
{
    protected static function getClassName(): string
    {
        return TestEntity::class;
    }

    public function throwNotFoundException(array $criteria): never
    {
        throw new NotFoundEntityException();
    }
}
