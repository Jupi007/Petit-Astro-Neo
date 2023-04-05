<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use App\Entity\Contract\PersistableEntityInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

trait LocalizationsGetterTrait
{
    private readonly RouteRepositoryInterface $routeRepository;
    private readonly WebspaceManagerInterface $webspaceManager;

    /** @return array<string, array{locale: string, url:string|null}> */
    protected function getLocalizationsArray(PersistableEntityInterface $entity): array
    {
        $routes = $this->routeRepository->findAllByEntity(\get_class($entity), (string) $entity->getId());

        $localizations = [];
        foreach ($routes as $route) {
            $url = $this->webspaceManager->findUrlByResourceLocator(
                $route->getPath(),
                null,
                $route->getLocale(),
            );

            $localizations[$route->getLocale()] = ['locale' => $route->getLocale(), 'url' => $url];
        }

        return $localizations;
    }
}
