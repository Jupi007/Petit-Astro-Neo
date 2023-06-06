<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Publication;
use App\Domain\Repository\Contract\BaseRepositoryInterface;

/** @extends BaseRepositoryInterface<Publication> */
interface PublicationRepositoryInterface extends BaseRepositoryInterface
{
}
