<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Definition;
use App\Event\Definition\CreatedDefinitionActivityEvent;
use App\Event\Definition\ModifiedDefinitionActivityEvent;
use App\Event\Definition\RemovedDefinitionActivityEvent;
use App\Repository\DefinitionRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;

/** @phpstan-type DefinitionData array{
 *      title: string|null,
 *      description: string|null,
 *      routePath: string|null,
 * } */
class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly TrashManagerInterface $trashManager,
        private readonly RouteManagerInterface $routeManager,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    /** @param DefinitionData $data */
    public function create(array $data, string $locale): Definition
    {
        $definition = new Definition();

        $this->mapDataToDefinition($definition, $data, $locale);
        $this->domainEventCollector->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        $this->generateDefinitionRoute($definition, $data);
        $this->repository->save($definition);

        return $definition;
    }

    /** @param DefinitionData $data */
    public function update(Definition $definition, array $data, string $locale): Definition
    {
        $this->mapDataToDefinition($definition, $data, $locale);
        $this->generateDefinitionRoute($definition, $data);
        $this->domainEventCollector->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->trashManager->store(Definition::RESOURCE_KEY, $definition);
        $this->domainEventCollector->collect(new RemovedDefinitionActivityEvent($definition));
        $this->removeRoutes($definition);
        $this->repository->remove($definition);
    }

    /** @param DefinitionData $data */
    private function mapDataToDefinition(Definition $definition, array $data, string $locale): void
    {
        $definition
            ->setLocale($locale)
            ->setTitle($data['title'] ?? '')
            ->setDescription($data['description'] ?? '');
    }

    /** @param DefinitionData $data */
    private function generateDefinitionRoute(Definition $definition, array $data): void
    {
        $routePath = $data['routePath'] ?? '';

        if (null === $definition->getRoute()) {
            $this->routeManager->create($definition, $routePath);
        } elseif ($definition->getRoute()->getPath() !== $routePath) {
            $this->routeManager->update($definition, $routePath);
        }
    }

    private function removeRoutes(Definition $definition): void
    {
        foreach ($definition->getLocales() as $locale) {
            $definition->setLocale($locale);

            if (null !== $route = $definition->getRoute()) {
                $this->routeRepository->remove($route);

                foreach ($route->getHistories() as $historyRoute) {
                    $this->routeRepository->remove($historyRoute);
                }
            }
        }
    }
}
