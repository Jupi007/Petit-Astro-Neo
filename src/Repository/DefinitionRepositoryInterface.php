<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Definition;
use App\Repository\Common\BaseRepositoryInterface;
use App\Repository\Common\FindLocalizedRepositoryInterface;

/**
 * @extends BaseRepositoryInterface<Definition>
 * @extends FindLocalizedRepositoryInterface<Definition>
 */
interface DefinitionRepositoryInterface extends BaseRepositoryInterface, FindLocalizedRepositoryInterface
{
}
