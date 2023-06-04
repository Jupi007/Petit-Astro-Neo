<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Entity\Definition;
use App\Exception\DefinitionNotFoundException;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocalizedRepositoryTrait;
use App\Repository\DefinitionRepositoryInterface;

/** @extends BaseRepository<Definition> */
class DefinitionRepository extends BaseRepository implements DefinitionRepositoryInterface
{
    /** @template-use LocalizedRepositoryTrait<Definition> */
    use LocalizedRepositoryTrait;

    protected static function getClassName(): string
    {
        return Definition::class;
    }

    public function throwNotFoundException(array $criteria): never
    {
        throw new DefinitionNotFoundException();
    }
}
