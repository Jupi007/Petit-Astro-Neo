<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\NewsletterRegistration;
use App\Domain\Repository\Contract\BaseRepositoryInterface;

/** @extends BaseRepositoryInterface<NewsletterRegistration> */
interface NewsletterRegistrationRepositoryInterface extends BaseRepositoryInterface
{
}
