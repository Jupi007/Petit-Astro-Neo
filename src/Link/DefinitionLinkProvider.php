<?php

declare(strict_types=1);

namespace App\Link;

use App\Entity\Definition;
use App\Repository\DefinitionRepository;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkConfigurationBuilder;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkItem;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefinitionLinkProvider implements LinkProviderInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly DefinitionRepository $definitionRepository,
    ) {
    }

    public function getConfiguration()
    {
        return LinkConfigurationBuilder::create()
            ->setTitle($this->translator->trans('app.admin.definition', [], 'admin'))
            ->setResourceKey(Definition::RESOURCE_KEY)
            ->setListAdapter('table')
            ->setDisplayProperties(['title'])
            ->setOverlayTitle($this->translator->trans('app.admin.single_definition_selection_overlay_title', [], 'admin'))
            ->setEmptyText($this->translator->trans('sulu_page.no_page_selected', [], 'admin'))
            ->setIcon('fa-book')
            ->getLinkConfiguration();
    }

    public function preload(array $hrefs, $locale, $published = true)
    {
        if (0 === \count($hrefs)) {
            return [];
        }

        $definitions = $this->definitionRepository->findAll();

        return \array_map(
            function (Definition $definition) use ($locale) {
                $definition->setlocale($locale);

                return new LinkItem(
                    id: (string) $definition->getId(),
                    title: $definition->getTitle() ?? '',
                    url: $definition->getRoute()?->getPath() ?? '',
                    published: true,
                );
            },
            $definitions,
        );
    }
}
