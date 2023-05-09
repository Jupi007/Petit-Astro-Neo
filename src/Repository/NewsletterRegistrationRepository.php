<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\NewsletterRegistration;
use App\Repository\Common\BaseRepository;

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
