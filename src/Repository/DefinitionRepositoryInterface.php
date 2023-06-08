<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Definition;
use App\Repository\Common\BaseRepositoryInterface;
use App\Repository\Common\LocalizedRepositoryInterface;

/**
 * @extends BaseRepositoryInterface<Definition>
 * @extends LocalizedRepositoryInterface<Definition>
 */
interface DefinitionRepositoryInterface extends BaseRepositoryInterface, LocalizedRepositoryInterface
{
}
