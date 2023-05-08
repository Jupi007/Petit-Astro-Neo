<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Definition;
use App\Repository\Utils\BaseRepository;
use App\Repository\Utils\FindLocalizedRepositoryTrait;

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
