<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\PublicationTypo;
use App\Domain\Repository\Contract\BaseRepositoryInterface;

/** @extends BaseRepositoryInterface<PublicationTypo> */
interface PublicationTypoRepositoryInterface extends BaseRepositoryInterface
{
}
