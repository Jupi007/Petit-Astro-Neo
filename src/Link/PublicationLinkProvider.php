<?php

declare(strict_types=1);

namespace App\Link;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkConfigurationBuilder;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkItem;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicationLinkProvider implements LinkProviderInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PublicationRepository $publicationRepository,
    ) {
    }

    public function getConfiguration()
    {
        return LinkConfigurationBuilder::create()
            ->setTitle($this->translator->trans('app.admin.publication', [], 'admin'))
            ->setResourceKey(Publication::RESOURCE_KEY)
            ->setListAdapter('table')
            ->setDisplayProperties(['title'])
            ->setOverlayTitle($this->translator->trans('app.admin.single_publication_selection_overlay_title', [], 'admin'))
            ->setEmptyText($this->translator->trans('sulu_page.no_page_selected', [], 'admin'))
            ->setIcon(Publication::RESOURCE_ICON)
            ->getLinkConfiguration();
    }

    public function preload(array $hrefs, $locale, $published = true)
    {
        if (0 === \count($hrefs)) {
            return [];
        }

        $publications = $this->publicationRepository->findAll();

        return \array_map(
            function (Publication $publication) use ($locale) {
                $publication->setlocale($locale);

                return new LinkItem(
                    id: (string) $publication->getId(),
                    title: $publication->getTitle() ?? '',
                    url: $publication->getRoute()?->getPath() ?? '',
                    published: true,
                );
            },
            $publications,
        );
    }
}
