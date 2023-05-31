<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Entity\NewsletterRegistration;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;
use App\Repository\NewsletterRegistrationRepositoryInterface;

/**
 * @extends BaseRepository<NewsletterRegistration>
 */
class NewsletterRegistrationRepository extends BaseRepository implements NewsletterRegistrationRepositoryInterface
{
    protected static function getClassName(): string
    {
        return NewsletterRegistration::class;
    }
}
