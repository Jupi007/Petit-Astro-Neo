<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\SmartContent\Repository;

use App\Domain\Entity\Publication;
use App\Domain\Entity\PublicationDimensionContent;
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
