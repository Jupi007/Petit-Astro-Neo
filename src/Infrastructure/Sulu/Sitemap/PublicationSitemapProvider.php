<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Sitemap;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Sitemap\ContentSitemapProvider;
use Sulu\Bundle\RouteBundle\Model\RouteInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

/** @extends ContentSitemapProvider<PublicationDimensionContent, Publication> */
class PublicationSitemapProvider extends ContentSitemapProvider
{
    /** @param class-string<RouteInterface> $suluRouteClass*/
    public function __construct(
        EntityManagerInterface $entityManager,
        WebspaceManagerInterface $webspaceManager,
        string $kernelEnvironment,
        string $suluRouteClass,
    ) {
        parent::__construct(
            $entityManager,
            $webspaceManager,
            $kernelEnvironment,
            Publication::class,
            $suluRouteClass,
            Publication::RESOURCE_KEY,
        );
    }
}
