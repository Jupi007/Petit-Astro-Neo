<?php

declare(strict_types=1);

namespace App\Sulu\Search;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentMetadataInspector\ContentMetadataInspectorInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\ContentResolverInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Search\ContentReindexProvider;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/** @extends ContentReindexProvider<PublicationDimensionContent, Publication> */
#[AutoconfigureTag('massive_search.reindex.provider', [
    'id' => Publication::RESOURCE_KEY,
])]
class PublicationReindexProvider extends ContentReindexProvider
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ContentMetadataInspectorInterface $contentMetadataInspector,
        ContentResolverInterface $contentResolver,
        string $suluContext,
    ) {
        parent::__construct(
            $entityManager,
            $contentMetadataInspector,
            $contentResolver,
            $suluContext,
            Publication::class,
        );
    }
}
