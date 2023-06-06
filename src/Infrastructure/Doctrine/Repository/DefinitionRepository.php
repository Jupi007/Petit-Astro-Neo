<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Definition;
use App\Domain\Exception\DefinitionNotFoundException;
use App\Domain\Repository\DefinitionRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocalizedRepositoryTrait;

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
