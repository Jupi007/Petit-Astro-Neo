<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Entity\Definition;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;
use App\Infrastructure\Doctrine\Repository\Common\FindLocalizedRepositoryTrait;
use App\Repository\DefinitionRepositoryInterface;

/** @extends BaseRepository<Definition> */
class DefinitionRepository extends BaseRepository implements DefinitionRepositoryInterface
{
    /** @template-use FindLocalizedRepositoryTrait<Definition> */
    use FindLocalizedRepositoryTrait;

    protected static function getClassName(): string
    {
        return Definition::class;
    }
}
