<?php

declare(strict_types=1);

namespace App\Sitemap;

use App\Entity\Definition;
use App\Repository\DefinitionTranslationRepository;
use Sulu\Bundle\WebsiteBundle\Sitemap\Sitemap;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapAlternateLink;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapProviderInterface;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapUrl;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class DefinitionSitemapProvider implements SitemapProviderInterface
{
    public function __construct(
        private readonly DefinitionTranslationRepository $translationsRepository,
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
    }

    public function build($page, $scheme, $host)
    {
        $defaultLocale = '';

        foreach ($this->webspaceManager->getAllLocalizations() as $localization) {
            if ($localization->isDefault()) {
                $defaultLocale = $localization->getLocale(Localization::DASH);
                break;
            }
        }

        $result = [];

        foreach ($this->translationsRepository->findPaginatedForSitemap((int) $page, self::PAGE_SIZE) as $translation) {
            $path = $translation->getRoute()?->getPath();

            if (null === $path) {
                continue;
            }

            $sitemapUrl = new SitemapUrl(
                loc: $this->generateUrl($scheme, $host, $path),
                locale: $translation->getLocale(),
                defaultLocale: $defaultLocale,
                lastmod: $translation->getChanged(),
            );

            foreach ($translation->getDefinition()->getTranslations() as $alternateTranslation) {
                $path = $alternateTranslation->getRoute()?->getPath();

                if (null === $path) {
                    continue;
                }

                $sitemapUrl->addAlternateLink(new SitemapAlternateLink(
                    href: $this->generateUrl($scheme, $host, $path),
                    locale: $alternateTranslation->getLocale(),
                ));
            }

            $result[] = $sitemapUrl;
        }

        return $result;
    }

    public function getAlias()
    {
        return Definition::RESOURCE_KEY;
    }

    public function createSitemap($scheme, $host)
    {
        return new Sitemap($this->getAlias(), $this->getMaxPage($scheme, $host));
    }

    public function getMaxPage($scheme, $host)
    {
        return (int) \ceil($this->translationsRepository->count([]) / self::PAGE_SIZE);
    }

    private function generateUrl(string $scheme, string $host, string $path): string
    {
        return $scheme . '://' . $host . $path;
    }
}
