<?php

declare(strict_types=1);

namespace App\Routing;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class PublicationRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    public function __construct(
        private readonly PublicationRepository $repository,
    ) {
    }

    /**
     * @param string $entityClass
     * @param string $id
     * @param string $locale
     * @param Publication|null $object
     *
     * @return array{
     *   '_controller': string,
     *   'publication': null|Publication
     * }
     */
    public function getByEntity($entityClass, $id, $locale, $object = null): array
    {
        return [
            '_controller' => 'App\Controller\Website\PublicationController::index',
            'publication' => $object ?: $this->repository->findById((int) $id, $locale),
        ];
    }

    public function isPublished($entityClass, $id, $locale): bool
    {
        return true;
    }

    public function supports($entityClass): bool
    {
        return Publication::class === $entityClass;
    }
}
