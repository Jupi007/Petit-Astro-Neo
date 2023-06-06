<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Teaser;

use App\Domain\Entity\Publication;
use App\Domain\Entity\PublicationDimensionContent;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentMetadataInspector\ContentMetadataInspectorInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Teaser\ContentTeaserProvider;
use Sulu\Bundle\PageBundle\Teaser\Configuration\TeaserConfiguration;
use Sulu\Component\Content\Metadata\Factory\StructureMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

/** @extends ContentTeaserProvider<PublicationDimensionContent, Publication> */
#[AutoconfigureTag('sulu.teaser.provider', [
    'alias' => Publication::RESOURCE_KEY,
])]
class PublicationTeaserProvider extends ContentTeaserProvider
{
    public function __construct(
        ContentManagerInterface $contentManager,
        EntityManagerInterface $entityManager,
        ContentMetadataInspectorInterface $contentMetadataInspector,
        #[Autowire('@sulu_page.structure.factory')]
        StructureMetadataFactoryInterface $metadataFactory,
        private readonly TranslatorInterface $translator,
        bool $suluDocumentShowDrafts,
    ) {
        parent::__construct(
            $contentManager,
            $entityManager,
            $contentMetadataInspector,
            $metadataFactory,
            Publication::class,
            $suluDocumentShowDrafts,
        );
    }

    public function getConfiguration(): TeaserConfiguration
    {
        return new TeaserConfiguration(
            $this->translator->trans('app.admin.publication', [], 'admin'),
            $this->getResourceKey(),
            'table',
            ['title'],
            $this->translator->trans('app.admin.publication_selection_overlay_title', [], 'admin'),
        );
    }
}
