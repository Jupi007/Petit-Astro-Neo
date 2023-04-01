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

    public function create(Definition $definition, string $routePath): Definition
    {
        $this->repository->save($definition, flush: true);
        $this->routeManager->create($definition, $routePath);
        $this->domainEventCollector->collect(new CreatedDefinitionActivityEvent($definition));
        $this->repository->save($definition, flush: true);

        return $definition;
    }

    public function update(Definition $definition, string $routePath): Definition
    {
        $this->routeManager->update($definition, $routePath);
        $this->domainEventCollector->collect(new ModifiedDefinitionActivityEvent($definition));
        $this->repository->save($definition, flush: true);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->trashManager->store(Definition::RESOURCE_KEY, $definition);
        $this->domainEventCollector->collect(new RemovedDefinitionActivityEvent($definition));
        $this->removeRoutes($definition);
        $this->repository->remove($definition, flush: true);
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
