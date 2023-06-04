<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Publication;
use App\Repository\Contract\BaseRepositoryInterface;

/** @extends BaseRepositoryInterface<Publication> */
interface PublicationRepositoryInterface extends BaseRepositoryInterface
{
}
