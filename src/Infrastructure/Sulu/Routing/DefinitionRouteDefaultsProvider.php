<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Routing;

use App\Entity\Definition;
use App\Repository\DefinitionRepositoryInterface;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class DefinitionRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(
        private readonly DefinitionRepositoryInterface $repository,
    ) {
    }

    /**
     * @param string $entityClass
     * @param string $id
     * @param string $locale
     * @param Definition|null $object
     *
     * @return array{
     *   '_controller': string,
     *   'definition': null|Definition
     * }
     */
    public function getByEntity($entityClass, $id, $locale, $object = null): array
    {
        return [
            '_controller' => 'App\Controller\Website\DefinitionController::index',
            'definition' => $object ?: $this->repository->findOneLocalized($id, $locale),
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
