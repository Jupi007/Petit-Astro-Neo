<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Routing;

use App\Domain\Entity\Definition;
use App\UserInterface\Controller\Website\DefinitionWebsiteController;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class DefinitionRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    /**
     * @param string $entityClass
     * @param string $id
     * @param string $locale
     * @param Definition|null $object
     *
     * @return mixed[]
     */
    public function getByEntity($entityClass, $id, $locale, $object = null): array
    {
        return [
            '_controller' => \sprintf('%s::index', DefinitionWebsiteController::class),
            'id' => (int) $id,
            'locale' => $locale,
        ];
    }

    public function isPublished($entityClass, $id, $locale): bool
    {
        return true;
    }

    public function supports($entityClass): bool
    {
        return Definition::class === $entityClass;
    }
}
