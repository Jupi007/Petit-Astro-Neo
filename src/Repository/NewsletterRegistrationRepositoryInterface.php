<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\NewsletterRegistration;
use App\Repository\Contract\BaseRepositoryInterface;

/** @extends BaseRepositoryInterface<NewsletterRegistration> */
interface NewsletterRegistrationRepositoryInterface extends BaseRepositoryInterface
{
}
