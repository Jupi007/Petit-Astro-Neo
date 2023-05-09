<?php

declare(strict_types=1);

namespace App\Sulu\Search;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use Massive\Bundle\SearchBundle\Search\Factory;
use Sulu\Bundle\ContentBundle\Content\Application\ContentMetadataInspector\ContentMetadataInspectorInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Search\ContentSearchMetadataProvider;
use Sulu\Component\Content\Metadata\Factory\StructureMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/** @extends ContentSearchMetadataProvider<PublicationDimensionContent, Publication> */
class PublicationSearchMetadataProvider extends ContentSearchMetadataProvider
{
    public function __construct(
        ContentMetadataInspectorInterface $contentMetadataInspector,
        #[Autowire('@massive_search.factory_default')]
        Factory $searchMetadataFactory,
        #[Autowire('@sulu_page.structure.factory')]
        StructureMetadataFactoryInterface $structureFactory,
    ) {
        parent::__construct(
            $contentMetadataInspector,
            $searchMetadataFactory,
            $structureFactory,
            Publication::class,
        );
    }
}
