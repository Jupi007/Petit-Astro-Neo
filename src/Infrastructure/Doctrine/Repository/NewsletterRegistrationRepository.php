<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\NewsletterRegistration;
use App\Domain\Exception\NewsletterRegistrationNotFoundException;
use App\Domain\Repository\NewsletterRegistrationRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\Common\BaseRepository;

/**
 * @extends BaseRepository<NewsletterRegistration>
 */
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
