<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContactRequest;
use App\Repository\Utils\BaseRepository;

/** @extends BaseRepository<ContactRequest> */
class ContactRequestRepository extends BaseRepository implements ContactRequestRepositoryInterface
{
    protected static function getClassName(): string
    {
        return ContactRequest::class;
    }
}
