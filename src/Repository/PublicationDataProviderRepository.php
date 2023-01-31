<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\SmartContent\Repository\ContentDataProviderRepository;

class PublicationDataProviderRepository extends ContentDataProviderRepository
{
    public function __construct(
        ContentManagerInterface $contentManager,
        EntityManagerInterface $entityManager,
        bool $suluDocumentShowDrafts,
    ) {
        parent::__construct(
            $contentManager,
            $entityManager,
            $suluDocumentShowDrafts,
            Publication::class,
        );
    }
}
