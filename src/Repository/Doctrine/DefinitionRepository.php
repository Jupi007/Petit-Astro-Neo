<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Definition;
use App\Exception\DefinitionNotFoundException;
use App\Repository\DefinitionRepositoryInterface;
use App\Repository\Doctrine\Common\BaseRepository;
use App\Repository\Doctrine\Common\LocalizedRepositoryTrait;

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
