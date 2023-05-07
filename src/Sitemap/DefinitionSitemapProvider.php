<?php

declare(strict_types=1);

namespace App\Sitemap;

use App\Entity\Definition;
use App\Entity\DefinitionTranslation;
use App\Exception\NullAssertionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sulu\Bundle\WebsiteBundle\Sitemap\Sitemap;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapAlternateLink;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapProviderInterface;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapUrl;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class DefinitionSitemapProvider implements SitemapProviderInterface
{
    public function __construct(
        private readonly WebspaceManagerInterface $webspaceManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function build($page, $scheme, $host)
    {
        $defaultLocale = $this->getDefaultLocale();

        $result = [];

        foreach ($this->getGroupedTranslationsByDefinition((int) $page) as $definitionTranslations) {
            $mainTranslation = $this->getMainTranslation($definitionTranslations, $defaultLocale);
            $mainPath = $this->getTranslationRoutePath($mainTranslation);
            $lastMod = $this->getTranslationsLastMod($definitionTranslations);

            $sitemapUrl = new SitemapUrl(
                loc: $this->generateUrl($scheme, $host, $mainPath),
                locale: $mainTranslation->getLocale(),
                defaultLocale: $defaultLocale,
                lastmod: $lastMod,
            );

            foreach ($definitionTranslations as $translation) {
                $path = $this->getTranslationRoutePath($translation);

                $sitemapUrl->addAlternateLink(new SitemapAlternateLink(
                    href: $this->generateUrl($scheme, $host, $path),
                    locale: $translation->getLocale(),
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
        $count = $this->entityManager->createQueryBuilder()
            ->from(DefinitionTranslation::class, 'translation')
            ->select('COUNT(translation)')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) \ceil($count / self::PAGE_SIZE);
    }

    private function getDefaultLocale(): string
    {
        foreach ($this->webspaceManager->getAllLocalizations() as $localization) {
            if ($localization->isDefault()) {
                return $localization->getLocale(Localization::DASH);
            }
        }

        throw new \LogicException('No default locale.');
    }

    /** @return array<int, non-empty-array<string, DefinitionTranslation>> */
    private function getGroupedTranslationsByDefinition(int $page): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->from(DefinitionTranslation::class, 'translation')
            ->select('translation')
            ->innerJoin('translation.route', 'route')
            ->innerJoin('translation.definition', 'definition')
            ->orderBy('translation.id', 'asc')
            ->setFirstResult(self::PAGE_SIZE * ($page - 1))
             ->setMaxResults(self::PAGE_SIZE);

        /** @var Paginator<DefinitionTranslation> */
        $translationPaginator = new Paginator($queryBuilder);

        $results = [];

        /** @var DefinitionTranslation $translation */
        foreach ($translationPaginator as $translation) {
            $definitionId = (int) $translation->getDefinition()->getId();

            if (!\array_key_exists($definitionId, $results)) {
                $results[$definitionId] = [];
            }

            $results[$definitionId][$translation->getLocale()] = $translation;
        }

        return $results;
    }

    /** @param non-empty-array<string, DefinitionTranslation> $definitionTranslations */
    private function getMainTranslation(array $definitionTranslations, string $defaultLocale): DefinitionTranslation
    {
        if (\array_key_exists($defaultLocale, $definitionTranslations)) {
            return $definitionTranslations[$defaultLocale];
        }

        return $definitionTranslations[\array_key_first($definitionTranslations)];
    }

    /** @param non-empty-array<string, DefinitionTranslation> $definitionTranslations */
    private function getTranslationsLastMod(array $definitionTranslations): \DateTime
    {
        $lastMod = null;

        foreach ($definitionTranslations as $translation) {
            if (null === $lastMod || $lastMod < $translation->getChanged()) {
                $lastMod = $translation->getChanged();
            }
        }

        return $lastMod;
    }

    private function getTranslationRoutePath(DefinitionTranslation $translation): string
    {
        return $translation->getRoute()?->getPath() ?? throw new NullAssertionException();
    }

    private function generateUrl(string $scheme, string $host, string $path): string
    {
        return $scheme . '://' . $host . $path;
    }
}
