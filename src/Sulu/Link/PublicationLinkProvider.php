<?php

declare(strict_types=1);

namespace App\Sulu\Link;

use App\Common\AdminTranslatorTrait;
use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Link\ContentLinkProvider;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkConfiguration;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkConfigurationBuilder;
use Sulu\Component\Content\Metadata\Factory\StructureMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

/** @extends ContentLinkProvider<PublicationDimensionContent, Publication> */
#[AutoconfigureTag('sulu.link.provider', [
    'alias' => Publication::RESOURCE_KEY,
])]
class PublicationLinkProvider extends ContentLinkProvider
{
    use AdminTranslatorTrait;

    public function __construct(
        ContentManagerInterface $contentManager,
        #[Autowire('@sulu_page.structure.factory')]
        StructureMetadataFactoryInterface $structureMetadataFactory,
        EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct($contentManager, $structureMetadataFactory, $entityManager, Publication::class);
    }

    public function getConfiguration(): LinkConfiguration
    {
        return LinkConfigurationBuilder::create()
            ->setTitle($this->trans('app.admin.publication'))
            ->setResourceKey(Publication::RESOURCE_KEY)
            ->setListAdapter('table')
            ->setDisplayProperties(['id'])
            ->setOverlayTitle($this->trans('app.admin.single_publication_selection_overlay_title'))
            ->setEmptyText($this->trans('app.admin.no_publication_selected'))
            ->setIcon(Publication::RESOURCE_ICON)
            ->getLinkConfiguration();
    }
}
