<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Definition;
use App\Domain\Repository\Contract\BaseRepositoryInterface;
use App\Domain\Repository\Contract\LocalizedRepositoryInterface;

/**
 * @extends BaseRepositoryInterface<Definition>
 * @extends LocalizedRepositoryInterface<Definition>
 */
interface DefinitionRepositoryInterface extends BaseRepositoryInterface, LocalizedRepositoryInterface
{
}
