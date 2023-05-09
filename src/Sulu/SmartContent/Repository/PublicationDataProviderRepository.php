<?php

declare(strict_types=1);

namespace App\Sulu\SmartContent\Repository;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\SmartContent\Repository\ContentDataProviderRepository;

/** @extends ContentDataProviderRepository<PublicationDimensionContent, Publication> */
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
