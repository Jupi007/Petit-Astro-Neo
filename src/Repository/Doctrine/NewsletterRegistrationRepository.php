<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\NewsletterRegistration;
use App\Exception\NewsletterRegistrationNotFoundException;
use App\Repository\Doctrine\Common\BaseRepository;
use App\Repository\NewsletterRegistrationRepositoryInterface;

/** @extends BaseRepository<NewsletterRegistration> */
class NewsletterRegistrationRepository extends BaseRepository implements NewsletterRegistrationRepositoryInterface
{
    protected static function getClassName(): string
    {
        return NewsletterRegistration::class;
    }

    public function throwNotFoundException(array $criteria): never
    {
        throw new NewsletterRegistrationNotFoundException();
    }
}
