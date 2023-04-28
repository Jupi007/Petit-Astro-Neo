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
use Sulu\Bundle\RouteBundle\Model\RouteInterface;

class DefinitionManager
{
    public function __construct(
        private readonly DefinitionRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly RouteManagerInterface $routeManager,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function create(Definition $definition): Definition
    {
        $this->repository->save($definition, flush: true);

        $this->createOrUpdateRoute($definition);
        $this->domainEventCollector->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition, flush: true);

        return $definition;
    }

    public function update(Definition $definition): Definition
    {
        $this->createOrUpdateRoute($definition);

        $this->domainEventCollector->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition, flush: true);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->domainEventCollector->collect(new RemovedDefinitionActivityEvent($definition));
        $this->removeRoutes($definition);
        $this->repository->remove($definition, flush: true);
    }

    private function createOrUpdateRoute(Definition $definition): void
    {
        if (null === $definition->getRoute()) {
            $this->routeManager->create($definition, $definition->getRoutePath());
        }

        $this->routeManager->update($definition, $definition->getRoutePath());
    }

    private function removeRoutes(Definition $definition): void
    {
        foreach ($definition->getLocales() as $locale) {
            $definition->setLocale($locale);

            if (($route = $definition->getRoute()) instanceof RouteInterface) {
                $this->routeRepository->remove($route);

                foreach ($route->getHistories() as $historyRoute) {
                    $this->routeRepository->remove($historyRoute);
                }
            }
        }
    }
}
