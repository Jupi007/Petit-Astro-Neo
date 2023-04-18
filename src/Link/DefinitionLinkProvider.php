<?php

declare(strict_types=1);

namespace App\Link;

use App\Entity\Definition;
use App\Repository\DefinitionRepository;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkConfigurationBuilder;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkItem;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('sulu.link.provider', [
    'alias' => Definition::RESOURCE_KEY,
])]
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
            ->setEmptyText($this->translator->trans('app.admin.no_definition_selection', [], 'admin'))
            ->setIcon(Definition::RESOURCE_ICON)
            ->getLinkConfiguration();
    }

    public function preload(array $hrefs, $locale, $published = true)
    {
        if ([] === $hrefs) {
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
