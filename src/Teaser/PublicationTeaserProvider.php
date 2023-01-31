<?php

declare(strict_types=1);

namespace App\Teaser;

use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentMetadataInspector\ContentMetadataInspectorInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Teaser\ContentTeaserProvider;
use Sulu\Bundle\PageBundle\Teaser\Configuration\TeaserConfiguration;
use Sulu\Component\Content\Metadata\Factory\StructureMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        private TranslatorInterface $translator,
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

    protected function getDescription(DimensionContentInterface $dimensionContent, array $data): ?string
    {
        $article = \strip_tags($data['article'] ?? '');

        return $article ?: parent::getDescription($dimensionContent, $data);
    }
}
